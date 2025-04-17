<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('QrCode Scan') }}</title>
    <style>
        /* 기본 스타일 */
        body {
            margin: 0;
            padding: 0;
            background-color: #111;
            color: white;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
        }

        /* 제목 스타일 */
        h2 {
            font-size: 2em;
            color: #f5f5f5;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
        }

        /* 비디오 화면 스타일 */
        video {
            width: 90%;
            max-width: 400px;
            height: auto;
            border-radius: 12px;
            border: 3px solid #00ff00;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.5);
            margin-bottom: 0; /* 비디오와 버튼 사이 간격 없앰 */
        }

        /* 스캔된 결과 표시 스타일 */
        .result {
            margin-top: 20px;
            font-size: 1.3em;
            color: #00ff00;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        /* 버튼 스타일 */
        .button {
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px; /* 비디오와 버튼 사이 간격이 매우 적거나 없도록 설정 */
        }

        .button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .button:active {
            transform: scale(0.98);
        }

        /* 모바일에서도 비디오가 잘 보이도록 */
        @media (max-width: 600px) {
            video {
                max-width: 100%;
            }

            h2 {
                font-size: 1.5em;
            }
        }

        /* 실패 메시지 */
        .error {
            color: red;
            font-size: 1.2em;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>{{ __('QrCode Scan') }}</h2>
    <video id="qr-video"></video>
    <!-- <button class="button" onclick="closeScanner()">{{ __('Close') }}</button> -->

    <script type="module">
        import QrScanner from 'https://unpkg.com/qr-scanner/qr-scanner.min.js';

        // 페이지 로드 시 QR 코드 스캐너 초기화
        window.onload = function() {
            const videoElem = document.getElementById('qr-video');
            const resultElem = document.getElementById('scan-result');
            const errorElem = document.getElementById('error-message');

            // QR Scanner 인스턴스 생성
            const qrScanner = new QrScanner(videoElem, result => {
               
                 // QR 코드 스캔 후 부모 창에 결과 전달
                if (window.opener && window.opener.setScanResult) {
                    window.opener.setScanResult(result);  // 부모 창의 setScanResult() 호출
                    qrScanner.stop();  // 카메라 중지
                    window.close();    // 새 창 닫기
                } else {
                    console.error("부모 창에서 setScanResult 함수가 정의되지 않았거나, window.opener가 null입니다.");
                }
            });

            // 카메라 시작
            qrScanner.start().catch(err => {
                console.error("QR 코드 리더기 오류:", err);
                resultElem.textContent = "{{ __('An error occurred. Please try again.')  }}";
                errorElem.textContent = "{{ __('Permission not found.') }}";
            });
        };

        // // 새 창 닫기 버튼
        // function closeScanner() {
        //     // 부모 창에서 새 창을 닫는 방식으로 수정
        //     if (window.opener) {
        //         window.opener.close();  // 부모 창 닫기
        //     } else {
        //         window.close();  // 직접 열린 창에서 닫기
        //     }
        // }
    </script>
</body>
</html>