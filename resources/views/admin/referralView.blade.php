<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="/admin/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/admin/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="/admin/js/datatables-simple-demo.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Referral View</title>
    <style>
        /* 배경색과 전체 레이아웃 스타일링 */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            font-size: 28px;
            color: #555;
        }

        /* 테이블 스타일 */
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            /* border: 2px solid rgb(101, 101, 101); 테이블에 테두리 추가 */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ddd; /* 각 셀에 테두리 추가 */
        }

        th {
            background-color:rgb(10, 2, 2);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* 페이지 네비게이션 스타일 */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            text-decoration: none;
            padding: 8px 16px;
            margin: 0 5px;
            background-color:rgb(101, 101, 101);
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color:rgb(101, 101, 101);
        }

        .pagination .disabled {
            background-color: #e0e0e0;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h2>파트너별 레퍼럴 수수료 내역</h2>

    <table>
        <thead>
            <tr>
                <th>파트너명</th>
                <th>총 거래액</th>
                <th>수수료(USDT)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($referralList as $list)
            <tr>
                <td>{{ $list->getUserInfo->username }} ({{ $list->getUserInfo->realname }})</td>
                <td>{{ $list->getUserInfo->totalSellAmount }}</td>
                <td>{{ $list->referral_benefit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        {{ $referralList->links() }}
    </div>
</body>
</html>
