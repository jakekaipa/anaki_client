<?php $__env->startSection("title"); ?>
Manage Internal
<?php $__env->stopSection(); ?>

<?php $__env->startSection('style'); ?>
<style>

    .buttons-and-search-container {
        display: flex;
        gap: 5px;
        /*align-items: center;*/
        width: 100%;
    }

    .buttons-container {
        display: flex;
        gap: 10px;
        /*align-items: center;*/
        margin-right: auto; /* Pushes buttons to the left */
        width: 100%;
    }

    .searchGroup {
        display: flex;
        gap: 10px;
        align-items: center;
        width:30%;
    }

    .pagination {
        display: flex;justify-content: center;
    }

    .activeTab {
        background-color: #0c84ff;
        color:white;
    }

    .form-select {
        width: 150px; /* 셀렉트 박스 크기 */
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        text-align: center; /* 텍스트 가운데 정렬 */
    }

    .form-select option {
        text-align: center; /* 옵션 텍스트 가운데 정렬 */
    }

    .form-select:focus {
        border-color: #4CAF50;
        outline: none;
    }

    .btn-group-lg .btn {
        font-size: 16px; /* 글씨 크기 */
        padding: 10px 20px; /* 버튼의 크기 조정 */
        transition: all 0.3s ease; /* 부드러운 전환 효과 */
    }

    .btn-group-lg .btn.active {
        background-color: #007bff; /* 활성화된 버튼 색상 */
        border-color: #007bff; /* 활성화된 버튼의 테두리 색상 */
        color: white; /* 활성화된 버튼의 텍스트 색상 */
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.4); /* 활성화된 버튼의 그림자 효과 */
    }

    .btn-group-lg .btn:not(.active):hover {
        background-color: #0056b3; /* 일반 버튼 hover 시 색상 */
        border-color: #0056b3; /* 일반 버튼 hover 시 테두리 색상 */
        color: white; /* hover 상태에서의 텍스트 색상 */
    }

    .btn-group-lg .btn:not(.active) {
        background-color: #f8f9fa; /* 비활성화된 버튼 색상 */
        border-color: #555; /* 비활성화된 버튼의 테두리 색상 */
        color: #555; /* 비활성화된 버튼의 텍스트 색상 */
    }
  
        
   /* Chat message container */
    .chat-message {
        display: flex; /* Use flexbox for alignment */
        flex-direction: column; /* Stack username and message vertically - 추가됨 */
        margin: 10px 0; /* Vertical spacing between messages */
        width: 100%; /* Full width of the container */
    }

    /* Left-aligned messages */
    .chat-message.left {
        align-items: flex-start; /* Align items to the left - justify-content에서 변경됨 */
    }

    /* Right-aligned messages */
    .chat-message.right {
        align-items: flex-end; /* Align items to the right - justify-content에서 변경됨 */
    }

    /* Message wrapper (bubble) */
    .message-wrapper {
        max-width: 70%; /* Limit message width for readability */
        display: inline-block; /* Allow the wrapper to adjust width based on content */
        background-color: #f0f0f0; /* Light gray background for messages (customize as needed) */
        border-radius: 8px; /* Rounded corners for a chat bubble effect */
        padding: 10px; /* Padding inside the message bubble */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    .custom-modal .modal-dialog {
        width: 600px; /* 고정된 너비 (원하는 값으로 조정 가능) */
        height: 500px; /* 고정된 높이 (원하는 값으로 조정 가능) */
        max-width: none; /* Bootstrap의 기본 max-width을 무시 */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
    }

    .custom-modal .modal-content {
        height: 100%; /* 모달 내부 전체 높이를 고정된 dialog에 맞춤 */
        display: flex;
        flex-direction: column; /* header, body, footer를 수직으로 배치 */
        border-radius: 8px;
    }

    .custom-modal .modal-body {
        flex: 1; /* 남은 공간을 채우되 고정된 크기 내에서 동작 */
        padding: 0;
        overflow-y: auto; /* 내용이 넘칠 경우 스크롤 생성 */
    }

    #chatContent {
        padding: 15px;
        background-color: #f0f2f5;
        height: 100%; /* 부모(.modal-body)의 높이를 모두 사용 */
        overflow-y: auto; /* 메시지가 많아지면 스크롤 */
        overflow-x: hidden;
        display: flex;
        flex-direction: column-reverse; /* 최신 메시지가 아래로 */
    }

    /* Username styling */
    .username {
        font-weight: bold; /* Bold username for emphasis */
        font-size: 0.9em; /* Slightly smaller than message text */
        color: #333; /* Dark gray for username text */
        margin-bottom: 5px; /* Space between username and message */
        display: block; /* Ensure username stays on its own line */
    }

    /* Message bubble styling */
    .message-bubble {
        word-wrap: break-word; /* Break long words to prevent overflow */
        font-size: 1em; /* Default message text size */
        color: #000; /* Black text for readability */
    }

    /* Optional: Different background colors for left/right messages */
    .chat-message.left .message-wrapper {
        background-color:#f1f1f1;/* Light blue for left (e.g., user) messages */
    }

    .chat-message.right .message-wrapper {
        background-color:#21B8A1; /* Light green for right (e.g., admin) messages */
    }


