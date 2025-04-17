<?php $__env->startSection("title"); ?>
Manage External
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
                    전체 외부거래 건수 :  <?php echo e($externalTransactionCount); ?>

                </div>
                
                <div class="card-body">
                    <form id="submitForm" action="" method="GET">
                        <input type="hidden" name="page" id="pages" value="<?php echo e($page); ?>">                               
                            <div class="btn-group-lg" role="group" aria-label="Basic radio toggle button group">
                                <input type="button" class="btn btn-primary " onclick='moveInternalList()' autocomplete="off" value='내부'>
                                <input type="button" class="btn btn-primary active"  onclick='moveExternalList()' value='외부' autocomplete="off" checked>
                                <input type="button" class="btn btn-primary " onclick='moveTransferWalletList()' value='지갑간전송' autocomplete="off">
                            </div>

                            <div class="buttons-and-search-container" style='margin-top:1%'>
                                <div class="buttons-container">
                                <select class="form-select" name='transferType' aria-label="Select Option 1" >
                                    <option value = '' <?php echo e(($transferType =='')? 'selected':''); ?> >구분</option> 
                                    <option value = 1  <?php echo e(($transferType == 1)? 'selected':''); ?>>전송</option>
                                    <option value = 2  <?php echo e(($transferType == 2)? 'selected':''); ?>>받음</option>
                                </select>

                                <div class="date-select-container" style="display: flex; align-items: center;">
                                    <label for="startDate" style="width: 180px;">기간선택:</label>
                                    <input type="date" class="form-control form-control-sm" name="startDate" id="startDate" value=<?php echo e($startDate); ?> style="width:70%;" placeholder="시작날짜">
                                    
                                    <span style="margin: 0 10px;">/</span>

                                    <input type="date" class="form-control form-control-sm" name="endDate" id="endDate" value=<?php echo e($endDate); ?> style="width: 70%;" placeholder="종료날짜">
                                </div>

                                </div>
                                <div class="searchGroup">                                    
                                    <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 30%">
                                        <option value='name'>이름</option>
                                    </select>
                                    <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" autocomplete="off" style="width:60%" placeholder="검색어를 입력하세요" value='<?php echo e($searchKey); ?>'>
                                    <button type="button" class="btn btn-block btn-default" style="width: 30%;border: 2px solid #555; border-radius: 5px;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                </div>
                            </div>
                       
                        <div style="height: 10px"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr style="text-align: center;">
                                <th>No</th>
                                <th>이름(실제이름)</th>
                                <th>구분(전송/받음)</th>
                                <th>USDT전송량</th>
                                <th>전송 수수료(USDT)</th>
                                <th>지갑주소(전송/받은)</th>
                                <th>전송 일자</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $count = ($page-1)*10+1; 
                            ?>
                            <?php $__currentLoopData = $externalTransactionList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="text-align: center;">
                                    <td style="width:3%"><?php echo e($count); ?></td>
                                    <td style="width:11%"><?php echo e($list->getUserInfo->username); ?>(<?php echo e($list->getUserInfo->realname); ?>) </td>
                                    <td style="width:6%"><?php echo e(($list->transfer_type == 1)? '전송':'받음'); ?></td>
                                    <td style="width:6%"><?php echo e(round($list->amount,3)); ?></td>
                                    <td style="width:5%"><?php echo e(round($list->network_fee)); ?></td>
                                    <td style="width:11%"><?php echo e(($list->from)??$list->to); ?></td>
                                    <td style="width:9%"><?php echo e($list->created_at); ?></td>
                                </tr>
                            <?php $count++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <div class="pagination">
                            <?php echo e($externalTransactionList->links()); ?>

                        </div>
                    </form>
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
<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/externalTransactionList.blade.php ENDPATH**/ ?>