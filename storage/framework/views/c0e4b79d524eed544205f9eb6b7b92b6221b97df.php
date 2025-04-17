<?php
    $default = get_default_language_code();
?>


<?php $__env->startSection('content'); ?>
    <div class="content purchase">
        
        <h2 class="page-tit"><?php echo e(__('Buy Tether (USDT)')); ?></h2>

        

        <div class="container-row">
            <ul class="plist" id="sell-list-container">
                <?php echo $__env->make('user.sell-list.partials.list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </ul>
        </div>

        <div class="purchase-more">
            <?php if($listData->hasMorePages()): ?>
                <button class="btn-other btn-small" id="load-more">
                    <span class="txt"><?php echo e(__('Load More Trades')); ?></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadMoreItems);
            }

            addItemClickListeners();

            function loadMoreItems() {
                currentPage++;
                fetch(`<?php echo e(route('user.sell-list.index')); ?>?page=${currentPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const container = document.getElementById('sell-list-container');
                        container.insertAdjacentHTML('beforeend', html);
                        addItemClickListeners();

                        // 더 이상 페이지가 없으면 "더 불러오기" 버튼을 숨깁니다
                        if (html.trim() === '') {
                            document.querySelector('.purchase-more').style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function addItemClickListeners() {
                document.querySelectorAll('.plist__btn button, .plist__btn a').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/sell-list/index.blade.php ENDPATH**/ ?>