</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

        <div class="container-fluid px-4">
            <h1 class="mt-4">거래 리스트</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">관리</li>
                <li class="breadcrumb-item active">거래</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    전체 내부거래 건수 :  <?php echo e($transactionCount); ?>

                </div>
                
                <div class="card-body">
                    <form id="submitForm" action="" method="GET">
                        <input type="hidden" name="page" id="pages" value="<?php echo e($page); ?>">                               
                            <div class="btn-group-lg" role="group" aria-label="Basic radio toggle button group">
                                <input type="button" class="btn btn-primary active" onclick='moveInternalList()' autocomplete="off" value='내부' checked>
                                <input type="button" class="btn btn-primary" onclick='moveExternalList()' value='외부' autocomplete="off">
                                <input type="button" class="btn btn-primary" onclick='moveTransferWalletList()' value='지갑간전송' autocomplete="off">
                            </div>

                            <div class="buttons-and-search-container" style='margin-top:1%'>
                                <div class="buttons-container">
                                    <select class="form-select" name='transactionType' aria-label="Select Option 1" >
                                        <option value=''     <?php echo e(($transactionType ==='')? 'selected':''); ?> >거래타입</option> 
                                        <option value="sell" <?php echo e(($transactionType ==='sell')? 'selected':''); ?>>판매</option>
                                        <option value="buy"  <?php echo e(($transactionType ==='buy')? 'selected':''); ?>>구매</option>
                                    </select>

                                    <select class="form-select" name='state' aria-label="Select Option 2">
                                        <option value=''               <?php echo e(($state =='')? 'selected':''); ?> >거래상태</option>
                                        <option value="open"           <?php echo e(($state =='open')? 'selected':''); ?>>거래시작</option>
                                        <option value="send"           <?php echo e(($state =='send')? 'selected':''); ?>>입금증빙전송</option>
                                        <option value="cancel"         <?php echo e(($state =='cancel')? 'selected':''); ?>>거래취소</option>
                                        <option value="dispute"        <?php echo e(($state =='dispute')? 'selected':''); ?>>분쟁</option>
                                        <option value="dispute-solved" <?php echo e(($state =='dispute-solved')? 'selected':''); ?>>분쟁해결</option>
                                        <option value="done"           <?php echo e(($state =='done')? 'selected':''); ?>>거래완료</option>
                                    </select>

                                    <div class="date-select-container" style="display: flex; align-items: center;">
                                        <label for="startDate" style=" width: 180px;">기간선택:</label>
                                        <input type="date" class="form-control form-control-sm" name="startDate" id="startDate" value=<?php echo e($startDate); ?> style="width: 50%" placeholder="시작날짜">
                                        
                                        <span style="margin: 0 10px;">/</span>

                                        <!-- <label for="endDate" style="width: 100px;">종료날짜: </label> -->
                                        <input type="date" class="form-control form-control-sm" name="endDate" id="endDate" value=<?php echo e($endDate); ?> style="width: 50%;" placeholder="종료날짜">
                                    </div>
                                    
                                </div>
                                <div class="searchGroup">
                                    <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 30%">
                                        <option value='name' <?php echo e(($searchType =='name')? 'selected':''); ?>>이름</option>
                                    </select>
                                    <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 50%" placeholder="검색어를 입력하세요" value='<?php echo e($searchKey); ?>'>
                                    <button type="button" class="btn btn-block btn-default" style="width: 14%;border: 2px solid #555; border-radius: 5px;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                </div>
                            </div>
                       
                        <div style="height: 10px"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr style="text-align: center;">
                                <th>No</th>
                                <th>거래타입</th>
                                <th>판매자(실제이름)</th>
                                <th>구매자(실제이름)</th>
                                <th>거래 테더량</th>
                                <th>수수료</th>
                                <th>상태</th>
                                <th>거래 시작일자</th>
                                <th>상세 보기 </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $count = ($page-1)*10+1; 
                            ?>
                            <?php $__currentLoopData = $transactionList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="text-align: center;">
                                    <td style="width:5%"><?php echo e($count); ?></td>
                                    <td style="width:6%"><?php echo e(($list->order_type ==='sell')? '판매':'구매'); ?></td>
                                    <td style="width:15%">
                                        <?php echo e(($list->order_type ==='sell')? $list->offer_user->username: $list->client_user->username); ?>

                                        (<?php echo e(($list->order_type ==='sell')? $list->offer_user->realname: $list->client_user->realname); ?>)
                                    </td>
                                    <td style="width:15%">
                                        <?php echo e(($list->order_type ==='sell')? $list->client_user->username : $list->offer_user->username); ?>

                                        (<?php echo e(($list->order_type ==='sell')? $list->client_user->realname: $list->offer_user->realname); ?>)
                                    </td>
                                    <td style="width:8%"><?php echo e($list->tetherAmount); ?></td>
                                    <td style="width:11%"><?php echo e($list->fee); ?></td>
                                    <?php  
                                    if($list->state === 'open'){
                                        $stateText = '거래시작';
                                    } else if($list->state === 'send'){
                                        $stateText = '입금증빙전송';
                                    } else if($list->state === 'dispute'){
                                        $stateText = '분쟁중';       
                                    } else if($list->state === 'dispute-solved'){
                                        $stateText = '분쟁해결';       
                                    } else if($list->state === 'cancel') {
                                        $stateText = '거래취소';       
                                    } else {
                                        $stateText = '거래완료';       
                                    }
                                    ?>
                                    <td style="width:7%"><?php echo e($stateText); ?> </td>
                                    <td style="width:11%"><?php echo e($list->created_at); ?></td>
                                    <td style="width:13%">
                                    <?php if($list->state === 'dispute'): ?>    
                                        <input type="button" class="btn btn-block btn-primary btn-xs" style='width:50%' onclick="openViewModal('<?php echo e(urlSafeEncrypt($list->id)); ?>')" value="보기">
                                        <input type="button" class="btn btn-block btn-danger btn-xs" style="width:40%;margin-top:0;" onclick="openChatModal('<?php echo e(urlSafeEncrypt($list->id)); ?>')"  value="채팅보기">
                                    <?php else: ?>
                                        <input type="button" class="btn btn-block btn-primary btn-xs" style='width:60%' onclick="openViewModal('<?php echo e(urlSafeEncrypt($list->id)); ?>')" value="보기">
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            <?php $count++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <div class="pagination">
                            <?php echo e($transactionList->links()); ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
       
