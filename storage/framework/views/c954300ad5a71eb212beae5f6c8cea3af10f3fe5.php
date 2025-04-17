<?php 
   $clickUrl =  (explode("admin/",request()->path())[1]); 
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- 대쉬보드 -->
                <a class="nav-link" href="/admin/dashboard">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <!-- 통계 -->
                <a class="nav-link collapsed <?php echo e(strpos($clickUrl, 'data') !== false ? 'active' : ''); ?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    통계
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse  <?php echo e(strpos($clickUrl, 'data') !== false ? 'show' : ''); ?>" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?php echo e(strpos($clickUrl, 'regist-user') !== false ? 'active' : ''); ?>" href="/admin/data/regist-user">사용자</a>
                        <a class="nav-link <?php echo e(strpos($clickUrl, 'trade-amount') !== false ? 'active' : ''); ?>" href="/admin/data/trade-amount">거래 금액</a>
                        <a class="nav-link <?php echo e(strpos($clickUrl, 'trade-count') !== false ? 'active' : ''); ?>" href="/admin/data/trade-count">거래 건수</a>
                    </nav>
                </div>
               
                <!-- 관리 메뉴 -->
                <a class="nav-link collapsed <?php echo e(strpos($clickUrl, 'manage') !== false ? 'active' : ''); ?>" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="<?php echo e(strpos($clickUrl, 'manage') !== false ? 'true' : 'false'); ?>" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                        관리
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php echo e(strpos($clickUrl, 'manage') !== false ? 'show' : ''); ?>" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link <?php echo e(strpos($clickUrl, 'user') !== false ? 'active' : ''); ?>" href="/admin/manage-user/">사용자</a>
                        <a class="nav-link <?php echo e(strpos($clickUrl, 'trade') !== false ? 'active' : ''); ?>" href="/admin/manage-trade/internal-list">거래</a>
                    </nav>
                </div>

                <!-- 공지 -->
                <a class="nav-link <?php echo e(strpos($clickUrl, 'manage-notice') !== false ? 'active' : ''); ?>" href="/admin/manage-notice/">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                    공지 및 알림
                </a>

                <!-- 고객 문의 -->
                <a class="nav-link <?php echo e(strpos($clickUrl, 'manage-cs') !== false ? 'active' : ''); ?>" href="/admin/manage-cs/">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    고객문의
                </a>

            </div>
        </div>
        <div class="sb-sidenav-footer">
            관리자 페이지   
        </div>
    </nav>

<script>
    // 메뉴 클릭 시, 클릭한 메뉴 항목의 클래스 이름을 로컬스토리지에 저장
document.querySelectorAll('.nav-link').forEach(function (link) {
    link.addEventListener('click', function (event) {
        // 기존의 active 클래스 제거
        document.querySelectorAll('.nav-link').forEach(function (el) {
            el.classList.remove('active');
        });

        // 클릭한 항목에 active 클래스 추가
        link.classList.add('active');

        // 클릭한 항목의 class 이름을 로컬스토리지에 저장
        const menuClass = link.classList[1]; // 클래스 이름을 'static', 'manage', 'notice', 'cs'로 저장
        if (menuClass) {
            localStorage.setItem('activeMenuClass', menuClass);
        }

        // 'collapsed' 상태인 메뉴의 펼침 여부를 로컬스토리지에 저장
        if (link.getAttribute('data-bs-toggle') === 'collapse') {
            const target = document.querySelector(link.getAttribute('data-bs-target'));
            if (target) {
                const isExpanded = target.classList.contains('show');
                localStorage.setItem(target.id + '-expanded', isExpanded);
            }
        }
    });
});

// 페이지 로드 시 로컬스토리지에서 활성화된 메뉴 클래스 찾기
window.addEventListener('DOMContentLoaded', function () {
    const activeMenuClass = localStorage.getItem('activeMenuClass');
    if (activeMenuClass) {
        document.querySelectorAll('.nav-link').forEach(function (link) {
            // 메뉴 항목의 class 이름이 로컬스토리지의 activeMenuClass와 일치하는 항목을 찾음
            if (link.classList.contains(activeMenuClass)) {
                link.classList.add('active');

                

                // 'collapsed' 상태인 메뉴가 있으면 펼치기
                if (link.getAttribute('data-bs-toggle') === 'collapse') {
                    const target = document.querySelector(link.getAttribute('data-bs-target'));
                    if (target) {
                        // 로컬스토리지에서 펼침 여부를 확인하여 상태 설정
                        const isExpanded = localStorage.getItem(target.id + '-expanded') === 'true';
                        if (isExpanded) {
                            const collapseInstance = new bootstrap.Collapse(target, { toggle: true });
                        }
                    }
                }
            }
        });
    }
});
</script><?php /**PATH /var/www/html/resources/views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>