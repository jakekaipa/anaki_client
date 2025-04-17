@php
    $defualt = get_default_language_code() ?? 'en';
    $default_lng = 'en';
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
@endphp

<div class="header-inner">
    <div class="header-head">
        <h1 class="logo big"><a href="/"><img src="{{ asset('/public/pub') }}/img/logo-basics@2x.png"
                    alt="anaki"></a></h1>
        <h1 class="logo small"><a href="/"><img src="{{ asset('/public/pub') }}/img/logo-symbol@2x.png"></a></h1>

        <button class="btn-mm mo-b" title="{{ __('Open Main Menu') }}">
            <span class="blind">{{ __('Open Main Menu') }}</span>
        </button>
    </div>

    <nav class="gnb">
        <ul class="gnb__list">
            <li class="gnb__item ico1 {{ menuActive('user.dashboard') }}"
                onclick="window.location.href='{{ setRoute('user.dashboard') }}'">
                <a class="gnb__btn">{{ __('Dashboard') }}</a>
            </li>
            <li class="gnb__item ico2 {{ menuActive('user.sell-list.index') }}"
                onclick="window.location.href='{{ setRoute('user.sell-list.index') }}'">
                <a class="gnb__btn">{{ __('구매하기') }}</a>
            </li>
            <li class="gnb__item ico3 {{ menuActive('user.buy-list.index') }}"
                onclick="window.location.href='{{ setRoute('user.buy-list.index') }}'">
                <a class="gnb__btn">{{ __('판매하기') }}</a>
            </li>
            <li class="gnb__item ico4 {{ menuActive('user.make-offer.index') }}"
                onclick="window.location.href='{{ setRoute('user.make-offer.index') }}'">
                <a class="gnb__btn">{{ __('거래 만들기') }}</a>
            </li>
            <li class="gnb__item ico5 {{ menuActive('user.mytrade.index') }}"
                onclick="window.location.href='{{ setRoute('user.mytrade.index') }}'">
                <a class="gnb__btn">{{ __('My Transactions') }}</a>
            </li>
            <li class="gnb__item ico6 {{ menuActive('user.wallet.index') }}"
                onclick="window.location.href='{{ setRoute('user.wallet.index') }}'">
                <a class="gnb__btn">{{ __('My Wallet') }}</a>
            </li>
            <li class="gnb__item ico7 {{ menuActive('user.authorize.kyc') }}"
                onclick="window.location.href='{{ setRoute('user.authorize.kyc') }}'">
                <a class="gnb__btn">{{ __('KYC Verification') }}</a>
            </li>
            <li class="gnb__item ico8" onclick="window.location.href='{{ setRoute('user.security.google.2fa') }}'">
                <a class="gnb__btn">{{ __('2단계인증') }}</a>
            </li>
           
            <li class="gnb__item ico10" style='background-image:url(/build/assets/speaker.png)' onclick="window.location.href='/user/notice'">
                <a class="gnb__btn" >
                   {{ __('공지사항') }}
                </a>
            </li>

            <li class="gnb__item ico9" onclick="handleLogout()">
                <a class="gnb__btn logout-btn">{{ __('Logout') }}</a>
            </li>
        </ul>

        <a href="#" class="hd-cs" onclick="window.location.href='{{ setRoute('user.cs.index') }}'">
            <strong class="cs-tit">{{ __('Customer Service') }}</strong>
            <p class="cs-txt">{{ __('무엇을 도와드릴까요?') }}</p>
        </a>
        <!-- <a href="#" class="hd-cs" style='width:50%;background-image:url(/build/assets/speaker.png);background-repeat: no-repeat;background-position: calc(100% - 16px) calc(100% - 19px);background-size: 72px;' onclick="window.location.href='{{ setRoute('user.cs.index') }}'">
            <strong class="cs-tit">{{ __('공지사항') }}</strong>
            <p class="cs-txt">{{ __('무엇을 도와드릴까요?') }}</p>
        </a> -->
    </nav>
</div>

