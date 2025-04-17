<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <style>

        @font-face {
            font-family: 'NanumGothic';
            src: url("<?php echo e(storage_path('fonts/NanumGothic.ttf')); ?>") format('truetype');
        }

        body {
            font-family: 'NanumGothic', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            width: 150px;
        }

        /* ✅ 헤더 스타일 (정렬 및 간격 추가) */
        .header {
            background: #f5f5f5;
            padding: 20px; /* ✅ 간격 추가 */
            border-radius: 10px;
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            gap: 10px; /* ✅ 행 간격 추가 */
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .header-left {
            text-align: left;
            flex: 1;
        }

        .header-right {
            text-align: right;
            flex: 1;
        }

        .status-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 5px; /* ✅ Status와 Successful 간 간격 추가 */
        }

        .status {
            color: green;
            font-weight: bold;
        }

        .amount {
            font-size: 18px;
            font-weight: bold;
        }

        /* ✅ 테이블 스타일 (테두리 추가) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* ✅ 반응형 설정 */
        @media (max-width: 600px) {
            .header-row {
                flex-direction: row;
                justify-content: space-between;
            }

            .info-table, th, td {
                display: block;
                width: 100%;
                text-align: left;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <!-- ✅ 로고 -->
    <div class="logo">
        <img src="<?php echo e($logoImageSrc); ?>" alt="anaki">
    </div>

    <!-- ✅ 헤더 (간격 추가 및 정렬 유지) -->
    <div class="header">
        <div class="header-row">
            <span class="header-left">Domestic Wire Transfer</span>       
           
            <span class="header-right" style='margin-left:40%'>Status</span>
        </div>
        <div class="header-row">
            <span class="header-left amount"><?php echo e(number_format($item->totalPayAmount)); ?> KRW (<?php echo e($item->tetherAmount); ?> USDT)</span>
           
            <span class="header-right status" style='margin-left:27%'>Successful</span>
        </div>
    </div>

    <!-- ✅ 거래 정보 테이블 (테두리 적용) -->
    <table class="info-table">
        <tr>
            <th>Trade Type</th>
            <th>Rate</th>
        </tr>
        <tr>
            <td class="highlight"><?php echo e(($tradingDirection === 'buy')? 'Buy Tether': 'Sell Tether'); ?></td>
            <td class="highlight"><?php echo e(number_format($item->price)); ?> KRW</td>
        </tr>

        <tr>
            <th>Amount fiat</th>
            <th>Amount crypto</th>
        </tr>
        <tr>
            <td class="highlight"><?php echo e(number_format($item->totalPayAmount)); ?> KRW</td>
            <td class="highlight"><?php echo e($item->tetherAmount); ?> USDT</td>
        </tr>

        <tr>
            <th>Market rate USD</th>
            <th>Fee crypto</th>
        </tr>
        <tr>
            <td class="highlight"><?php echo e($marketRate); ?></td>
            <td class="highlight"><?php echo e($fee); ?> USDT</td>
        </tr>

        <tr>
            <th>Buyer Name</th>
            <th>Seller Name</th>
        </tr>
        <tr>
            <td class="highlight"><?php echo e($buyerName); ?></td>
            <td class="highlight"><?php echo e($sellerName); ?></td>
        </tr>

        <tr>
           <th>Payment method</th>
           <th>Completed At</th>
       </tr>
        <tr>
            <td class="highlight">Domestic Wire Transfer</td>
            <td class="highlight"><?php echo e($endedAt); ?></td>
        </tr>

    </table>
</div>

</body>
</html>
<?php /**PATH /var/www/html/resources/views/user/my-trade/pdf.blade.php ENDPATH**/ ?>