document.addEventListener('DOMContentLoaded', function() {
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
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
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
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});