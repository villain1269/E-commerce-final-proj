<?php if(session('status')): ?>
    <div id="toast" class="show" role="status"><?php echo e(session('status')); ?></div>
    <script>
        setTimeout(function () {
            var t = document.getElementById('toast');
            if (t) t.classList.remove('show');
        }, 1800);
    </script>
<?php endif; ?>
<?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/partials/toast.blade.php ENDPATH**/ ?>