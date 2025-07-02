


<?php $__env->startSection('title', 'Đăng ký tài khoản'); ?>
<?php $__env->startSection('content'); ?>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 400px;
            margin: 65px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
    <div class="container">
        <div class="register-container">
            <h3 class="text-center mb-4">Đăng ký tài khoản</h3>
            <form action="<?php echo e(route('signup')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                
                

                
                <input type="hidden" name="ref" value="<?php echo e(request()->query('ref')); ?>"> 
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Nhập số điện thoại" value="<?php echo e(old('phone')); ?>" required>
                </div>

                
                

                
                

                <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                <p class="text-center mt-3">Bạn đã có tài khoản? <a href="<?php echo e(route('login')); ?>">Đăng nhập ngay</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                if (target.type === 'password') {
                    target.type = 'text';
                    this.classList.replace('bi-eye-slash', 'bi-eye');
                } else {
                    target.type = 'password';
                    this.classList.replace('bi-eye', 'bi-eye-slash');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/signup.blade.php ENDPATH**/ ?>