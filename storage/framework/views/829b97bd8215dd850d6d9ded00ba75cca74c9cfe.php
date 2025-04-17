<?php
    $default = get_default_language_code() ?? 'en';
    $login_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $login = App\Models\Admin\SiteSections::getData($login_slug)->first();
    $register_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::REGISTER_SECTION);
    $register = App\Models\Admin\SiteSections::getData($register_slug)->first();
?>
<?php $__env->startSection('content'); ?>
<div class="full-container flex-c-c f8f8f8">
<div class="login">
        <div class="form-box">
            <div class="login__head" style="position: relative;">
                <div class="language-selector" style="position: absolute; top: -40px; right: 0;">
                    <select class="lang-select" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;" onchange=changeLang(this.value)>
                        <option value="en"  <?php echo e(($selectLang === 'en')? 'selected':''); ?>>English</option>
                        <option value="ko" <?php echo e(($selectLang === 'ko')? 'selected':''); ?>>한국어</option>
                    </select>
                </div> 
                <h1 class="logo"><img src="<?php echo e(asset('/public/pub')); ?>/img/logo-basics@2x.png" alt="anaki"></h1>
            </div>

                <form action="<?php echo e(setRoute('user.login.submit')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="text" hidden name="connectInfo" id="connectInfo">

                    <div class="i-txt email w-full">
                        <div class="i-txt__wrap">
                            <input type="text" class="i-txt__input" name="email" title="이메일 입력하세요" placeholder="<?php echo e(__('이메일')); ?>">
                            <button type="button" class="i-txt__del" title="입력내용 삭제"><img
                                    src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button>
                        </div>
                    </div>
                   
                    <div class="i-txt password w-full">
                        <div class="i-txt__wrap">
                            <input type="password" class="i-txt__input" name="password" title="비밀번호 입력하세요"
                                placeholder="<?php echo e(__('Password')); ?>">
                            <button type="button" class="i-txt__btn-look" title="비밀번호 보기 실행">
                                <span class="txt blind">비밀번호 보기</span>
                            </button>
                        </div>
                    </div>

                    <div class="login-find flex-c-s">
                        <a href="<?php echo e(setRoute('user.password.forgot')); ?>" class="login-find-link"><?php echo e(__('Find Password')); ?></a>
                    </div>

                    <div class="btn-wrap btn-login">
                        <button type="submit" class="btn-other w-full">
                            <span class="txt"><?php echo e(__('login')); ?></span>
                        </button>
                    </div>
                </form>

                <div class="esay-login">
                    <span class="txt"><?php echo e(__('간편 로그인')); ?></span>
                </div>

                <div class="ss-link">
                    
                    <a href="#" class="ss-link-a" title="구글 로그인" onclick="oauthLogin('google')"><img
                            src="<?php echo e(asset('/public/pub')); ?>/img/google@2x.png"></a>
                    
            </div>

            <div class="go-reg flex-c-s">
                <p class="info-txt"><?php echo e(__('아직 회원이 아니신가요?')); ?> <a href="<?php echo e(setRoute('user.register')); ?>" class="point-color tdu"><?php echo e(__('Sign Up')); ?></a>
                </p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <style>
        /* 기존 스타일 유지 */
        .ss-link {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .ss-link-a img {
            width: 40px;
            height: 40px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone-with-data.js"></script>

    <script>
        jQuery.get("https://ipinfo.io", function(response) {
            var connectInfo = document.getElementById("connectInfo");
            connectInfo.value = JSON.stringify({
                city: response.city,
                country: response.country,
                ip: response.ip,
                location: response.loc,
                internet_provider: response.org,
                postal: response.postal,
                region: response.region,
                timezone: response.timezone
            });
        }, "jsonp");

        function changeLang(value) {
            location.href='/login?selectLang='+value;
        }

        function oauthLogin(provider) {
            var urlSessionData = <?php echo json_encode(Session::get('url'), 15, 512) ?>;
            var intendedUrl = urlSessionData?.intended;

            if (intendedUrl) {
                window.location.href = `/api/v1/user/login/${provider}?intended=${intendedUrl}`;
            } else {
                window.location.href = `/api/v1/user/login/${provider}`;
            }
        }

        // 비밀번호 보기/숨기기 토글
        document.querySelector('.i-txt__btn-look').addEventListener('click', function() {
            var passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('active');
            }
        });

        // 입력 내용 삭제 버튼
        document.querySelector('.i-txt__del').addEventListener('click', function() {
            document.querySelector('input[name="email"]').value = '';
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/auth/login.blade.php ENDPATH**/ ?>