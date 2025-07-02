

<?php $__env->startSection('title', 'Mã giới thiệu'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container d-flex align-items-center justify-content-center mt-2">
        <div class="card p-4 shadow-sm rounded-4 text-center" style="max-width: 400px; width: 100%;">

            <div class="mb-3 p-3 bg-light rounded-3 d-flex justify-content-between">
                <div class="text-muted">Người giới thiệu</div>
                <?php if($user->referrer_id): ?>
                    <div class="fw-bold text-primary">GT<?php echo e($user->referrer_id); ?></div>
                <?php else: ?>
                    <div class="fw-bold text-primary">Không có</div>
                <?php endif; ?>
            </div>

            <div class="mb-4 p-3 bg-light rounded-3 d-flex justify-content-between">
                <div class="text-muted">Mã giới thiệu của bạn</div>
                <div class="fw-bold text-primary">GT<?php echo e($user->id); ?></div>
            </div>

            <button id="shareBtn" class="btn"
                style="background-color: #152379; color: white; border-radius: 999px; padding: 10px 30px">
                Chia sẻ link giới thiệu
            </button>
        </div>
    </div>
    <script>
        document.getElementById('shareBtn').addEventListener('click', function() {
            const userId = "<?php echo e(auth()->user()->id); ?>"; // lấy user_id từ Laravel
            const shareData = {
                title: 'Ứng dụng mua hàng trực tuyến',
                text: 'Tải app và nhận tới 600.000đ!',
                url: window.location.origin + '/referrer?ref=' + userId
            };

            if (navigator.share) {
                navigator.share(shareData)
                    .then(() => console.log('Shared successfully'))
                    .catch((error) => console.error('Error sharing:', error));
            } else {
                navigator.clipboard.writeText(shareData.url).then(() => {
                    alert('Link đã được sao chép! Hãy chia sẻ với bạn bè của bạn nhé 🎉');
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/referrer.blade.php ENDPATH**/ ?>