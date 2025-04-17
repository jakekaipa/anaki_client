<!-- resources/views/user/layouts/chat.blade.php -->

@php
    $currentUserId = auth()->id();
    if (isset($item) && is_object($item)) {
        // if ($currentUserId == $item->seller_user_id) {
        //     $receiverId = $item->buyer_user_id;
        // } else {
        //     $receiverId = $item->seller_user_id;
        // }
        $receiverId = $item->user_id;
    } else {
        $receiverId = null;
    }

    $key = config('app.key');

    // JavaScript에 전달할 암호화 함수를 생성
    $encryptionFunction =
        "function encryptId(id) {
        return '" .
        encrypt("' + id + '", '$key') .
        "';
    }";

    $supportTicketTokens = Session::get('support_ticket_tokens', []);

    //$default = get_default_language_code();
    
@endphp

<div id="chat-list" class="chat-list">
    <div class="chat-list-header">
        <h5>{{ __('Chat list') }}</h5>
        <button id="close-chat-list" class="btn-close"></button>
    </div>
    <div id="chat-list-content">
        <!-- 채팅 목록이 여기에 추가됩니다 -->
    </div>
    <div class="resize-handle"></div>
</div>

<!-- chat-layer -->
<div id="chat-popup" class="chat-layer" style="display: none;">
    <div class="cl-inner">
        <div class="cl-container">
            <div class="cl-header">
                <span id="chat-title" class="cl-name">James</span>
            </div>

            <div class="cl-cont">
                <div id="chat-body" class="cl-chat-box">
                    <div class="cl-noti">
                        <dl>
                            <dt>NOTICE</dt>
                            <dd>
                                {{ __('chat notice1') }}
                                {{ __('chat notice2') }}
                            </dd>
                        </dl>
                        <dl>
                            <dt>NOTICE</dt>
                            <dd> {{ __('chat notice3') }}</dd>
                            <dd> {{ __('chat notice4') }}</dd>
                            <dd> {{ __('chat notice5') }}</dd>
                            <dd> {{ __('chat notice6') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="chat-text-area">
                    <form id="chat-form">
                        @csrf
                        <label id="attach-file" for="file-input" class="add-photo" title="사진 첨부">
                            <img src="{{ asset('/public/pub') }}/img/chat-add-photo.png" alt="사진 첨부"
                                style="width: 22px;">
                        </label>

                        <input type="file" id="file-input" style="display: none;" accept="image/*,application/pdf">
                        <input type="text" id="message-input" class="chat-cont"
                            placeholder="{{ __('Enter message.') }}">

                        <button type="submit" class="send-msg" title="메세지 보내기">
                            <img src="{{ asset('/public/pub') }}/img/chat-send@2x.png">
                        </button>
                    </form>
                </div>
            </div>

            <button id="close-chat" class="btn-cl-close" title="채팅창 닫기"><img
                    src="{{ asset('/public/pub') }}/img/close-12@2x.png"></button>
        </div>
    </div>
</div>
<!-- chat-layer -->

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    <?php echo $encryptionFunction; ?>
    const supportTicketTokens = <?php echo json_encode($supportTicketTokens); ?>;

    let currentUserId = null;

    class ChatManager {
        constructor() {
            this.chats = new Map();
            this.currentChatId = null;
            this.unreadCounts = new Map();
            this.receiverNames = new Map(); // 새로운 맵을 추가하여 수신자 이름을 저장
        }

        genChatId(order_id, sender_id, receiver_id) {
            // sender_id와 receiver_id 중 작은 값을 first, 큰 값을 second로 설정
            const [first, second] = [sender_id, receiver_id].sort((a, b) => a - b);
            return `${order_id}-${first}-${second}`;
        }

        createChat(chatId, receiverId, receiverName) {
            if (!this.chats.has(chatId)) {
                this.chats.set(chatId, {
                    receiverId,
                    messages: []
                });
                this.receiverNames.set(receiverId, receiverName); // 수신자 이름 저장
            }
            this.currentChatId = chatId;
        }

        getReceiverName(receiverId) {
            return this.receiverNames.get(receiverId) || `User ${receiverId}`;
        }

        updateReceiverName(receiverId, receiverName) {
            this.receiverNames.set(receiverId, receiverName);
        }

        switchChat(chatId) {
            if (this.chats.has(chatId)) {
                this.currentChatId = chatId;
                return true;
            }
            return false;
        }

        addMessage(chatId, message, senderName) {
            if (!this.chats.has(chatId)) {
                this.createChat(chatId, message.sender_id, senderName);
            }
            this.chats.get(chatId).messages.push(message);
        }

        getChat(chatId) {
            return this.chats.get(chatId);
        }

        getCurrentChatId() {
            return this.currentChatId;
        }

        getAllChatIds() {
            return Array.from(this.chats.keys());
        }

        incrementUnreadCount(chatId) {
            const count = this.unreadCounts.get(chatId) || 0;
            this.unreadCounts.set(chatId, count + 1);
        }

        getUnreadCount(chatId) {
            return this.unreadCounts.get(chatId) || 0;
        }

        markAsUnread(chatId) {
            const count = this.unreadCounts.get(chatId) || 0;
            this.unreadCounts.set(chatId, count + 1);
        }

        markAsRead(chatId) {
            this.unreadCounts.delete(chatId);
        }

        isUnread(chatId) {
            return this.unreadCounts.has(chatId);
        }

        saveChatList() {
            const chatData = Array.from(this.chats.entries()).map(([chatId, chatInfo]) => ({
                chatId,
                receiverId: chatInfo.receiverId,
                receiverName: this.getReceiverName(chatInfo.receiverId),
                unreadCount: this.getUnreadCount(chatId)
            }));
            localStorage.setItem('chatList', JSON.stringify(chatData));
        }

        loadChatList() {
            const savedChatList = localStorage.getItem('chatList');
            if (savedChatList) {
                const chatData = JSON.parse(savedChatList);
                chatData.forEach(chat => {
                    this.createChat(chat.chatId, chat.receiverId, chat.receiverName);
                    if (chat.unreadCount > 0) {
                        this.unreadCounts.set(chat.chatId, chat.unreadCount);
                    }
                });
            }
        }

        clearChatList() {
            this.chats.clear();
            this.unreadCounts.clear();
            this.currentChatId = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const chatManager = new ChatManager();
        const chatTitle = document.getElementById('chat-title');
        const chatBody = document.getElementById('chat-body');
        const messageInput = document.getElementById('message-input');
        const closeButton = document.getElementById('close-chat');
        const chatPopup = document.getElementById('chat-popup');
        const chatList = document.getElementById('chat-list');
        // const chatPopupResizeHandle = chatPopup.querySelector('.resize-handle');
        // const chatListResizeHandle = chatList.querySelector('.resize-handle');

        const notificationIcon = document.getElementById('notification-icon');
        const pushList = document.querySelector('.push-list');

        // function initResize(element, handle, storageKey) {
        //     let isResizing = false;
        //     let lastWidth, lastHeight, lastX, lastY;

        //     // 저장된 크기 불러오기 및 적용
        //     const savedSize = localStorage.getItem(storageKey);
        //     if (savedSize) {
        //         const {
        //             width,
        //             height
        //         } = JSON.parse(savedSize);
        //         element.style.width = width;
        //         element.style.height = height;
        //     }

        //     handle.addEventListener('mousedown', startResize, false);
        //     handle.addEventListener('touchstart', startResize, false);

        //     function startResize(e) {
        //         e.preventDefault();

        //         isResizing = true;
        //         lastWidth = element.offsetWidth;
        //         lastHeight = element.offsetHeight;
        //         lastX = e.clientX || e.touches[0].clientX;
        //         lastY = e.clientY || e.touches[0].clientY;
        //         document.addEventListener('mousemove', resize);
        //         document.addEventListener('touchmove', resize);
        //         document.addEventListener('mouseup', stopResize);
        //         document.addEventListener('touchend', stopResize);
        //     }

        //     function resize(e) {

        //         if (!isResizing) return;

        //         const clientX = e.clientX || e.touches[0].clientX;
        //         const clientY = e.clientY || e.touches[0].clientY;

        //         const deltaX = clientX - lastX;
        //         const deltaY = clientY - lastY;

        //         const newWidth = Math.max(200, lastWidth + deltaX); // 최소 너비 200px
        //         const newHeight = Math.max(200, lastHeight + deltaY); // 최소 높이 200px

        //         element.style.width = newWidth + 'px';
        //         element.style.height = newHeight + 'px';

        //         // 크기 변경 후 스크롤 조정
        //         if (element === chatPopup) {
        //             const chatBody = document.getElementById('chat-body');
        //             chatBody.scrollTop = chatBody.scrollHeight;
        //         } else if (element === chatList) {
        //             const chatListContent = document.getElementById('chat-list-content');
        //             chatListContent.scrollTop = chatListContent.scrollHeight;
        //         }


        //         lastX = clientX;
        //         lastY = clientY;
        //     }

        //     function stopResize() {

        //         if (isResizing) {
        //             isResizing = false;
        //             window.removeEventListener('mousemove', resize, false);
        //             window.removeEventListener('touchmove', resize, false);

        //             // 크기 저장
        //             const size = {
        //                 width: element.style.width,
        //                 height: element.style.height
        //             };
        //             localStorage.setItem(storageKey, JSON.stringify(size));
        //         }
        //     }
        // }

        // function makeDraggable(element, positionStorageKey, sizeStorageKey) {
        //     let pos1 = 0,
        //         pos2 = 0,
        //         pos3 = 0,
        //         pos4 = 0;
        //     let isDragging = false;
        //     const header = element.querySelector('.chat-header') || element.querySelector('.chat-list-header');

        //     // 저장된 위치 불러오기
        //     const savedPosition = localStorage.getItem(positionStorageKey);
        //     if (savedPosition) {
        //         const {
        //             top,
        //             left
        //         } = JSON.parse(savedPosition);
        //         element.style.top = top;
        //         element.style.left = left;
        //     }

        //     if (header) {
        //         header.addEventListener('mousedown', dragStart);
        //         header.addEventListener('touchstart', dragStart);
        //     } else {
        //         element.addEventListener('mousedown', dragStart);
        //         element.addEventListener('touchstart', dragStart);
        //     }

        //     function dragStart(e) {
        //         e = e || window.event;
        //         e.preventDefault();
        //         isDragging = true;

        //         if (e.type === 'touchstart') {
        //             pos3 = e.touches[0].clientX;
        //             pos4 = e.touches[0].clientY;
        //         } else {
        //             pos3 = e.clientX;
        //             pos4 = e.clientY;
        //         }

        //         document.addEventListener('mouseup', dragEnd);
        //         document.addEventListener('touchend', dragEnd);
        //         document.addEventListener('mousemove', elementDrag);
        //         document.addEventListener('touchmove', elementDrag);
        //     }

        //     function elementDrag(e) {
        //         if (!isDragging) return;
        //         e = e || window.event;
        //         e.preventDefault();

        //         if (e.type === 'touchmove') {
        //             pos1 = pos3 - e.touches[0].clientX;
        //             pos2 = pos4 - e.touches[0].clientY;
        //             pos3 = e.touches[0].clientX;
        //             pos4 = e.touches[0].clientY;
        //         } else {
        //             pos1 = pos3 - e.clientX;
        //             pos2 = pos4 - e.clientY;
        //             pos3 = e.clientX;
        //             pos4 = e.clientY;
        //         }

        //         element.style.top = (element.offsetTop - pos2) + "px";
        //         element.style.left = (element.offsetLeft - pos1) + "px";
        //     }

        //     function dragEnd() {
        //         isDragging = false;
        //         document.removeEventListener('mouseup', dragEnd);
        //         document.removeEventListener('touchend', dragEnd);
        //         document.removeEventListener('mousemove', elementDrag);
        //         document.removeEventListener('touchmove', elementDrag);

        //         // 위치 저장
        //         const position = {
        //             top: element.style.top,
        //             left: element.style.left
        //         };
        //         localStorage.setItem(positionStorageKey, JSON.stringify(position));

        //         // 크기 저장
        //         const size = {
        //             width: element.style.width,
        //             height: element.style.height
        //         };
        //         localStorage.setItem(sizeStorageKey, JSON.stringify(size));
        //     }
        // }

        // initResize(chatPopup, chatPopupResizeHandle, 'chatPopupSize');
        // initResize(chatList, chatListResizeHandle, 'chatListSize');

        // makeDraggable(chatPopup, 'chatPopupPosition', 'chatPopupSize');
        // makeDraggable(chatList, 'chatListPosition', 'chatListSize');

        // let receiverId = null;
        let clientId = null;

        @if (isset($currentUserId))
            currentUserId = {{ $currentUserId }};
        @endif
        @if (isset($receiverId))
            window.receiverId = {{ $receiverId }};
        @endif

        const chatListContent = document.getElementById('chat-list-content');
        const closeChatListButton = document.getElementById('close-chat-list');

        function closeChatListHandler(e) {
            e.preventDefault();
            e.stopPropagation();
            chatList.style.display = 'none';
            chatManager.clearChatList();
            localStorage.removeItem('chatList');
            chatPopup.style.display = 'none';
        }

        closeChatListButton.addEventListener('click', closeChatListHandler);
        closeChatListButton.addEventListener('touchend', function(e) {
            e.preventDefault();
            closeChatListHandler(e);
        });

        var primaryKey = "{{ $basic_settings->broadcast_config->primary_key ?? '' }}";
        var cluster = "{{ $basic_settings->broadcast_config->cluster ?? '' }}";

        // Pusher 설정
        if (!window.PusherSingleton.isConnected()) {
            window.pusherInstance.connect(currentUserId, primaryKey, cluster);
        } else {
            console.log('Pusher already connected');
        }

        const isChatVisible = () => {
            return chatPopup.style.display != 'none';
        }

        function bindPusherEvents() {
            window.pusherInstance.bindEvent('pusher:subscription_succeeded', () => {
                console.log('Successfully subscribed to private channel: private-user.' +
                    currentUserId);
            });

            window.pusherInstance.bindEvent('pusher:subscription_error', (error) => {
                console.error('Pusher subscription error:', error);
            });

            chatManager.loadChatList();
            updateChatList();

            window.pusherInstance.bindEvent('new.message', (data) => {
                const message = data.message;
                const senderName = data.senderName; // 서버에서 발신자 이름을 전송한다고 가정
                const chatId = message.chat_id;

                // 발신자 이름 업데이트
                chatManager.updateReceiverName(message.sender_id, senderName);

                chatManager.addMessage(chatId, message, senderName);

                if (chatId !== chatManager.getCurrentChatId() || !isChatVisible()) {
                    chatManager.markAsUnread(chatId);
                }

                if (chatId === chatManager.getCurrentChatId() && isChatVisible()) {
                    addMessageToChat(message);
                    scrollToBottom();
                }

                updateChatList();
                chatManager.saveChatList(); // 새 메시지 수신 시 채팅 목록 저장
            });

            window.pusherInstance.bindEvent('new.notification', (data) => {
                console.log('New notification:', data.userNotification);
                if(data.userNotification.type === 'TRADE_CANCELLED'){ // 취소로 변경시 
                    alert(data.userNotification.message.message);
                    window.location.href = '{{ route('user.dashboard') }}';
                } else if(data.userNotification.type === 'DISPUTE_STARTED'){ // 분쟁으로 변경시
                    alert(data.userNotification.message.message);
                    window.location.href = '{{ route('user.dashboard') }}';
                } else if(data.userNotification.type === 'USDT_RECEIVED'){ // 테더 전송 받을시
                    alert(data.userNotification.message.message);
                    window.location.href = '{{ route('user.wallet.index') }}';
                }
                updateNotificationIcon(true);
            });

            // 고객문의 소켓 보류 
            // function subscribeToSupportTicketChannels() {
            //     const channel = window.pusherInstance.pusher.subscribe('support_conversation.' + currentUserId);
            //     channel.bind('pusher:subscription_succeeded', function() {
            //         console.log('Successfully subscribed to private channel: support_conversation.' + currentUserId);
            //     });

            //     channel.bind('support-conversation', function(data) {
            //         console.log(data);
            //             // addNewNotification(data);
            //         }); 
            //     };

            // subscribeToSupportTicketChannels();

            function addNewNotification(data) {
                const pushList = document.querySelector('.push-list');
                const newNotification = document.createElement('li');

                // 현재 시간을 "time ago" 형식으로 포맷팅
                const timeAgo = formatTimeAgo(new Date());

                newNotification.innerHTML = `
                    <div class="thumb">
                        <img src="${data.user_image || '{{ auth()->user()->userImage }}'}" alt="user">
                    </div>
                    <div class="content">
                        <div class="title-area">
                            <h5 class="title">${data.title || 'New Notification'}</h5>
                            <span class="time">${timeAgo}</span>
                        </div>
                        <span class="sub-title">${data.message || 'You have a new notification'}</span>
                    </div>
                `;

                // 목록의 맨 위에 새 알림 추가
                pushList.insertBefore(newNotification, pushList.firstChild);

                // 알림 아이콘 업데이트 (옵션)
                updateNotificationIcon(true);
            }

            function formatTimeAgo(date) {
                const seconds = Math.floor((new Date() - date) / 1000);
                let interval = seconds / 31536000;

                if (interval > 1) return Math.floor(interval) + " years ago";
                interval = seconds / 2592000;
                if (interval > 1) return Math.floor(interval) + " months ago";
                interval = seconds / 86400;
                if (interval > 1) return Math.floor(interval) + " days ago";
                interval = seconds / 3600;
                if (interval > 1) return Math.floor(interval) + " hours ago";
                interval = seconds / 60;
                if (interval > 1) return Math.floor(interval) + " minutes ago";
                return Math.floor(seconds) + " seconds ago";
            }

            function updateNotificationIcon(hasNewNotifications) {
                if (hasNewNotifications) {
                    $('.alram__cnt').removeClass('hide');
                } else {
                    $('.alram__cnt').addClass('hide');
                }
                // const notificationIcon = document.getElementById('notification-icon');
                // const alarmCountSpan = notificationIcon.nextElementSibling;

                // if (hasNewNotifications) {
                //     notificationIcon.classList.add('has-notifications');
                //     notificationIcon.innerHTML =
                //         `<img src="{{ asset('/public/pub') }}/img/notifications@2x.png">`;
                // } else {
                //     notificationIcon.classList.remove('has-notifications');
                //     notificationIcon.innerHTML =
                //         `<img src="{{ asset('/public/pub') }}/img/notifications@2x.png">`;
                // }
            }

            // 알림 목록 업데이트 함수
            function updateNotificationList() {
                fetch('{{ route('user.messages.getNotifications') }}') // 이 route는 서버에서 구현해야 합니다
                    .then(response => response.json())
                    .then(data => {
                        pushList.innerHTML = ''; // 기존 목록 비우기
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <div class="thumb">
                                    <img src="{{ auth()->user()->userImage }}" alt="user">
                                </div>
                                <div class="content">
                                    <div class="title-area">
                                        <h5 class="title">${item.message.title}</h5>
                                        <span class="time">${item.created_at}</span>
                                    </div>
                                    <span class="sub-title">${item.message.message}</span>
                                </div>
                            `;

                            if (item.sender_id) {
                                li.style.cursor = 'pointer';
                                li.addEventListener('click', function() {
                                    if (item?.type === 'TRADE_STARTED') {
                                        window.location.href =
                                            `{{ route('user.dashboard') }}/${item.order_id}`;
                                    } else if (item?.type === 'USDT_RECEIVED' || item?.type === 'USDT_SENT') {
                                        window.location.href =
                                            `{{ route('user.wallet.index') }}`;
                                    }
                                });
                            }
                            pushList.appendChild(li);
                        });
                        updateNotificationIcon(false);
                    })
                    .catch(error => console.error('Error:', error));
            }

            // 알림 아이콘 클릭 이벤트
            notificationIcon.addEventListener('click', function(e) {
                e.preventDefault();
                updateNotificationList();
            });

            // 초기 알림 아이콘 상태 설정
            updateNotificationIcon(false);
        }

        // Initial binding of events
        bindPusherEvents();

        function updateChatList() {
            chatListContent.innerHTML = '';
            chatManager.getAllChatIds().forEach(chatId => {
                const chatItem = document.createElement('div');
                chatItem.className = 'chat-list-item';
                if (chatManager.isUnread(chatId)) {
                    chatItem.classList.add('unread');
                }
                if (chatManager.getCurrentChatId() === chatId) {
                    chatItem.classList.add('active');
                }
                const chat = chatManager.getChat(chatId);
                const unreadCount = chatManager.getUnreadCount(chatId);
                const receiverName = chatManager.getReceiverName(chat.receiverId);

                chatItem.innerHTML = `
                    <span>${receiverName}</span>
                    ${unreadCount > 0 ? `<span class="unread-count">${unreadCount}</span>` : ''}
                `;

                chatItem.onclick = () => {
                    switchToChat(chatId);
                    updateChatList();
                };
                chatListContent.appendChild(chatItem);
            });

            // 채팅 목록이 비어있으면 숨기기
            if (chatManager.getAllChatIds().length === 0) {
                chatList.style.display = 'none';
            } else {
                showChatList();
            }

            chatManager.saveChatList(); // 채팅 목록 업데이트 시 저장
        }

        function switchToChat(chatId) {
            if (chatManager.switchChat(chatId)) {
                const chat = chatManager.getChat(chatId);
                const receiverName = chatManager.getReceiverName(chat.receiverId);
                chatTitle.textContent = `${receiverName}`;
                // console.log(`${receiverName}`);
                loadMessages(chatId);
                showChatPopup();
                chatManager.markAsRead(chatId);
                updateChatList();
                chatManager.saveChatList();
            }
        }

        function startChat(orderId, senderId) {
            window.receiverId = senderId;

            showChatPopup();

            // 서버에서 수신자 이름을 가져오는 함수
            function fetchReceiverName(chatId) {
                return fetch(`{{ setRoute('user.messages.getReceiverName') }}?receiver_id=${chatId}`)
                    .then(response => response.json())
                    .then(data => data.receiver_name);
            }

            fetchReceiverName(senderId).then(receiverName => {
                const chatId = chatManager.genChatId(orderId, currentUserId, senderId);
                chatManager.createChat(chatId, senderId, receiverName);
                switchToChat(chatId);
                updateChatList();
            }).catch(error => {
                console.error('Error fetching receiver name:', error);
                const chatId = chatManager.genChatId(orderId, currentUserId, senderId);
                chatManager.createChat(chatId, senderId, `User ${senderId}`);
                switchToChat(chatId);
                updateChatList();
            });
        }
        // 채팅 메세지 초기 셋팅 부분
        function loadMessages(chatId) {
            const chat = chatManager.getChat(chatId);
            // console.log('chatId', chatId);
            fetch(`{{ setRoute('user.messages.getMessages') }}?chat_id=${chatId}`)
                .then(response => response.json())
                .then(data => {
                    // console.log(data);
                    chatBody.innerHTML = `
                        <div class="cl-noti">
                            <dl>
                                <dt>NOTICE</dt>
                                <dd style="color: black;">
                                    {!! nl2br(__('chatMessage')) !!}
                                </dd>
                            </dl>
                        </div>
                    `;
                    /* 이전 버전
                           <div class="cl-noti">
                            <dl>
                                <dt>NOTICE</dt>
                                <dd style="color: blue;">
                                    {!! nl2br(__('chat notice1')) !!}
                                     <br>
                                    {{ __('chat notice2') }}
                                </dd>
                            </dl>
                            <br>
                            <dl>
                                <dt>NOTICE</dt>
                                <dd style="color: blue;"> {{ __('chat notice3') }}</dd>
                                <dd style="color: blue;"> {{ __('chat notice4') }}</dd>
                                <dd style="color: blue;"> {{ __('chat notice5') }}</dd>
                                <dd style="color: blue;"> {{ __('chat notice6') }}</dd>
                                <dd style="color: blue;"> {{ __('chat notice7') }}</dd>
                            </dl>
                        </div>
                    */
                    data.messages.forEach(message => {
                        addMessageToChat(message);
                    });
                    scrollToBottom();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`{{ __('메시지를 불러오는데 실패했습니다. 페이지를 새로고침해주세요.') }}`);
                });
        }

        function addMessageToChat(item) {
            const messageElement = document.createElement('div');

            if (currentUserId == item.sender_id) {
                messageElement.className = 'my-chat';
            } else {
                messageElement.className = 'chat-other';
            }

            const messageTime = new Date(item.created_at);

            if (item.message.includes('File shared:')) {
                const fileInfo = item.message.split(' - ');
                const fileName = fileInfo[0].replace('File shared: ', '');
                const downloadLink = fileInfo[1].replace('[Download](', '').replace(')', '');

                messageElement.innerHTML = `
                    <div class="ch-txt-wrap">
                        <p class="ch-txt">${fileName}</p>
                        <a href="${downloadLink}" class="btn-down">Download</a>
                    </div>
                    <div class="date-time">${messageTime.toLocaleString()}</div>
                `;
            } else {
                messageElement.innerHTML = `
                    <div class="ch-txt-wrap">
                        <p class="ch-txt">${item.message}</p>
                    </div>

                    <div class="date-time">${messageTime.toLocaleString()}</div>
                `;
            }

            chatBody.appendChild(messageElement);
            scrollToBottom();
        }

        function scrollToBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function sendMessage(message, retryCount = 3) {
            const currentChatId = chatManager.getCurrentChatId();
            console.log('currentChatId', currentChatId)
            if (currentChatId) {
                const chat = chatManager.getChat(currentChatId);
                console.log('receiver', chat.receiverId);
                fetch('{{ setRoute('user.messages.sendMessage') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            receiver_id: chat.receiverId,
                            chat_id: currentChatId,
                            message: message
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        messageInput.value = '';

                        function getCurrentTimeString() {
                            const now = new Date();
                            return now.toISOString();
                        }

                        addMessageToChat({
                            sender_id: currentUserId,
                            message: message,
                            created_at: getCurrentTimeString(),
                        });
                        scrollToBottom();
                        chatManager.saveChatList(); // 메시지 전송 후 저장
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (retryCount > 0) {
                            console.log(`Retrying... (${retryCount} attempts left)`);
                            setTimeout(() => sendMessage(message, retryCount - 1), 1000);
                        } else {
                            alert(`{{ __('메시지 전송에 실패했습니다. 다시 시도해주세요.') }}`);
                        }
                    });
            }
        }

        // Event listeners
        function closeChatHandler(e) {
            e.preventDefault();
            e.stopPropagation();
            chatPopup.style.display = 'none';
            updateChatList();
            chatManager.saveChatList();
        }

        closeButton.addEventListener('click', closeChatHandler);
        closeButton.addEventListener('touchend', closeChatHandler);

        // const chatHeader = document.querySelector('.chat-header');
        // chatHeader.addEventListener('touchend', function(e) {
        //     e.stopPropagation();
        // });

        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value;
            if (message) {
                sendMessage(message);
            }
        });

        const fileInput = document.getElementById('file-input');
        const attachFileBtn = document.getElementById('attach-file');

        attachFileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                uploadFile(file);
            }
        });

        function uploadFile(file) {
            // 파일 크기 체크 (예: 10MB 제한)
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                alert(`{{ __('파일 크기가 10MB를 초과합니다. 더 작은 파일을 선택해주세요.') }}`);
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('receiver_id', window.receiverId);

            // 업로드 진행 상황을 표시할 요소 생성
            const progressElement = document.createElement('div');
            progressElement.className = 'upload-progress';
            progressElement.style.width = '0%';
            chatBody.appendChild(progressElement);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('user.messages.uploadFile') }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressElement.style.width = percentComplete + '%';
                    progressElement.textContent = Math.round(percentComplete) + '%';
                }
            };

            xhr.onload = function() {
                progressElement.remove();
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const message =
                            `File shared: ${response.file_name} - [Download](${response.download_link})`;
                        sendMessage(message);
                    } else {
                        console.error('File upload failed:', response.message);
                        alert(`{{ __('파일 업로드에 실패했습니다. 다시 시도해 주세요.') }}`);
                    }
                } else {
                    console.error('File upload failed');
                    alert(`{{ __('파일 업로드에 실패했습니다. 다시 시도해 주세요.') }}`);
                }
            };

            xhr.onerror = function() {
                progressElement.remove();
                console.error('File upload error');
                alert(`{{ __('파일 업로드 중 오류가 발생했습니다. 다시 시도해 주세요.') }}`);
            };

            xhr.send(formData);
        }

        // function completeTransaction(tradeId) {
        //     fetch('/api/complete-transaction', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //             },
        //             body: JSON.stringify({
        //                 trade_id: tradeId
        //             })
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             alert(`거래 ${tradeId}가 완료되었습니다!`);
        //             // You might want to update the UI or redirect the user after completing the transaction
        //         })
        //         .catch(error => console.error('Error:', error));
        // }

        // chatPopup을 표시할 때 저장된 위치 적용
        function showChatPopup() {
            $('.push-wrapper').hide();
            $('.chat-layer').show();

            // if (chatPopup.style.display !== 'none') {
            //     chatPopup.style.display = '';
            // const savedPosition = localStorage.getItem('chatPopupPosition');
            // const savedSize = localStorage.getItem('chatPopupSize');
            // if (savedPosition) {
            //     const {
            //         top,
            //         left
            //     } = JSON.parse(savedPosition);
            //     chatPopup.style.top = top;
            //     chatPopup.style.left = left;
            // }
            // if (savedSize) {
            //     const {
            //         width,
            //         height
            //     } = JSON.parse(savedSize);
            //     chatPopup.style.width = width;
            //     chatPopup.style.height = height;
            // }
            // }
        }

        function showChatList() {
            // if (chatList.style.display !== 'block') {
            //     chatList.style.display = 'block';
            //     const savedPosition = localStorage.getItem('chatListPosition');
            //     const savedSize = localStorage.getItem('chatListSize');
            //     if (savedPosition) {
            //         const {
            //             top,
            //             left
            //         } = JSON.parse(savedPosition);
            //         chatList.style.top = top;
            //         chatList.style.left = left;
            //     }
            //     if (savedSize) {
            //         const {
            //             width,
            //             height
            //         } = JSON.parse(savedSize);
            //         chatList.style.width = width;
            //         chatList.style.height = height;
            //     }
            // }
        }

        // document.getElementById('start-chat-header').addEventListener('click',
        //     function() {
        //         startChat(receiverId);
        //     });
        // document.getElementById('complete-transaction').addEventListener('click', function() {
        //     window.location.href = 'https://test.anakitrade.com/user/wallet';
        // });

        function openChat() {
            $('.chat-layer').show();
        }

        function closeChat() {
            $('.chat-layer').hide();
        }

        window.switchToChat = switchToChat;
        window.startChat = startChat;
    });
</script>

<style>

    .blue-text {
        color: blue;  /* 파란색으로 텍스트 색상 설정 */
    }

    #chat-form {
        width: 100%;
        display: flex;
    }

    /* 채팅 팝업 스타일 */
    .chat-popup,
    .chat-list {
        position: fixed;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        display: none;
        flex-direction: column;
        z-index: 1000;
        touch-action: none;
        overflow: hidden;
        resize: both;
    }

    .chat-popup {
        bottom: 20px;
        right: 20px;
        width: 300px;
        height: 400px;
    }

    .chat-list {
        bottom: 20px;
        left: 20px;
        width: 200px;
        height: 300px;
    }

    .chat-body,
    #chat-list-content {
        flex-grow: 1;
        overflow-y: auto;
    }

    .resize-handle {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 15px;
        height: 15px;
        background: transparent;
        cursor: se-resize;
    }

    .resize-handle::before {
        content: '';
        position: absolute;
        right: 3px;
        bottom: 3px;
        width: 9px;
        height: 9px;
        border-right: 2px solid #888;
        border-bottom: 2px solid #888;
    }

    .chat-header {
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        cursor: move;
    }

    .chat-title {
        color: black;
        margin: 0;
    }

    .chat-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .chat-footer {
        padding: 10px;
        border-top: 1px solid #ddd;
    }

    /* 메시지 스타일 */
    .message {
        margin-bottom: 10px;
        display: flex;
    }

    .message-left {
        justify-content: flex-start;
    }

    .message-right {
        justify-content: flex-end;
    }

    .message .content {
        background-color: #f1f1f1;
        padding: 5px 10px;
        border-radius: 10px;
        color: black;
        display: inline-block;
        max-width: 70%;
    }

    .message-right .content {
        background-color: #0084ff;
        color: white;
    }

    /* 채팅 목록 스타일 */
    .chat-list {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 200px;
        max-height: 300px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        z-index: 1000;
        color: black;
        display: none;
        overflow-y: auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        /* 그림자 효과 추가 */
    }

    .chat-list-header {
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        cursor: move;
        background-color: rebeccapurple;
        color: black;
    }

    .chat-list-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: bold;
        /* 제목을 굵게 표시 */
    }

    #close-chat-list {
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: white;
        /* 닫기 버튼 색상을 흰색으로 변경 */
        opacity: 0.8;
        /* 약간의 투명도 추가 */
        transition: opacity 0.3s ease;
    }

    #close-chat-list:hover {
        opacity: 1;
        /* 호버 시 완전 불투명하게 변경 */
    }

    #chat-list-content {
        background-color: white;
        /* 내용 영역 배경색 명시적 설정 */
    }

    /* 채팅 목록 아이템 스타일 수정 */
    .chat-list-item {
        position: relative;
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: background-color 0.3s ease;
        color: #333;
        /* 텍스트 색상을 어두운 색으로 설정 */
    }

    .chat-list-item:hover {
        background-color: #f8f9fa;
    }

    .chat-list-item.active {
        background-color: #e9ecef;
        font-weight: bold;
    }

    .chat-list-item.unread {
        background-color: #cce5ff;
        font-weight: bold;
        border-left: 4px solid #007bff;
    }

    .chat-list-item.active.unread {
        background-color: #b8daff;
        border-left: 4px solid #0056b3;
    }

    .unread-count {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.8em;
    }

    /* 파일 업로드 진행 바 */
    .upload-progress {
        height: 5px;
        background-color: #007bff;
        width: 0;
        transition: width 0.3s ease;
        margin-bottom: 10px;
    }

    /* 모바일 환경을 위한 미디어 쿼리 */
    @media (max-width: 768px) {
        .chat-popup {
            width: 90%;
            height: 80%;
            bottom: 10px;
            right: 5%;
        }
    }

    .push-icon {
        position: relative;
    }

    .notification-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 10px;
        height: 10px;
        background-color: red;
        border-radius: 50%;
    }

    .btn-close {
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
        width: 1em;
        height: 1em;
        opacity: 0.5;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
        border: 0;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .chat-header .btn-close {
        color: #000;
        /* 채팅 헤더의 닫기 버튼 색상 */
    }

    .chat-list-header .btn-close {
        color: #fff;
        /* 채팅 리스트 헤더의 닫기 버튼 색상 */
    }

</style>
