<?php $__env->startSection("title"); ?>
    거래 건수 통계
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
            max-width: 2500px;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        .chart {
            width: 100%;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 0 15px;
        }
        canvas {
            width: 100% !important;
            min-height: 500px !important;
            min-height: 700px !important;
        }

        .date-filter {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .date-filter input {
            padding: 8px;
            font-size: 16px;
            margin: 0 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form id='submitForm'>
    <div class="date-filter">
        <input type="date" id="startDate" name='start' value="<?php echo e($date['start']); ?>" placeholder="시작 날짜">
        <input type="date" id="endDate" name='end' value="<?php echo e($date['end']); ?>" placeholder="끝 날짜">
        <button onclick="updateChartData()">검색</button>
    </div>
    </form>
    <div class="chart-container">
        <div class="chart">
            <canvas id="graph1"></canvas>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 서버에서 전달된 데이터를 JavaScript 변수로 변환
        var tradeCountData = <?php echo json_encode($tradeCountData, 15, 512) ?>;

        // 'trade_count', 'completed_trade_count', 'dipute_trade_count','dipute_solved_trade_count','cancel_trade_count'
        // 동적 날짜 라벨 생성
        var labels = tradeCountData['trade_count'].map(function(item) {
            return item.date;
        });

        // 각 항목의 값 추출
        var tradeCountValues = tradeCountData['trade_count'].map(function(item) {
            return item.value;
        });

        var completedTradeCoutValues = tradeCountData['completed_trade_count'].map(function(item) {
            return item.value;
        });

        var diputeTradeCountValues = tradeCountData['dipute_trade_count'].map(function(item) {
            return item.value;
        });

        var diputeSolvedTradeCountValues = tradeCountData['dipute_solved_trade_count'].map(function(item) {
            return item.value;
        });

        var cancelTradeCountValues = tradeCountData['cancel_trade_count'].map(function(item) {
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
                            size: 16,
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 16,
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 18,
                        },
                        padding: 25,
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 16 },
                    bodyFont: { size: 18 },
                    padding: 10,
                }
            }
        };

        var ctx1 = document.getElementById('graph1').getContext('2d');
        var graph1 = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '전체 거래',
                        data: tradeCountValues,
                        borderColor: 'rgb(39, 152, 60)',
                        backgroundColor: 'rgba(45, 146, 46, 0.2)',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(88, 186, 108)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: '완료된 거래',
                        data: completedTradeCoutValues,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: '분쟁 거래',
                        data: diputeTradeCountValues,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(255, 99, 132)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: '분쟁 해결된 거래',
                        data: diputeSolvedTradeCountValues,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: '취소된 거래',
                        data: cancelTradeCountValues,
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: 'rgb(153, 102, 255)',
                        tension: 0.1,
                        fill: true,
                    }
                ]
            },
            options: chartOptions
        });

        function updateChartData() {
            const currentDate = <?php echo e($now); ?>;
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();

            if (!startDate || !endDate) {
                alert('검색 날짜를 입력해주세요 ');
                return false;
            } else if (startDate > currentDate) {
                alert("시작 날짜는 현재 날짜보다 클 수 없습니다.");
                $('#startDate').val('');
                return false;
            } else if (endDate > currentDate) {
                alert("끝 날짜는 현재 날짜보다 클 수 없습니다.");
                $('#endDate').val('');
            } else {
                const start = new Date(startDate);
                const end = new Date(endDate);

                const diffTime = end - start;
                const diffMonths = diffTime / (1000 * 3600 * 24 * 30);
                if (diffMonths > 6) {
                    alert("검색 기간은 6개월을 넘을 수 없습니다.");
                    $('#startDate').val('');
                    $('#endDate').val('');
                    return false;
                }
            }

            $('#submitForm').submit();
        }

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dataStatsTradeCount.blade.php ENDPATH**/ ?>