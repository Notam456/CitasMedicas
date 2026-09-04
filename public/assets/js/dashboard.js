document.addEventListener('DOMContentLoaded', function() {
    var isDark = document.documentElement.classList.contains('dark');
    var textColor = isDark ? '#b0b0b0' : '#333';
    var gridColor = isDark ? '#2d3037' : '#dee2e6';
    var legendColor = isDark ? '#e0e0e0' : '#333';

    // Gráfico municipios
    const ctxMun = document.getElementById('municipiosChart').getContext('2d');
    new Chart(ctxMun, {
        type: 'bar',
        data: {
            labels: window.municipiosLabels,
            datasets: [{
                label: 'Pacientes atendidos',
                data: window.municipiosData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: legendColor } }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });

    // Gráfico especialidades
    const ctxEsp = document.getElementById('especialidadesChart').getContext('2d');
    new Chart(ctxEsp, {
        type: 'line',
        data: {
            labels: window.especialidadesLabels,
            datasets: [{
                label: 'Citas agendadas',
                data: window.especialidadesData,
                backgroundColor: 'rgba(0, 75, 255, 0.47)',
                borderColor: 'rgba(0, 75, 255, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: legendColor } }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
