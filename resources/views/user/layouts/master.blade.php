<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">

<head>
    <!-- 유저 페이지 레이아웃 -->
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    
    <!--Seo -->
    <title>Anaki global Peer-to-Peer Tether Marketplace</title>
    <meta name="description" content="ANAKI is a secure P2P platform for fast and safe Tether (USDT) transactions." />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Anaki global P2P Tether Marketplace" /> 
    <meta property="og:description" content="ANAKI is a secure P2P platform for fast and safe Tether (USDT) transactions."/>
    <meta property="og:url" content="https://anakip2p.com" />
    <meta property="og:site_name" content="Anaki global P2P Tether Marketplace" />
    <meta property="og:image" content="https://anakip2p.com/public/pub/img/logo-basics@2x.png" />
    <link rel="canonical" href="https://anakip2p.com" />
    <!--end Seo -->
    <link rel="apple-touch-icon" sizes="180x180" href="/backend/images/icon/favicon.png?v={{ time(); }}">
    <link rel="icon" href="/backend/images/icon/favicon.png?v={{ time(); }}" type="image/png">
    <link rel="shortcut icon" href="/backend/images/icon/favicon.png?v={{ time(); }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($page_title) ? __($page_title) : __('Dashboard') }}</title>
   
    @vite('resources/js/app.js')

    <script src="{{ asset('pub/js/jquery-3.5.1.min.js') }}"></script>

    @stack('css')
    
    @yield('style')
</head>

<body class="{{ Session::get('side-menu', '') }}">
    <div class="wrap">
        <header class="header" id="side-menu">
            @include('user.partials.side-nav')
        </header>

        <div class="container">
            @include('user.partials.top-nav')

            <div class="content-wrap">
                @yield('content')
            </div>
        </div>

    </div>

    @include('partials.footer-asset')
    @include('user.partials.notify')

    @include('user.layouts.chat')

    @stack('script')
    
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        function laravelCsrf() {
            return $("head meta[name=csrf-token]").attr("content");
        }

        function openAlertModal(URL, target, message, actionBtnText = "Remove", httpMethod = "DELETE") {
            if (URL == "" || target == "") {
                return false;
            }

            if (message == "") {
                message = "Are you sure you want to proceed?";
            }

            if (confirm(message)) {
                // 사용자가 확인을 눌렀을 때만 실행
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = URL;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = laravelCsrf(); // Laravel CSRF 토큰 함수 호출
                form.appendChild(csrfToken);

                if (httpMethod !== 'POST') {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = httpMethod;
                    form.appendChild(methodField);
                }

                const targetField = document.createElement('input');
                targetField.type = 'hidden';
                targetField.name = 'target';
                targetField.value = target;
                form.appendChild(targetField);

                const typeField = document.createElement('input');
                typeField.type = 'hidden';
                typeField.name = 'type';
                typeField.value = actionBtnText;
                form.appendChild(typeField);

                document.body.appendChild(form);
                form.submit();
            }
        }
     
        window.onload = function () {
            var primaryKey = "{{ $basic_settings->broadcast_config->primary_key ?? '' }}";
            var cluster = "{{ $basic_settings->broadcast_config->cluster ?? '' }}";
            window.pusherInstance.connect(currentUserId, primaryKey, cluster);
            // 채팅창에서 상대방이 채팅 입력 했을 경우 
            window.pusherInstance.bindEvent('new.message', (data) => {
                const message = data.message.message;
                const senderName = data.senderName; // 서버에서 발신자 이름을 전송한다고 가정
                const chatId = data.message.chat_id;
                // console.log(JSON.stringify(data));
                // 채팅창이 안 열려 있을때 -> 노티만
                if (!$('.chat-layer').is(':visible')) {
                    @php
                        $text = __('메세지 수신');     
                    @endphp
                    let text = @json($text);
                    alertMessage = text.replace("nickname", senderName);
                    showNotification(alertMessage, 'success');   
                }else{ // 채팅창이 열려 있을때 -> 바로 읽음 처리 
                    messageRead(chatId)
                }
            });
        };
        
        // 메시지 읽음처리
        function messageRead(chatId) {
            fetch('/user/messages/messageRead', {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': laravelCsrf(),  
                },
                body: JSON.stringify({
                    chatId: chatId  
                })
            })
            .then(result => {  
                // console.log('서버 응답:', result);  
                console.log('메시지 처리 완료');
            })
            .catch(error => console.error('요청 실패:', error));  
        }

    
    </script>

</body>

<style>
    .no-transition,
    .no-transition * {
        transition: none !important;
    }

    .new-badge {
        color: red; /* NEW 텍스트의 색상 */
        font-weight: bold; /* NEW 텍스트를 굵게 */
        font-size: 14px; /* NEW 텍스트 크기 */
        margin-left: 5px; /* NEW와 Chat 사이의 간격 */
        background-color: yellow; /* NEW 텍스트 배경색 */
        padding: 2px 5px; /* NEW 텍스트 주변 여백 */
        border-radius: 5px; /* NEW 텍스트의 테두리 둥글게 */
    }

</style>

</html>
