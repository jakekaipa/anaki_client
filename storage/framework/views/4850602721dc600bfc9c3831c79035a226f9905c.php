<?php $__env->startSection("title"); ?>
Manage cs
<?php $__env->stopSection(); ?>

<?php $__env->startSection('style'); ?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 90%;
        margin: 50px auto;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 8px;
    }

    h1 {
        text-align: center;
        color: #555;
        margin-bottom: 30px;
    }

    /* 공지사항 제목과 버튼을 한 줄로 정렬 */
    .header-container {
        display: flex;
        justify-content: center; /* 좌우 정렬 */
        align-items: center; /* 수직 중앙 정렬 */
        margin-bottom: 30px;
    }

    .header-container h1 {
        margin: 0; /* 제목의 기본 margin 제거 */
    }

    .header-container .btn {
        margin: 0; /* 버튼에 기본 margin 제거 */
    }

    .board-table {
        width: 100%;
        border-collapse: collapse;
    }

    .board-table th, .board-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    .board-table th {
        background-color: #007bff;
        color: white;
    }

    .board-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .board-table tr:hover {
        background-color: #ddd;
    }

    .board-title {
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .board-title:hover {
        color: #0056b3;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .pagination a {
        text-decoration: none;
        color: #007bff;
        padding: 10px 15px;
        border: 1px solid #007bff;
        margin: 0 5px;
        border-radius: 5px;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: white;
    }

    .pagination .active {
        background-color: #007bff;
        color: white;
        pointer-events: none;
    }

    .back-btn {
        display: block;
        margin-top: 30px;
        text-align: center;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        width: 100px;
        margin-left: auto;
        margin-right: auto;
    }

    .back-btn:hover {
        background-color: #0056b3;
    }

    .new-label {
        background-color:rgb(250, 25, 0);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 10px;
        font-weight: bold;
    }

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="header-container">
            <h1>고객문의</h1>
        </div>

        <table class="board-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>상태</th>
                    <th>고객명</th>
                    <th>작성일</th>
                    <th>상세보기</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $count = ($page-1)*10+1; 
                ?>
                <?php $__currentLoopData = $csList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                    if($list->status == 3){
                        $statusText='답변 대기중';
                    } else if($list->status == 2){
                        $statusText='진행중';
                    } else {
                        $statusText='해결완료';     
                    }
                ?>
                <tr>
                    <td style="width:3%"><?php echo e($count); ?> </td>
                    <td style='width:15%'><?php echo e(strlen($list->subject) > 20 ? substr($list->subject, 0, 20) . '...' : $list->subject); ?></td>
                    <td style="width:6%"><?php echo e($statusText); ?></td>
                    <td style="width:13%"><?php echo e($list->user->username); ?>(<?php echo e($list->user->realname); ?>) </td>
                    <td style="width:8%"><?php echo e($list->created_at->format('Y-m-d')); ?></td>
                    <td style="width:10%">
                        <?php if(isset($list->getCommentsOne) && $list->getCommentsOne->write_user_id !== Auth::guard('admin')->id() && (!$list->getCommentsOne->read_user_id) ): ?>
                            <input type='button' class='btn btn-block btn-primary btn-xs' onclick="csView('<?php echo e(urlSafeEncrypt($list->id)); ?>','<?php echo e(urlSafeEncrypt($list->getCommentsOne->id)); ?>')" value='상세보기'>
                            <span class="new-label">NEW</span>
                        <?php else: ?>
                        <input type='button' class='btn btn-block btn-primary btn-xs' onclick="csView('<?php echo e(urlSafeEncrypt($list->id)); ?>','')" value='상세보기'>
                        <?php endif; ?> 
                    </td>
                </tr>
                <?php 
                    $count++;
                ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php echo e($csList->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    function csView(id,commentId){
        if(commentId)
        {
            location.href='/admin/manage-cs/view/'+id+'/'+commentId;
        } else {
            location.href='/admin/manage-cs/view/'+id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/csList.blade.php ENDPATH**/ ?>