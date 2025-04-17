<!DOCTYPE html>
<html lang="<?php echo e(get_default_language_code()); ?>">
<head>
    <!-- 로그인 페이지 레이아웃 -->
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!--Seo -->
    <title>Anaki global Peer-to-Peer Tether Marketplace</title>
    <!-- <meta name="description" content="ANAKI is a P2P trading platform dedicated to Tether (USDT) that supports user-to-user direct transactions safely and quickly with a secure escrow trading system and a variety of security features." />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="ANAKI is a P2P trading platform" /> 
    <meta property="og:description" content="ANAKI is a P2P trading platform dedicated to Tether (USDT) that supports user-to-user direct transactions safely and quickly with a secure escrow trading system and a variety of security features."/>
    <meta property="og:url" content="https://anakip2p.com/main-landing" />
    <meta property="og:site_name" content="ANAKI" />
    <meta property="og:image" content="https://anakip2p.com/public/pub/img/logo-basics@2x.png" />
    <link rel="canonical" href="https://anakip2p.com/main-landing" />
    <meta name="naver-site-verification" content="3d3546f5382c53ca24025ae6e19d2876250f8351" />
    <meta name="google-site-verification" content="uF7efhLPfozYKqbl0JMyDdQDJRdgzkP02jkThefZZsc" /> -->
    <!--end Seo -->
    <!-- <link rel="icon" href="/backend/images/icon/favicon.png?v=<?php echo e(time()); ?>" type="image/png">
    <link rel="shortcut icon" href="/backend/images/icon/favicon.png?v=<?php echo e(time()); ?>" type="image/png"> -->
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo e($basic_settings->sitename(__($page_title??''))); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>

    <?php echo $__env->yieldPushContent('css'); ?>
</head>
<body>

<?php echo $__env->yieldContent("content"); ?>

<?php echo $__env->make('partials.footer-asset', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('script'); ?>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/frontend/layouts/auth.blade.php ENDPATH**/ ?>