<script>
    function handleLogout() {
        if (confirm("{{ __('Are you sure you want to logout?') }}")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ setRoute('user.logout') }}";
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
        }
    }

    // document.addEventListener('DOMContentLoaded', function() {
    //     const menuItems = document.querySelectorAll('.gnb__item');

    //     menuItems.forEach(item => {
    //             item.addEventListener('click', function(e) {
    //                     alert('click');
    //                     const link = this.querySelector('a');
    //                     if (link && link.href) {
    //                         if (link.classList.contains('logout-btn')) {
    //                             e.preventDefault();
    //                             // Add your logout logic here
    //                             console.log('Logout clicked');
    //                             // For example: window.location.href = '/logout';
    //                             if (confirm("{{ __('Are you sure you want to logout?') }}")) {
    //                                 const form = document.createElement('form');
    //                                 form.method = 'POST';
    //                                 form.action = "{{ setRoute('user.logout') }}";
    //                                 form.style.display = 'none';

    //                                 const csrfToken = document.createElement('input');
    //                                 csrfToken.type = 'hidden';
    //                                 csrfToken.name = '_token';
    //                                 csrfToken.value = "{{ csrf_token() }}";
    //                                 form.appendChild(csrfToken);

    //                                 document.body.appendChild(form);
    //                                 form.submit();
    //                             }

    //                         } else {
    //                             window.location.href = link.href;
    //                         }
    //                     }

    //                     // alert('a');
    //                     // // header 요소와 gnb 요소 선택
    //                     // var btnMm = document.querySelector(
    //                     //     '.btn-mm'); // 이미 btn-mm 버튼을 가리키므로 this 키워드 사용
    //                     // var gnb = document.querySelector('.gnb');

    //                     // btnMm.classList.remove('open');
    //                     // gnb.classList.remove('open'); // gnb에서도 open 클래스 제거
    //                 }
    //             });

    //     });

    /**
     * Function for search user panel sidebar menu item
     */
    // function sideBarSearch() {
    //     const menuLinks = document.querySelectorAll(".gnb__list .gnb__item a");
    //     const filterMenuItem = Array.from(menuLinks).filter(item => item.getAttribute("href") !== "#");

    //     const searchInput = document.getElementById("schAuto");
    //     const searchResultContainer = document.querySelector(".sidebar-search-result");

    //     searchInput.addEventListener("keyup", function() {
    //         sideBarSearchWithInput(this, filterMenuItem);
    //     });

    //     // focus 이벤트 리스너 추가
    //     searchInput.addEventListener('focus', function() {
    //         searchResultContainer.style.display = 'block';
    //         sideBarSearchWithInput(this, filterMenuItem);
    //     });

    //     searchInput.addEventListener('blur', function() {
    //         // 약간의 지연을 주어 다른 요소를 클릭할 시간을 줍니다.
    //         setTimeout(() => {
    //             searchResultContainer.style.display = 'none';
    //         }, 200);
    //     });
    // }

    // function sideBarSearchWithInput(input, navItems) {
    //     const inputValue = input.value.toLowerCase();
    //     const searchResult = navItems.filter(item => {
    //         const title = item.textContent.toLowerCase();
    //         return title.includes(inputValue);
    //     });

    //     const searchResultContainer = document.querySelector(".sidebar-search-result");
    //     searchResultContainer.innerHTML = "";

    //     if (searchResult.length > 0) {
    //         const sidebarSearchBox = document.createElement('div');
    //         sidebarSearchBox.className = 'sidebar-search-box';

    //         searchResult.forEach(item => {
    //             const link = item.getAttribute("href");
    //             const title = item.textContent;
    //             const iconClass = item.closest('.gnb__item').className.split(' ')[
    //                 1]; // Assuming the icon class is the second class

    //             const singleItem = document.createElement('div');
    //             singleItem.className = 'single-item';
    //             singleItem.innerHTML = `
    //                 <a href="${link}">
    //                     <i class="${iconClass}"></i>
    //                     <span style="position:inherit">${title}</span>
    //                 </a>
    //             `;
    //             sidebarSearchBox.appendChild(singleItem);
    //         });

    //         searchResultContainer.appendChild(sidebarSearchBox);
    //     }
    // }

    // sideBarSearch();

    // window.handleSearch = function(event) {
    //     event.preventDefault();
    //     const searchInput = document.getElementById("schAuto");
    //     const searchResult = document.querySelector(".sidebar-search-result .single-item a");

    //     if (searchResult) {
    //         window.location.href = searchResult.getAttribute("href");
    //     }
    //     return false;
    // };

    // // Logout functionality
    // const logoutBtn = document.querySelector('.logout-btn');
    // logoutBtn.addEventListener('click', function(e) {
    //     e.preventDefault();
    //     if (confirm("{{ __('Are you sure you want to logout?') }}")) {
    //         const form = document.createElement('form');
    //         form.method = 'POST';
    //         form.action = "{{ setRoute('user.logout') }}";
    //         form.style.display = 'none';

    //         const csrfToken = document.createElement('input');
    //         csrfToken.type = 'hidden';
    //         csrfToken.name = '_token';
    //         csrfToken.value = "{{ csrf_token() }}";
    //         form.appendChild(csrfToken);

    //         document.body.appendChild(form);
    //         form.submit();
    //     }
    // });
    // });
</script>

<style>
    #side-menu {
        transition: transform 0.3s ease-in-out;
    }

    #side-menu.menu-closed {
        transform: translateX(-100%);
    }
</style>
