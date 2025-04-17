<?php $__env->startSection("title"); ?>
Manage User
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

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

        <div class="container-fluid px-4">
            <h1 class="mt-4">사용자 리스트</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">관리</li>
                <li class="breadcrumb-item active">사용자</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    전체 이용자 :  <?php echo e($userTotalCount); ?>

                </div>
                
                <div class="card-body">
                    <form id="submitForm" action="" method="GET">
                        <input type="hidden" name="page" id="pages" value="<?php echo e($page); ?>">
                            <div class="buttons-and-search-container">
                                <div class="buttons-container">

                                </div>

                                <div class="searchGroup">
                                    <select id="searchSelect" class="form-control form-control-sm" name="searchType" onchange="selectType(this.value)" style="width: 30%">
                                        <option <?php echo e(($searchType === 'name')? 'selected':''); ?> value='name'>이름</option>
                                        <option  <?php echo e(($searchType === 'email')? 'selected':''); ?>  value='email'>Email</option>
                                    </select>
                                    <input type="search" id="searchInput" class="form-control form-control-sm" name="searchKey" style="width: 50%" placeholder="검색어를 입력하세요" value='<?php echo e($searchKey); ?>'>
                                    <button type="button" class="btn btn-block btn-default" style="width: 14%;" onclick="search()"><i class="fa fa-fw fa-search"></i></button>
                                </div>
                            </div>

                        <div style="height: 10px"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr style="text-align: center;">
                                <th>No</th>
                                <th>이름(실제이름)</th>
                                <th>Email</th>
                                <th>USDT 보유량</th>
                                <th>유저 타입</th>
                                <th>가입 일자</th>
                                <th>계정 상태</th>
                                <th>상세 보기 </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $count = ($page-1)*10+1; 
                            ?>
                            <?php $__currentLoopData = $userList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="text-align: center;">
                                    <td style="width:8%"><?php echo e($count); ?></td>
                                    <td style="width:15%"><?php echo e($list->username); ?> ( <?php echo e($list->realname); ?> ) </td>
                                    <td style="width:15%"><?php echo e($list->email); ?></td>
                                    <td style="width:13%"><?php echo e($list->usdtBalance); ?></td>
                                    <?php if(isset($list->getReferenceInfo) && $list->getReferenceInfo->group_id == 0){
                                            $userType = '에이전트';
                                         } else if(isset($list->getReferenceInfo) && $list->getReferenceInfo->group_id != 0){
                                            $userType = '파트너';  
                                         } else {
                                            $userType = '일반';     
                                         }
                                     ?>
                                    <td style="width:12%"><?php echo e($userType); ?> </td>
                                    <td style="width:13%"><?php echo e($list->created_at); ?></td>
                                    <td style="width:10%"><?php echo e(($list->status == 1)? '사용중' : '정지'); ?></td>
                                    <td style="">
                                        <input type="button" class="btn btn-block btn-primary btn-xs" style='width:60%' onclick="moveListViewForm('<?php echo e(urlSafeEncrypt($list->id)); ?>')" value="보기">
                                        <!-- <input type="button" class="btn btn-block btn-danger btn-xs" style="margin-top:0;" onclick="deleteUser()"  value="삭제"> -->
                                    </td>
                                </tr>
                            <?php $count++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <div class="pagination">
                            <?php echo e($userList->links()); ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
<script>
    function search(){
        document.getElementById('submitForm').submit();
    }
    function moveListViewForm(id){
       location.href='/admin/manage-user/view/'+id;
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.mainLayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/userList.blade.php ENDPATH**/ ?>