<?php
    $default = get_default_language_code();
?>


<?php $__env->startSection('content'); ?>
    <div class="content purchase">
        <h2 class="page-tit"><?php echo e(__('Sell Tether (USDT)')); ?></h2>
        

        

        <div class="container-row">
            <ul class="plist" id="buy-list-container">
                <?php if(isset($listData) && $listData->count() > 0): ?>
                    <?php echo $__env->make('user.buy-list.partials.list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php else: ?>
                    <li class="plist__item">
                        <p class="text-center"><?php echo e(__('No data available')); ?></p>
                    </li>
                <?php endif; ?>
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
        });

        function loadMoreItems() {
            currentPage++;
            fetch(`<?php echo e(route('user.buy-list.index')); ?>?page=${currentPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    if (html.trim() === '') {
                        document.querySelector('.purchase-more').style.display = 'none';
                        return;
                    }
                    const container = document.getElementById('buy-list-container');
                    container.insertAdjacentHTML('beforeend', html);
                    addItemClickListeners();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<?php echo e(__('An error occurred while loading data. Please try again.')); ?>');
                });
        }

        function addItemClickListeners() {
            document.querySelectorAll('.plist__btn button, .plist__btn a').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/buy-list/index.blade.php ENDPATH**/ ?>