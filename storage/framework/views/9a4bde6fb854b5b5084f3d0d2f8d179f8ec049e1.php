<?php $__env->startSection("title"); ?>
    Dash Board
<?php $__env->stopSection(); ?>

<?php $__env->startSection('style'); ?>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 1800px; /* 최대 너비를 1400px에서 1800px로 증가 */
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        .chart {
            width: 70%; /* 너비를 60%에서 70%로 증가 */
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 0 15px;
        }
        canvas {
            width: 100% !important;
            min-height: 500px !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="chart-container">
        <div class="chart">
            <canvas id="graph1"></canvas>
        </div>
        <div class="chart">
            <canvas id="graph2"></canvas>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 서버에서 전달된 데이터를 JavaScript 변수로 변환
        var usdtData = <?php echo json_encode($usdtData, 15, 512) ?>;
        var trxData = <?php echo json_encode($trxData, 15, 512) ?>;

        // 동적 날짜 라벨 생성
        var labels = usdtData.map(function(item) {
            return item.date;
        });

        // USDT 값 추출
        var usdtValues = usdtData.map(function(item) {
            return item.value;
        });

        // TRX 값 추출
        var trxValues = trxData.map(function(item) {
            return item.value;
        });

        // 공통 차트 옵션
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.2)',
                    },
                    ticks: {
                        font: {
                            size: 12,
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 12,
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 14,
                        },
                        padding: 20,
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 12 },
                    padding: 10,
                }
            }
        };

        // 첫 번째 꺾은선 그래프 (USDT)
        var ctx1 = document.getElementById('graph1').getContext('2d');
        var graph1 = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'USDT Amount',
                    data: usdtValues,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: chartOptions
        });

        // 두 번째 꺾은선 그래프 (TRX)
        var ctx2 = document.getElementById('graph2').getContext('2d');
        var graph2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'TRX Amount',
                    data: trxValues,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: chartOptions
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>