<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 페이지</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        /* 전체 화면 스타일 */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(24, 23, 23);
            color: #ffffff;  /* 흰색 텍스트 */
            display: flex;
            justify-content: center;
            align-items: center;  /* 수평, 수직 모두 가운데 정렬 */
            height: 100vh;  /* 화면 전체 높이 */
        }

        .login-container {
            background-color: rgba(68, 68, 68, 0.8);  /* 배경을 어두운 회색 반투명으로 설정 */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 300px;
            border: 2px solid #cccccc;  /* 테두리 추가 */
        }

        h2 {
            color: #666;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* 로고가 있는 부분의 배경을 하얀색으로 설정 */
        .logo-section {
            background-color: white;  /* 로고 아래 배경을 하얀색으로 설정 */
            padding: 20px 0;  /* 위아래 여백 설정 */
            width: 100%;
            text-align: center;
            margin-bottom: 20px;  /* 로고와 로그인 폼 사이의 간격 */
        }

        .logo {
            width: 100px;  /* 로고 크기 조정 */
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #cccccc;  /* 입력 필드 테두리 */
            border-radius: 5px;
            background-color: #555555;  /* 어두운 회색 배경 */
            color: #ffffff;
        }

        .input-group input:focus {
            border-color: #ff9900;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: rgb(12, 7, 0);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: rgb(15, 9, 1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- 로고가 있는 부분을 감싸는 div -->
        <div class="logo-section">
            <img src="https://anakip2p.com/public/pub/img/logo-basics@2x.png" alt="anaki" class="logo">
        </div>
        <form action="<?php echo e(setRoute('admin.login.submit')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="input-group">
                <label for="email">관리자 아이디</label>
                <input type="text" id="email" name="email" placeholder="이메일을 입력하세요" required>
            </div>
            <div class="input-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required>
            </div>
            <button type="submit" class="login-btn">로그인</button>
        </form>
    </div>
</body>
</html><?php /**PATH /var/www/html/resources/views/admin/loginForm.blade.php ENDPATH**/ ?>