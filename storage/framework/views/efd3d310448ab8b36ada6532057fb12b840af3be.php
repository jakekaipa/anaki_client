<?php
    $defualt = get_default_language_code() ?? 'en';
    $register_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::REGISTER_SECTION);
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $register = App\Models\Admin\SiteSections::getData($register_slug)->first();
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
?>
<?php $__env->startSection('content'); ?>
    <div class="full-container flex-c-c f8f8f8">
        <div class="join2">
            <form method="POST" action="<?php echo e(setRoute('user.register.submit')); ?>" onsubmit="return submitCheck();" >
                <?php echo csrf_field(); ?>
                <div class="form-box">
                    <div class="join-head">
                        <h1 class="join-tit"><?php echo e(__('sign_up')); ?></h1>
                    </div>
                    <!-- 레퍼럴 코드 -->
                    <input type='hidden' name='referralCode' value='<?php echo e($referralCode); ?>'>

                    <div class="i-txt uname w-full">
                        <label for="first_name" class="i-txt__label"><?php echo e(__('First Name')); ?></label>
                        <div class="i-txt__wrap">
                            <input type="text" id="first_name" name="first_name" class="i-txt__input" 
                                title="<?php echo e(__('please_enter_first_name')); ?>"
                                placeholder="<?php echo e(__('please_enter_first_name')); ?>"
                                value="<?php echo e(old('first_name')); ?>"
                                maxlength="9">
                            <button type="button" class="i-txt__del" title="<?php echo e(__('delete_input')); ?>"><img
                                    src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button>
                        </div>
                    </div>

                    <div class="i-txt uname w-full">
                        <label for="last_name" class="i-txt__label"><?php echo e(__('Last Name')); ?></label>
                        <div class="i-txt__wrap">
                            <input type="text" id="last_name" name="last_name" class="i-txt__input"
                                title="<?php echo e(__('please_enter_last_name')); ?>" placeholder="<?php echo e(__('please_enter_last_name')); ?>"
                                 value="<?php echo e(old('last_name')); ?>"
                                 maxlength="9">
                            <button type="button" class="i-txt__del" title="<?php echo e(__('delete_input')); ?>"><img
                                    src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button>
                        </div>
                    </div>

                    <div class="i-txt w-full">
                        <label for="email" class="i-txt__label"><?php echo e(__('email')); ?></label>
                        <div class="i-txt__wrap" style='display: flex;'>
                            <input type="text" id="email" name="email" class="i-txt__input" style='margin-right: 8px;' title="<?php echo e(__('please_enter_email')); ?>" placeholder="<?php echo e(__('email')); ?>" >
                            <button type='button' id='verificationCodeButton' class='btn-solid large' style='width:30%' onclick='sendEmailVerificationCode()'><span class='txt'><?php echo e(__('인증코드')); ?></span></button>
                            <input type='hidden' id='verificationCheck' value=0>
                        </div>
                    </div>

                    <div class="i-txt w-full" id='verificatioCode' style='display:none'>
                        <div class="i-txt__wrap" style='display: flex;'>
                            <input type="text" name="code" class="i-txt__input" style='margin-right: 6px;' title="<?php echo e(__('please_enter_email')); ?>" >
                            <!-- <button type="button" class="i-txt__del" title="<?php echo e(__('delete_input')); ?>"><img
                                    src="<?php echo e(asset('/public/pub')); ?>/img/elimination@2x.png"></button> -->
                            <button type='button' id='verificationButton' class='btn-solid large' style='width:18%' onclick='verificationCode()'><span class='txt'><?php echo e(__('인증')); ?></span></button>
                        </div>
                    </div>

                    <div class="i-txt password w-full">
                        <label for="pw" class="i-txt__label"><?php echo e(__('password')); ?></label>
                        <div class="i-txt__wrap">
                            <input type="password" id="pw" name="password" class="i-txt__input" 
                                title="<?php echo e(__('password_requirements')); ?>" placeholder="<?php echo e(__('password_requirements')); ?>"
                                value="<?php echo e(old('password')); ?>">
                            <button type="button" class="i-txt__btn-look" id="i-txt__btn-look-pw"
                                title="<?php echo e(__('show_password')); ?>">
                                <span class="txt blind"><?php echo e(__('show_password')); ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="i-txt password w-full">
                        <label for="pw2" class="i-txt__label"><?php echo e(__('confirm_password')); ?></label>
                        <div class="i-txt__wrap">
                            <input type="password" id="pw2" name="password1" class="i-txt__input" 
                                title="<?php echo e(__('please_reenter_password')); ?>"
                                placeholder="<?php echo e(__('please_reenter_password')); ?>"
                                value="<?php echo e(old('password1')); ?>">
                            <button type="button" class="i-txt__btn-look" id="i-txt__btn-look-pw2"
                                title="<?php echo e(__('show_password')); ?>">
                                <span class="txt blind"><?php echo e(__('show_password')); ?></span>
                            </button>
                        </div>
                    </div>
 

                    <div class="btn-wrap join-done flex-c-s">
                        <button type="submit" class="btn-solid small">
                            <span class="txt"><?php echo e(__('sign_up')); ?></span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="go-reg flex-c-s">
                <p class="info-txt"><?php echo e(__('already_a_member')); ?> <a href="<?php echo e(setRoute('user.login')); ?>"
                        class="point-color tdu"><?php echo e(__('login')); ?></a></p>
            </div>
        </div>
    </div><!-- //container End -->

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>

        function submitCheck(){
           
            var firstName           =  document.querySelector('#first_name').value; 
            var last_name           =  document.querySelector('#last_name').value;  
            var verificationCheck   =  document.querySelector('#verificationCheck').value;
            var pw                  =  document.querySelector('#pw').value;
            var pw2                 =  document.querySelector('#pw2').value;
            var email               =  document.querySelector('#email').value;

            if(!firstName){
                alert("<?php echo e(__('please_enter_first_name')); ?> ");
                return false;
            } else if(!last_name){
                alert("<?php echo e(__('please_enter_last_name')); ?> ");
                return false;
            } else if(!email){
                alert("<?php echo e(__('이메일을 입력 해주세요')); ?>");
                return false;     
            } else if(verificationCheck == 0){
                alert("<?php echo e(__('이메일 인증 해주세요')); ?> ");
                return false;
            } else if(!pw){
                alert("<?php echo e(__('Enter Password')); ?> ");
                return false;
            } else if(!pw2){
                alert("<?php echo e(__('Enter Confirm Password')); ?> ");
                return false;
            } else if(!validatePassword(pw)){
                alert("<?php echo e(__('password_requirements')); ?> ");
                return false;
            } else if (pw !== pw2){
                alert("<?php echo e(__('The password and confirmation password do not match.')); ?> ");
                return false;
            } 
        }

        // 비밀번호 체크
        function validatePassword(password){
            const regex = /^.{6,}$/;
            return regex.test(password);
        }

        // // 비밀번호 보기/숨기기 토글
        document.querySelector('#i-txt__btn-look-pw').addEventListener('click', function() {
            var passwordInput = document.querySelector('input[name="password"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('active');
            }
        });

        document.querySelector('#i-txt__btn-look-pw2').addEventListener('click', function() {
            var passwordInput = document.querySelector('input[name="password1"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('active');
            }
        });

        // 인증 이 메일 정송
        function sendEmailVerificationCode(){
            var email = document.querySelector('#email');
            if(!email || !email.value){
                alert("<?php echo e(__('이메일을 입력 해주세요')); ?>");  
                return false;
            }
            const emailRegex = /^(?!.*\.\.)[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
          
            if(!emailRegex.test(email.value))
            {
                alert("<?php echo e(__('이메일 정보를 잘못입력하였습니다')); ?>");  
                return false;
            }

            sendEmail(email.value);

        }

        // 이메일 발송
        function sendEmail(email){
                $.ajax({
                url:"<?php echo e(setRoute('user.register.send.email')); ?>",
                method: 'POST',
                data: {
                    email: email,
                    _token: '<?php echo e(csrf_token()); ?>' // CSRF 토큰
                },
                success: function(response) {
                    if(response == -1){ // 이메일 중복 일때
                        alert("<?php echo e(__('이미 가입된 이메일 입니다')); ?>");
                        return false;
                    } else {
                        alert("<?php echo e(__('Email sent successfully.')); ?>");

                        verificationCodeButton = document.querySelector('#verificationCodeButton'); 
                        verificationCodeButton.style.display  ='none';
                        
                        var email = document.querySelector('#email');
                        email.setAttribute('readonly', true);

                        verificatioCode = document.querySelector('#verificatioCode');
                        verificatioCode.style.display  = 'block';
                    }
                    
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON.message || '알 수 없는 오류가 발생했습니다.';
                    alert(errorMessage);
                }
            });
        }
        
        // 인증
        function verificationCode(){
            var code = document.querySelector('input[name="code"]').value;
            var email = document.querySelector('#email').value;
            
            if(!code){
                alert("<?php echo e(__('please Enter The Code')); ?>");
                return false;
            }

            $.ajax({
                url:"<?php echo e(setRoute('user.register.code.verification')); ?>",
                method: 'POST',
                data: {
                    code:code,
                    email:email,
                    _token: '<?php echo e(csrf_token()); ?>' // CSRF 토큰
                },
                success: function(response) {
                    if(response.message === 1){ // 코드 일치
                        alert("<?php echo e(__('Email verified successfully.')); ?>");
                        
                        var verificationCheck = document.querySelector('#verificationCheck');
                        verificationCheck.value = 1;
                        
                        var verificationButton = document.querySelector('#verificationButton');
                        verificationButton.style.display  ='none';
                        
                        var code = document.querySelector('[name="code"]');
                        code.setAttribute('readonly', true);
                        
                    } else if(response.message === -1) { // 코드 불일치
                        alert("<?php echo e(__('Code not found.')); ?>");    
                    } else {
                        alert("{{ __('Something went wrong. Please try again.')");
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON.message || '알 수 없는 오류가 발생했습니다.';
                    alert(errorMessage);
                }
            });
        }

        // // 입력 내용 삭제 버튼
        // document.querySelector('#pw').addEventListener('click', function() {
        //     document.querySelector('input[name="email"]').value = '';
        // });

        // // 입력 내용 삭제 버튼
        // document.querySelector('#pw2').addEventListener('click', function() {
        //     document.querySelector('input[name="email"]').value = '';
        // });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/user/auth/register.blade.php ENDPATH**/ ?>