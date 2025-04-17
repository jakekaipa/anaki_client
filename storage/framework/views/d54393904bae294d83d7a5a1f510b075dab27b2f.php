<?php
    $default = get_default_language_code();
    \Log::info(auth()->user()->two_factor_tether_transfer);
?>


<?php $__env->startSection('content'); ?>
    <div class="content dbl-sc">
        <h2 class="page-tit"><?php echo e(__($page_title)); ?></h2>

        <div class="container-row">
            <div class="dbl-sc-box">
                <strong class="cb-tit"><?php echo e(__('Two-Factor Authentication (2FA)')); ?></strong>

                <p class="sub-tit"><?php echo e(__('Set up 2FA to enhance your account security.')); ?></p>

                <div class="checkbox">
                    <input type="checkbox" id="checkbox" class="checkbox__chk" name="2FATetherTransfer"
                        <?php echo e(auth()->user()->two_factor_tether_transfer ? 'checked' : ''); ?>>
                    <label for="checkbox"
                        class="checkbox__label"><?php echo e(__('Use 2FA when transferring Tether from your wallet.')); ?></label>
                </div>

                <div class="auth-wrap">
                    <div class="auth-box">
                        <strong class="auth-tit"><?php echo e(__('Two Factor Authenticator')); ?></strong>

                        <div class="auth-img">
                            <img src="<?php echo e($qr_code); ?>"
                                alt="<?php echo e(__('Two-Factor Authentication QR Code')); ?>" 
                                title="<?php echo e(__('Scan this QR code with your authenticator app')); ?>"
                                id="qrCodeImage"
                            >
                        </div>

                        <p class="qr-guide"><?php echo e(__('Tap and hold the QR code to save it to your device.')); ?></p>

                        <div class="copy-txt-wrap">
                            <p class="copy-txt" id="twoFactorSecret"><?php echo e(auth()->user()->two_factor_secret); ?></p>

                            <button class="btn-copy" id="copyButton"><img src="<?php echo e(asset('/public/pub')); ?>/img/copy@2x.png"
                                    alt="<?php echo e(__('Copy to clipboard')); ?>"></button>
                        </div>

                        <p class="guide-txt">
                            <?php echo e(__("Do not forget to add the QR code or address to the Google Authenticator app. Once activated, a security code will be required for login and coin transfers from your wallet")); ?>

                        </p>

                        <div class="auth-btn">
                            <button class="btn-default active-deactive-btn">
                                <span
                                    class="txt"><?php echo e(auth()->user()->two_factor_status ? __('Disable') : __('Activate')); ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="auth-box">
                        <strong class="auth-tit"><?php echo e(__('Google Authenticator')); ?></strong>

                        <div class="auth-img">
                            <img src="<?php echo e(asset('public/frontend/')); ?>/images/element/play-store.png"
                                alt="Google Authenticator">
                        </div>

                        <p class="auth-txt"><?php echo e(__('Download Google Authenticator App')); ?></p>

                        <p class="guide-txt">
                            <?php echo e(__('It runs as a two-step authentication service to verify users using the authentication tool provided by Google')); ?>

                        </p>

                        <div class="auth-btn">
                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                class="btn-default">
                                <span class="txt"><?php echo e(__('Download App')); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(".active-deactive-btn").click(function() {
            const actionRoute = "<?php echo e(setRoute('user.security.google.2fa.status.update')); ?>";
            const target = 1;
            const btnText = $(this).text();
            const message =
                `<?php echo e(__('Are you sure you want to change your two-factor authentication settings (Powered by Google)?')); ?>`;
            openAlertModal(actionRoute, target, message, btnText, "POST");
        });

        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copyButton');
            const twoFactorSecret = document.getElementById('twoFactorSecret');
            const qrCodeImage = document.getElementById('qrCodeImage');

            copyButton.addEventListener('click', function() {
                // Copy the selected text to clipboard
                navigator.clipboard.writeText(twoFactorSecret.textContent)
                    .then(() => {
                        // Notify user of successful copy
                        alert('<?php echo e(__('Two-factor secret copied to clipboard!')); ?>');
                    })
                    .catch(err => {
                        // Error message if copy fails
                        console.error('Failed to copy: ', err);
                        alert('<?php echo e(__('Failed to copy the secret. Please try again.')); ?>');
                    });
            });

            function downloadImage(imgSrc) {
                const link = document.createElement('a');
                link.href = imgSrc;
                link.download = 'QR_Code.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            qrCodeImage.addEventListener('click', function() {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    // Mobile device
                    alert('<?php echo e(__('To save the QR code, press and hold the image, then select "Save Image".')); ?>');
                } else {
                    // Desktop device
                    downloadImage(this.src);
                }
            });

            // Update checkbox state based on two_factor_status
            const checkbox = document.getElementById('checkbox');
            checkbox.checked = <?php echo e(auth()->user()->two_factor_tether_transfer ? 'true' : 'false'); ?>;

            // Add event listener to checkbox
            checkbox.addEventListener('change', function() {
                // Here you can add AJAX call to update the two_factor_status in the backend
                // For now, we'll just log the change
                console.log('two_factor_tether_transfer status changed:', this.checked);

                fetch("<?php echo e(route('user.security.google.2fa.tether-transfer')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            two_factor_tether_transfer: this.checked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(
                                'Two-factor authentication for Tether transfer updated successfully'
                            );
                        } else {
                            console.error(
                                'Failed to update two-factor authentication for Tether transfer');
                            // Optionally revert the checkbox state if the update failed
                            this.checked = !this.checked;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Optionally revert the checkbox state if there was an error
                        this.checked = !this.checked;
                    });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/sections/security/google-2fa.blade.php ENDPATH**/ ?>