

<?php $__env->startSection('title', 'Chỉnh sửa thông tin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card shadow-sm p-4 rounded-4 mt-4">
        <h5 class="mb-4 text-center fw-bold"><i class="fas fa-user-edit me-2 text-primary"></i>Chỉnh sửa thông tin</h5>

        <form method="POST" action="<?php echo e(route('users.update', $user->id)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tên đăng nhập</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo e(old('full_name', $user->full_name)); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu mới (nếu đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn thay đổi">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $user->phone)); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Giới tính</label>
                <select name="gender" class="form-select">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="male" <?php echo e(old('gender', $user->gender) == 'male' ? 'selected' : ''); ?>>Nam</option>
                    <option value="female" <?php echo e(old('gender', $user->gender) == 'female' ? 'selected' : ''); ?>>Nữ</option>
                    <option value="other" <?php echo e(old('gender', $user->gender) == 'other' ? 'selected' : ''); ?>>Khác</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ngày sinh</label>
                <input type="date" name="birthday" class="form-control" value="<?php echo e(old('birthday', $user->birthday)); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="<?php echo e(old('address', $user->address)); ?>">
            </div>

            

            

            <div class="mb-3">
                <label class="form-label fw-semibold">Ảnh đại diện</label>
                <input type="file" name="avatar" class="form-control">
                <?php if($user->avatar): ?>
                    <div class="mt-2">
                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" width="100" class="rounded">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                <i class="fas fa-save me-2"></i>Cập nhật thông tin
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/change_info.blade.php ENDPATH**/ ?>