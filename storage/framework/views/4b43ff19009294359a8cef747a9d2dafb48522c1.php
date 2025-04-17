<div class="p-head">
    <div class="p-head__col mb flex-s-c">
        <button class="p-head__menu-btn"><img src="<?php echo e(asset('/public/pub')); ?>/img/p-head-mm.png"></button>
        <p class="p-head-tit"><?php echo e(__($page_title)); ?></p>
    </div>

    <div class="p-head__col flex-s-c">
        

        <div class="lang" style="width: 100%">
            <button id="languageToggle" class="lang-btn">
                <span class="txt">
                    <?php
                        $currentLang = Session::get('locale', 'ko');
                        echo $currentLang === 'ko' ? '한국어' : 'English';
                        
                    ?>
                </span>
            </button>
            <ul id="languageDropdown" class="lang-list" style="display: none;width:100px">
                <li><a href="#" class="btn-select-lang" data-lang="en">English</a></li>
                <li><a href="#" class="btn-select-lang" data-lang="ko">한국어</a></li>
            </ul>
        </div>

        <div class="alram">
            <button id="notification-icon" class="alram__btn">
                <img src="<?php echo e(asset('/public/pub')); ?>/img/notifications@2x.png">
            </button>
            <span class="alram__cnt hide"></span>
        </div>

        <div class="user-profile">
            <a href="<?php echo e(setRoute('user.profile.index')); ?>" class="user-profile__btn">
                <img src="<?php echo e(auth()->user()->userImage); ?>" alt="profile">
            </a>
        </div>
        
    </div>
</div>

