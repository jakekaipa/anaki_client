<div class="dashboard-path">
    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="main-path"><a href="<?php echo e($item['url']); ?>"><?php echo e($item['name']); ?></a></span>
        <?php if(request()->routeIs('user.dashboard')): ?>
        <?php else: ?>
        <i class="las la-angle-right" ></i>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(request()->routeIs('user.dashboard')): ?>
    <?php else: ?>
      <span class="active-path "><?php echo e($active ?? ""); ?></span>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/html/resources/views/user/components/breadcrumb.blade.php ENDPATH**/ ?>