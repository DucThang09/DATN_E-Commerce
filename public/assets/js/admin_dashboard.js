document.addEventListener('DOMContentLoaded', function () {
    const userCanvas   = document.getElementById('userChart');
    const rangeSelect  = document.getElementById('userRangeSelect');
    const summaryEl    = document.getElementById('userSummary');

    if (!userCanvas || !rangeSelect) return;

    let userChart;

    async function loadUserStats(range) {
        try {
            const resp = await fetch(`/admin/user-stats?range=${encodeURIComponent(range)}`);
            const json = await resp.json();

            if (resp.status !== 200) {
                console.error('User stats error:', json.error || json);
                return;
            }

            const labels    = json.labels || [];
            const data      = json.data || [];
            const totalNew  = json.total_new || 0;
            const rangeText = json.range_label || '';

            if (userChart) userChart.destroy();

            userChart = new Chart(userCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tổng người dùng',
                        data: data,
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34,197,94,0.12)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.35,      // bo cong đường
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Text dưới biểu đồ: "Tổng 7 ngày gần nhất: 5 người dùng mới"
            if (summaryEl) {
                summaryEl.innerHTML =
                    `Tổng <strong>${rangeText}</strong>: <strong>${totalNew}</strong> người dùng mới`;
            }

        } catch (err) {
            console.error('User stats fetch failed:', err);
        }
    }

    rangeSelect.addEventListener('change', function () {
        loadUserStats(this.value);
    });

    // load lần đầu
    loadUserStats(rangeSelect.value || '7d');
});
document.addEventListener('DOMContentLoaded', function () {
    const refreshBtn = document.getElementById('btnDashboardRefresh');
    const lastUpdated = document.getElementById('dashboardLastUpdated');

    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            window.location.reload();
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('dashboardLastUpdated');
    if (!el) return;

    const now = new Date();              // giờ hiện tại
    const pad = n => String(n).padStart(2, '0');

    const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    const dateStr = `${pad(now.getDate())}/${pad(now.getMonth() + 1)}/${now.getFullYear()}`;

    el.textContent = `${timeStr} ${dateStr}`;
});

