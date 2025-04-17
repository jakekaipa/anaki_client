<?php
    $defualt = get_default_language_code() ?? 'en';
    $login_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $login = App\Models\Admin\SiteSections::getData($login_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
?>
<?php $__env->startSection('content'); ?>
    <div class="full-container flex-c-c f8f8f8">
        <div class="find-pi">
            <div class="join-head">
                <h1 class="join-tit"><?php echo e(__('Find Password')); ?></h1>
                <p class="join-tit-sub"><?php echo e(__('We will send a password reset link to your email.')); ?></p>
            </div>

            <form class="account-form" action="<?php echo e(setRoute('user.password.forgot.send.code')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="i-txt w-full">
                    <div class="i-txt__wrap">
                        <input type="email" class="i-txt__input" name="credentials" title="<?php echo e(__('Enter your email')); ?>"
                            placeholder="<?php echo e(__('Enter your email')); ?>" required>
                        <button type="button" class="i-txt__del" title="<?php echo e(__('Delete input')); ?>"><img
                                src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button>
                    </div>
                </div>

                <div class="btn-wrap find-btn flex-c-s">
                    <button type="submit" class="btn-other w-full">
                        <span class="txt"><?php echo e(__('Find Password')); ?></span>
                    </button>
                </div>
            </form>

            <div class="go-login flex-c-s">
                <a href="<?php echo e(setRoute('user.login')); ?>" class="info-txt tdu"><?php echo e(__('Back to login screen')); ?></a>
            </div>
        </div>
    </div><!-- //container End -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            $('.i-txt__del').on('click', function() {
                $(this).siblings('input').val('');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/auth/forgot-password/forgot.blade.php ENDPATH**/ ?>