<div class="push-wrapper" style="display: none;">
    <div class="push-header">
        <h5 class="title"><?php echo e(__('Notification')); ?></h5>
    </div>
    <ul class="push-list">
        <?php $__currentLoopData = get_user_notifications(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <div class="thumb">
                    <img src="<?php echo e(auth()->user()->userImage); ?>" alt="user">
                </div>
                <div class="content">
                    <div class="title-area">
                        <h5 class="title"><?php echo e($item->message->title); ?></h5>
                        <span class="time"><?php echo e($item->created_at->diffForHumans()); ?></span>
                    </div>
                    <span class="sub-title"><?php echo e($item->message->message); ?></span>
                </div>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>

<style>
    .alram__btn.has-notifications {
        animation: pulse 2s infinite;
    }

    .alram {
        position: relative;
        display: flex;
        align-items: center;
    }

    .alram__btn {
        width: 40px;
        height: 40px;
        /* background-color: #f0f0f0; */
        border: none;
        border-radius: 20%;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        /* transition: background-color 0.3s ease; */
    }

    /*
    .alram__btn:hover {
        background-color: #e0e0e0;
    } */

    .alram__btn img {
        width: 100%;
        height: 100%;
    }

    .alram__cnt {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ff4444;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        font-weight: bold;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .lang-list {
        z-index: 9999;
    }

    .user-profile {
        /* 프로필 컨테이너 스타일링 (필요한 경우) */
        display: inline-block;
        position: relative;
    }

    .user-profile__btn {
        display: block;
        width: 40px;
        /* 원하는 크기로 조정 */
        height: 40px;
        /* 원하는 크기로 조정 */
        overflow: hidden;
    }

    .user-profile__btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* 원형 이미지를 위한 스타일 */
        border-radius: 25%;

        /* 또는 약간 둥근 모서리를 위한 스타일 (원형 대신 이 줄을 사용) */
        /* border-radius: 8px; */

        transition: transform 0.3s ease;
        /* 호버 효과를 위한 트랜지션 */
    }

    /* 선택적: 호버 효과 */
    .user-profile__btn:hover img {
        transform: scale(1.2);
        border-radius: 25%;
        /* 호버 상태에서도 border-radius 유지 */
    }

    .sidebar-search-result {
        max-height: 300px;
        overflow-y: scroll;
    }

    .sidebar-search-result .single-item span {
        font-size: 14px !important;
    }

    .sidebar-search-box {
        position: absolute;
        top: 45px;
        left: 0;
        z-index: 2;
        width: 100%;
        background: white;
        box-shadow: 0 0 9px #00000026;
        border: 1px solid #c7cbd3;
        background-color: #fff;
        border-radius: 10px;
        padding: 5px 15px;
    }

    .sidebar-search-box .single-item a {
        display: block;
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
    }

    .sidebar-search-box .single-item a i {
        margin-right: 5px;
        color: rgba(255, 255, 255, 0.8);
    }

    @media only screen and (max-width: 575px) {
        .sidebar-search-box .single-item a i {
            font-size: 11px;
        }
    }

    .sidebar-search-box .single-item a span {
        font-size: 14px;
        font-weight: 500;
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
    }

    @media only screen and (max-width: 575px) {
        .sidebar-search-box .single-item a span {
            font-size: 11px;
        }
    }

    .sidebar-search-box .single-item a:hover span {
        color: var(--primary-color);
    }


    .push-wrapper {
        position: absolute;
        top: 47px;
        right: 0;
        width: 280px;
        background: white;
        box-shadow: 0 0 9px #00000026;
        border: 1px solid #c7cbd3;
        background-color: #fff;
        border-radius: 10px;
        padding: 5px 15px;
        z-index: 1000;
        text-align: left;
        transition: all 0.3s;
        -webkit-transition: all 0.3s;
        -webkit-transform-origin: top left;
        transform-origin: top left;
    }

    @media only screen and (max-width: 991px) {
        .push-wrapper {
            width: 250px;
        }
    }

    .push-wrapper.active {
        -webkit-transform: scaleX(1);
        transform: scaleX(1);
    }

    .push-wrapper .push-header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .push-wrapper .push-header .title {
        margin-bottom: 0;
    }

    .push-wrapper .push-header .sub-title {
        font-size: 12px;
    }

    .push-wrapper .push-list {
        margin-bottom: -15px;
    }

    .push-wrapper .push-list li {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        font-size: 12px;
        padding-bottom: 15px;
    }

    .push-wrapper .push-list li .thumb {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        overflow: hidden;
    }

    .push-wrapper .push-list li .thumb img {
        width: 100%;
        height: 100%;
        -o-object-fit: cover;
        object-fit: cover;
    }

    .push-wrapper .push-list li .content {
        width: calc(100% - 25px);
        padding-left: 10px;
    }

    .push-wrapper .push-list li .content .title-area {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .push-wrapper .push-list li .content .title {
        margin-bottom: 0;
    }

    .push-wrapper .push-list li .content .time {
        color: var(--primary-color);
        font-weight: 600;
    }

    .push-wrapper .push-footer {
        font-size: 12px;
    }

    .push-icon {
        width: 42px;
        height: 42px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        border: 1px solid #2c2566;
        border-radius: 10px;
        font-size: 20px;
    }
</style>

<script>
    $(document).ready(function() {

        // Language selector
        const languageToggle = document.getElementById('languageToggle');
        console.log(languageToggle);
       
        const languageDropdown = document.getElementById('languageDropdown');

        languageToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            languageDropdown.style.display = languageDropdown.style.display === 'block' ? 'none' :
                'block';
        });

        $('.btn-select-lang').click(function(e) {
            e.preventDefault();
            const selectedLang = $(this).data('lang');
            const languageText = selectedLang === 'ko' ? '한국어' : 'English';

            console.log(selectedLang);
            // AJAX를 사용하여 서버에 언어 변경 요청
            $.ajax({
                url: '<?php echo e(route('user.language.switch')); ?>',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    target: selectedLang
                },
                success: function(response) {
                    if(response == 0){
                        $('#languageToggle .txt').text(languageText);
                        location.reload();
                    } else {
                        alert('Error changing language');
                    }
                    
                },
                error: function(xhr, status, error) {
                    console.error("Error changing language:", error);
                }
            });

            languageDropdown.style.display = 'none';
        });

        // 드롭다운 외부 클릭 시 닫기
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.lang').length) {
                languageDropdown.style.display = 'none';
            }
        });

        // Notification
        document.getElementById('notification-icon').addEventListener('click', function() {
            var pushWrappers = document.getElementsByClassName('push-wrapper');
            for (var i = 0; i < pushWrappers.length; i++) {
                if (pushWrappers[i].style.display === 'none' || pushWrappers[i].style.display === '') {
                    pushWrappers[i].style.display = 'block';
                } else {
                    pushWrappers[i].style.display = 'none';
                }
            }
        });

        // push-wrapper 외부 클릭 시 닫기
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.push-wrapper').length && !$(e.target).closest(
                    '#notification-icon').length) {
                $('.push-wrapper').hide();
            }
        });

        // push-wrapper 내부 클릭 시 이벤트 전파 중단
        $('.push-wrapper').on('click', function(e) {
            e.stopPropagation();
        });

        // side-menu
        var header = document.querySelector('.header');

        // header 요소가 존재하는지 확인
        if (header) {
            // btn-mm 버튼에 이벤트 리스너 추가
            document.querySelector('.btn-mm').addEventListener('click', function() {
                // header 요소와 gnb 요소 선택
                var btnMm = this; // 이미 btn-mm 버튼을 가리키므로 this 키워드 사용
                var gnb = document.querySelector('.gnb');

                // btn-mm에 open 클래스가 있는지 확인하고, 있으면 제거, 없으면 추가
                if (btnMm.classList.contains('open')) {
                    btnMm.classList.remove('open');
                    gnb.classList.remove('open'); // gnb에서도 open 클래스 제거
                } else {
                    btnMm.classList.add('open');
                    gnb.classList.add('open'); // gnb에도 open 클래스 추가
                }
            });
        }

        document.querySelector('.p-head__menu-btn').addEventListener('click', function() {
            const body = document.body;

            body.classList.add('no-transition');

            body.offsetHeight;

            const isSideHide = body.classList.toggle('side-hide');

            body.offsetHeight;

            // Re-enable transitions
            body.classList.remove('no-transition');


            // AJAX request to update session
            fetch('<?php echo e(route('update.side.menu.session')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: JSON.stringify({
                        sideMenuState: isSideHide ? 'side-hide' : ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Session updated successfully');
                    } else {
                        console.error('Failed to update session');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });


    // $('#schAuto').on('input', function() {
    //     var input = $(this).val();
    //     if (input.length > 0) {
    //         $('.sch-inp').addClass('on-input');
    //     } else {
    //         $('.sch-inp').removeClass('on-input');
    //     }
    // });

    // // Delete search term
    // function delKey() {
    //     $('#schAuto').val('');
    //     $('.sch-inp').removeClass('on-input');
    // }
</script>
<?php /**PATH /var/www/html/resources/views/user/partials/top-nav.blade.php ENDPATH**/ ?>