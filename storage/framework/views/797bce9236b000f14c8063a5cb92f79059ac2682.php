<style>
    #notification-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
    }

    .custom-notification {
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        color: white;
        font-family: Arial, sans-serif;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .custom-notification.show {
        opacity: 1;
    }

    .custom-notification.error {
        background-color: #f44336;
        /* background-color: white; */
    }

    .custom-notification.success {
        background-color: #4CAF50;
        /* background-color: white; */
    }

    .custom-notification.warning {
        background-color: #ff9800;
        /* background-color: white; */
    }

    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
    }
</style>

<div id="notification-container"></div>

<script>
    function showNotification(message, type) {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `custom-notification ${type}`;
        notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
    `;
        container.appendChild(notification);

        // Trigger reflow to enable transition
        notification.offsetHeight;
        notification.classList.add('show');

        // 자동으로 알림 제거
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('error')): ?>
            <?php if(is_array(session('error'))): ?>
                <?php $__currentLoopData = session('error'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    showNotification("<?php echo e(__($item)); ?>", "error");
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php elseif(session('success')): ?>
            <?php if(is_array(session('success'))): ?>
                <?php $__currentLoopData = session('success'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    showNotification("<?php echo e(__($item)); ?>", "success");
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php elseif(session('warning')): ?>
            <?php if(is_array(session('warning'))): ?>
                <?php $__currentLoopData = session('warning'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    showNotification("<?php echo e(__($item)); ?>", "warning");
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php elseif($errors->any()): ?>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                showNotification("<?php echo e(__($item)); ?>", "error");
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    });
</script>
<?php /**PATH /var/www/html/resources/views/user/partials/notify.blade.php ENDPATH**/ ?>