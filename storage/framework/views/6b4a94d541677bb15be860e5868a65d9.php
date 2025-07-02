

<?php $__env->startSection('title', 'Đại sứ kết nối'); ?>

<?php $__env->startSection('content'); ?>
<div class="container d-flex align-items-center justify-content-center mt-2">
    <div class="card p-4 shadow-sm rounded-4 " style="max-width: 400px; width: 100%;">
        <h5 class="mb-3 fw-bold">Đại sứ kết nối</h5>
        <h2 class="mb-3 fw-bold fs-1" style="color: #152379">1.000.000đ</h2>
        <p class="text-muted mb-4">Để hưởng những quyền lợi của đại sứ kết nối</p>

        <a href="<?php echo e(route('getAllProduct', ['category_id' => 2])); ?>" class="btn btn-success rounded-pill mb-3 px-4" style="background: #152379">Tiếp tục đăng ký</a>
        <div style="width: 100%; " class="text-center">
            <a href="<?php echo e(route('account.agency')); ?>" class="text-primary small text-decoration-underline ">Bỏ qua</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/ambassador.blade.php ENDPATH**/ ?>