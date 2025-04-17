<?php $__empty_1 = true; $__currentLoopData = $listData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <li class="plist__item" data-href="<?php echo e(setRoute('user.buy-list.preview', urlSafeEncrypt($item->id))); ?>">
        <div class="plist__head">
            <div class="plist__head-col">
                <div class="flex-s-c">
                    <p class="plist__head-txt"><?php echo e(__('Buyer')); ?></p>
                    <span class="plist__head-txt-outline"><?php echo e($item->offerLabel); ?></span>
                </div>
            </div>
            <div class="plist__head-col">
                <div class="flex-s-c">
                    <span class="plist__head-txt dn"><?php echo e(__('제안단가')); ?></span>
                </div>
            </div>
        </div>
        <div class="plist__info">
            <div class="plist__col">
                <div class="plist__auth">
                    <span class="plist__auth1"><?php echo e(__('Buyer')); ?></span>
                    <span class="plist__auth2"><?php echo e($item->user->username); ?></span>
                    <span
                        class="plist__auth3 <?php echo e($item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? 'cf' : 'uncf'); ?>">
                        <?php echo e($item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified')); ?>

                    </span>
                </div>
            </div>
            <div class="plist__col">
                <div class="plist__value-wrap">
                    <span class="plist__value">
                        <?php echo e(number_format($item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice)); ?>

                        <?php echo e(__('KRW')); ?>

                    </span>
                    <?php if($item->priceType == 0): ?>
                        <span
                            class="plist__arrow <?php echo e($item->offerMargin >= 0 ? 'up' : 'down'); ?>"><?php echo e(abs($item->offerMargin)); ?>%</span>
                    <?php endif; ?> 
                </div> 
            </div>
            <div class="plist__col w-full">
                <div class="plist__how-wrap">
                    <?php if(isset($item->bankName) && strlen(trim($item->bankName)) > 0): ?>
                        <span class="plist__how transfer"><?php echo e(__('bank_transfer')); ?></span>
                    <?php endif; ?>
                    <?php if(isset($item->payQR) && strlen(trim($item->payQR)) > 0): ?>
                        <span class="plist__how qr"><?php echo e(__('Qr Pay')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="plist__col">
                <span class="plist__ttl"><?php echo e(__('trade_time_limit')); ?>: <?php echo e($item->offerTimeLimit); ?><?php echo e(__('minutes')); ?></span>
                <span class="plist__log"><?php echo e(__('last_access')); ?>:
                    <?php
                        $diff = $now->diff(new DateTime($item->user->updated_at));
                        if ($diff->days == 0 && $diff->h == 0 && $diff->i < 3) {
                            echo __('currently_logged_in');
                        } else {
                            $time = '';
                            if ($diff->days > 0) {
                                $time = trans_choice('days_ago', $diff->days, ['value' => $diff->days]);
                            } elseif ($diff->h > 0) {
                                $time = trans_choice('hours_ago', $diff->h, ['value' => $diff->h]);
                            } elseif ($diff->i > 0) {
                                $time = trans_choice('minutes_ago', $diff->i, ['value' => $diff->i]);
                            } else {
                                $time = __('just_now');
                            }
                            echo $time;
                        }
                    ?>
                </span>
            </div>
            <div class="plist__col">
                <ul class="limit">
                    <li><?php echo e(__('min_purchase')); ?>: <?php echo e(number_format($item->tradeVolMin)); ?> <?php echo e(__('KRW')); ?></li>
                    <li><?php echo e(__('max_purchase')); ?>: <?php echo e(number_format($item->tradeVolMax)); ?> <?php echo e(__('KRW')); ?></li>
                </ul>
            </div>
            <div class="plist__btn">
                <?php if($item->password != null): ?>
                    <div class="plist__deal" style="pointer-events: none; cursor: default;"><span class="txt"><?php echo e(__('Private Trade')); ?></span></div>
                <?php endif; ?>
                <a href="<?php echo e(setRoute('user.buy-list.preview', urlSafeEncrypt($item->id))); ?>" class="plist__sell">
                    <span class="txt"><?php echo e(__('Sell')); ?></span>
                </a>
            </div>
        </div>
    </li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <li class="plist__item">
        <h3 class="text-warning text-center"><?php echo e(__('no_data_found')); ?></h3>
    </li>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/user/buy-list/partials/list.blade.php ENDPATH**/ ?>