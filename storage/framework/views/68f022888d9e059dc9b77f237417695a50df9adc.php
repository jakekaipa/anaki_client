<?php $__empty_1 = true; $__currentLoopData = $listData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <?php
            if ($item->offer_user_id == auth()->user()->id) {
                $tradingDirection = $item->order_type;
                $dealerName = ($item->client_user->realname)? $item->client_user->realname : $item->client_user->username;
            } else {
                if ($item->order_type == 'buy') {
                    $tradingDirection = 'sell';
                } else {
                    $tradingDirection = 'buy';
                }

                $dealerName = ($item->offer_user->realname)? $item->offer_user->realname : $item->offer_user->username;
            }

            $nowText = now()->format('Y-m-d H:i:s');
            $isOngoing = $item->state == 'open' && $item->ended_at >= $nowText;

            $style = '';
            $statusMsg = '';

            if ($item->state == 'open' || $item->state == 'send') {
                $style = 'going';
                $statusMsg = __('Ongoing');
            } elseif ($item->state == 'dispute') {
                $style = 'going';
                $statusMsg = __('분쟁중');
            } elseif ($item->state == 'done') {
                $style = 'complete';
                $statusMsg = __('Complete');
            } elseif ($item->state == 'cancel') {
                $style = 'cancel';
                $statusMsg = __('Cancel');
            } elseif ($item->state == 'dispute-solved') {
                $style = 'complete';
                $statusMsg = __('분쟁 해결됨');
            }
        ?>
        <td><?php echo e(__($tradingDirection)); ?></td>
        <td><?php echo e($dealerName); ?></td>
        <td><?php echo e(number_format($item->tetherAmount, 2)); ?> <span class="unit">USDT</span></td>
        <td><?php echo e(number_format($item->totalPayAmount)); ?> <span class="unit"><?php echo e(__('KRW')); ?></span></td>
        <td><?php echo e(number_format($item->price)); ?> <span class="unit"><?php echo e(__('KRW')); ?></span></td>
        <td>
            <span class="condition <?php echo e($style); ?>"> <?php echo e($statusMsg); ?> </span>
        </td>
        <td>
            <?php
                $creationDateTime = new DateTime($item->created_at);
                $creationDateTime->setTimezone($curTimeZone);
                echo $creationDateTime->format('Y-m-d H:i');
            ?>
        </td>
        <td>
            <?php
                if ($item->ended_at) {
                    $endTime = new DateTime($item->ended_at);
                    $endTime->setTimezone($curTimeZone);
                    echo $endTime->format('Y-m-d H:i');
                } else {
                    echo '-';
                }
            ?>
        </td>
        <td>
            <a href="#" class="go-detail"
                onclick="event.preventDefault(); showTradeDetails('<?php echo e($item->id); ?>', '<?php echo e(urlSafeEncrypt($item->id)); ?>');">
                <span class="txt"><?php echo e(__('View')); ?></span>
            </a>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="9">
            <div class="deal-empty">
                <p class="empty-txt"><?php echo e(__('No Records Found')); ?></p>
            </div>
        </td>
    </tr>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/user/my-trade/partials/list.blade.php ENDPATH**/ ?>