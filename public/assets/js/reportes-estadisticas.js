document.addEventListener('DOMContentLoaded', function () {
    var loading = document.getElementById('estadistica-loading');
    var error = document.getElementById('estadistica-error');
    var contenido = document.getElementById('estadistica-contenido');
    var tablasContainer = document.getElementById('estadistica-tablas');
    var chartsContainer = document.getElementById('estadistica-charts');

    var isDark = document.documentElement.classList.contains('dark');

    fetch(window.routeEstadisticas)
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (bloques) {
            loading.classList.add('d-none');
            contenido.classList.remove('d-none');

            bloques.forEach(function (bloque) {
                if (bloque.chartType === 'table') {
                    renderTabla(bloque, tablasContainer);
                } else {
                    renderChart(bloque, chartsContainer);
                }
            });
        })
        .catch(function () {
            loading.classList.add('d-none');
            error.classList.remove('d-none');
        });

    function renderTabla(bloque, container) {
        var top5 = (bloque.meta && bloque.meta.top5) ? bloque.meta.top5 : [];
        var bottom5 = (bloque.meta && bloque.meta.bottom5) ? bloque.meta.bottom5 : [];

        var html = '<div class="col-12">';
        html += '<div class="bg-light rounded p-3">';
        html += '<h6 class="mb-3">' + escapeHtml(bloque.title) + '</h6>';
        html += '<p class="text-muted small mb-3">' + escapeHtml(bloque.description) + '</p>';

        if (top5.length > 0) {
            html += '<h6 class="text-success mb-2">Primeros 5 - Mayor Tasa de Atención</h6>';
            html += buildTable(top5, '#198754', '#d1e7dd');
        }

        if (bottom5.length > 0) {
            html += '<h6 class="text-danger mt-3 mb-2">Ultimos 5 - Menor Tasa de Atención</h6>';
            html += '<small class="text-muted d-block mb-2">Requieren revisión de agenda o capacitación.</small>';
            html += buildTable(bottom5, '#dc3545', '#f8d7da');
        }

        html += '</div></div>';
        container.innerHTML += html;
    }

    function buildTable(data, headerBgLight, headerBgDark) {
        var hdrBg = isDark ? headerBgDark : headerBgLight;
        var hdrFg = isDark ? '#63666A' : '#000';

        var html = '<div class="table-responsive">';
        html += '<table class="table table-sm table-bordered align-middle mb-0">';
        html += '<thead><tr style="background:' + hdrBg + ';color:' + hdrFg + '">';
        html += '<th>#</th><th>Médico</th><th>Especialidad</th><th>Citas</th><th>Atendidas</th><th>Tasa Atención</th>';
        html += '</tr></thead><tbody>';

        data.forEach(function (m, i) {
            var tasa = m.tasa_atencion;
            var badge = tasa >= 80 ? 'bg-success' : (tasa >= 60 ? 'bg-warning text-dark' : 'bg-danger');
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td>' + escapeHtml(m.medico) + '</td>';
            html += '<td>' + escapeHtml(m.especialidad) + '</td>';
            html += '<td class="text-center">' + m.total_citas + '</td>';
            html += '<td class="text-center">' + m.atendidas + '</td>';
            html += '<td class="text-center"><span class="badge ' + badge + '">' + tasa + '%</span></td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        return html;
    }

    function renderChart(bloque, container) {
        var div = document.createElement('div');
        div.className = 'col-md-6';

        var card = document.createElement('div');
        card.className = 'bg-light rounded p-3';

        var title = document.createElement('h6');
        title.className = 'mb-2';
        title.textContent = bloque.title;
        card.appendChild(title);

        var desc = document.createElement('small');
        desc.className = 'text-muted d-block mb-3';
        desc.textContent = bloque.description;
        card.appendChild(desc);

        var canvas = document.createElement('canvas');
        canvas.style.maxHeight = '300px';
        card.appendChild(canvas);

        div.appendChild(card);
        container.appendChild(div);

        var ctx = canvas.getContext('2d');
        var config = buildChartConfig(bloque);
        new Chart(ctx, config);
    }

    function buildChartConfig(bloque) {
        var type = bloque.chartType;
        var tickColor = isDark ? '#ccc' : '#1a1a1a';
        var gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

        var datasets = bloque.datasets.map(function (ds) {
            var base = {
                label: ds.label,
                data: ds.data,
                borderWidth: 2,
            };
            if (ds.backgroundColor) base.backgroundColor = ds.backgroundColor;
            if (ds.borderColor) base.borderColor = ds.borderColor;
            if (ds.fill !== undefined) base.fill = ds.fill;
            if (ds.tension !== undefined) base.tension = ds.tension;
            return base;
        });

        var config = {
            type: type,
            data: {
                labels: bloque.labels,
                datasets: datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: bloque.datasets.length > 1 || type === 'doughnut',
                        position: 'bottom',
                        labels: { color: tickColor, font: { weight: '500' } },
                    },
                },
            },
        };

        if (type === 'bar') {
            config.options.barPercentage = 0.7;
            config.options.categoryPercentage = 0.8;
            config.options.scales = {
                x: {
                    beginAtZero: true,
                    ticks: { color: tickColor, font: { weight: '500' }, maxRotation: 45 },
                    grid: { color: gridColor },
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: tickColor, font: { weight: '500' } },
                    grid: { color: gridColor },
                },
            };
        }

        if (type === 'line') {
            config.options.elements = { point: { radius: 4, hoverRadius: 6 } };
            config.options.scales = {
                x: {
                    ticks: { color: tickColor, font: { weight: '500' } },
                    grid: { color: gridColor },
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: tickColor,
                        font: { weight: '500' },
                        callback: function (value) { return value + '%'; },
                    },
                    grid: { color: gridColor },
                },
            };
        }

        return config;
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
});