<!-- 거래 상세 모달 -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 30%; margin: 10% auto 0;">
        <div class="modal-content">
        <div class="modal-header" >
            <h5 class="modal-title " id="viewModalLabel" style="flex: 1; text-align: center;">거래 상세 내역</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <div class="modal-body">
                <div id="modalContent">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 채팅 내역 모달 -->
<div class="custom-modal" id="chatModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #007bff; color: white; border-bottom: 2px solid #0056b3; padding: 10px 20px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h5 class="modal-title" id="chatModalLabel">채팅 내역</h5>
                <button type="button" class="btn-close" id="closeChatModalBtn" aria-label="Close" style="color: white;"></button>
            </div>
            <div class="modal-body">
                <div id="chatContent">
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; padding: 15px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                <button type="button" class="btn btn-secondary" id="closeChatModal" style="background-color: #6c757d; color: white; border: none; border-radius: 5px; padding: 10px 20px;">닫기</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    function search(){
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;
        // 날짜를 비교해서 시작 날짜가 종료 날짜보다 늦은 경우
        if (new Date(startDate) > new Date(endDate)) {
            alert('시작 날짜는 종료 날짜보다 클 수 없습니다.');
            return false; // 제출을 막고 알림 창을 띄움
        }
        document.getElementById('submitForm').submit();
    }

    // 거래 상세
    function openViewModal(id) {
        fetch(`/admin/manage-trade/view/${id}`)  
            .then(response => response.json())
            .then(data => {
                //console.log(data);
                // 판매자 정보
                const sellerUsername = (data.order_type === 'sell') ? data.offer_user?.username : data.client_user?.username;
                const sellerRealname = (data.order_type === 'sell') ? data.offer_user?.realname : data.client_user?.realname;

                // 구매자 정보
                const buyerUsername = (data.order_type === 'sell') ? data.client_user?.username : data.offer_user?.username;
                const buyerRealname = (data.order_type === 'sell') ? data.client_user?.realname : data.offer_user?.realname;
                
                // 테이블 형식으로 내용 추가
                document.getElementById('modalContent').innerHTML = `
                <table class="table table-bordered table-striped" style="margin: 0 auto;">
                    <tbody>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>판매자</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${sellerRealname ? sellerRealname : sellerUsername}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>구매자</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${buyerRealname ? buyerRealname : buyerUsername}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>거래량</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${data.tetherAmount}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>수수료</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${data.fee}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>상태</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${chageStateText(data.state)}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>거래일</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${formatDate(data.created_at)}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>거래 종료일</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">${formatDate(data.ended_at)}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #0d6efd; color: white; text-align: center; vertical-align: middle; padding: 10px;"><strong>입금증빙자료</strong></td>
                            <td style="text-align: center; vertical-align: middle; padding: 10px;">
                            ${data.payProof ? `
                                <a href="/frontend/proof/${data.payProof}" download>
                                    <img style="width:200px; height:200px;" src="/frontend/proof/${data.payProof}" alt="Purchase Proof Image">
                                </a>
                            ` : '증빙 자료 없음'}
                            </td>
                        </tr>
                         
                    </tbody>
                </table>
                `;

                if (data.state === 'dispute') {
                    document.getElementById('modalContent').innerHTML += `
                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-secondary w-100" onclick="solveDispute('${id}',1)" id="resolveDisputeButton">분쟁 해결</button>
                    </div>
                     <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-danger w-100" onclick="solveDispute('${id}',2)" id="resolveDisputeButton">테더 전송 후 분쟁 해결</button>
                    </div>
                    `;
                }
                
            // 모달을 띄움
            $('#viewModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // 채팅 
    function openChatModal(id) {
        const $chatModal = $('#chatModal').show();
        $.ajax({
            url: '/admin/manage-trade/chatting-message-list/' + id, 
            method: 'GET',
            success: function(response) {
                console.log(response);
                // return false; 

                if (response && response !== -1) {
                    const chatContent = $('#chatContent'); 
                    chatContent.html('');
                   
                    if (Array.isArray(response)) {
                        response.forEach(msg => {
                            let parts = msg.chat_id.split("-");
                            const positionClass = ( msg.sender_id == parts[1])? 'left':'right'
                           
                            chatContent.append(`
                              <div class="chat-message ${positionClass}">
                                    <span class="username">${msg.sender.username}</span> <!-- 순서 변경: username이 message-wrapper 위로 이동 -->
                                    <div class="message-wrapper">
                                        <div class="message-bubble">${msg.message}</div> 
                                    </div>
                              </div>
                            `);
                        });
                        chatContent.scrollTop(chatContent[0].scrollHeight); // 스크롤 맨 아래로
                    } else {
                        chatContent.html('<p>채팅 메시지가 없습니다.</p>');
                    }

                } else {
                    alert('에러가 발생하였습니다. 다시 시도해주세요.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#chatContent').html('<p>채팅 내역을 불러오는데 실패했습니다.</p>');
            }
        });
        
    }

        
    // 닫기 버튼 이벤트
    $('#closeChatModal, #closeChatModalBtn').on('click', function() {
        $('#chatModal').hide();
    });

    // 날짜 형태 변환
    function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString('ko-KR', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                });
    }
    
    // 상태 텍스트 변환
    function chageStateText(state){
        let stateText = '';
            switch (state) {
                case 'open':
                    stateText = '거래시작';
                    break;
                case 'send':
                    stateText = '입금증빙전송';
                    break;
                case 'dispute':
                    stateText = '분쟁중';
                    break;
                case 'dispute-solved':
                    stateText = '분쟁해결';
                    break;
                case 'cancel':
                    stateText = '거래취소';
                    break;
                default:
                    stateText = '거래완료';
                    break;
            }
        return stateText;
    }

     // 분쟁 해결 버튼
     function solveDispute(id,option){
        let confirmText = (option == 1)? '분쟁 해결 하시겠습니까?' : '테더 전송 후 분쟁 해결 하시겠습니까?' ;
        if(confirm(confirmText)){
            $.ajax({
                url: '/admin/manage-trade/change-state', 
                method: 'POST',
                data: {
                    transactionId: id,
                    state:'dispute-solved',
                    option:option,
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                success: function(response) {
                if(response = 1){
                    alert('변경 되었습니다.');
                    window.location.reload();
                } else{
                    alert('변경 작업중 오류가 발생하였습니다.');
                }   
                },
                error: function(xhr, status, error) {
                    alert('오류가 발생하였습니다.');
                }
            });
        }
    }
   
     // 외부 전송 내역
     function moveExternalList(){
        location.href='/admin/manage-trade/external-list';
    }

    // 내부 전송 내역
    function moveInternalList(){
        location.href='/admin/manage-trade/internal-list';
    }

     // 지갑간 전송 내역
     function moveTransferWalletList(){
        location.href='/admin/manage-trade/transferWallet-list';
    }


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/transactionList.blade.php ENDPATH**/ ?>