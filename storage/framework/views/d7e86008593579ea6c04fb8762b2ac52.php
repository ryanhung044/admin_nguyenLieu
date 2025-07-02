

<?php $__env->startSection('title', $article->title); ?>
<?php $__env->startSection('content'); ?>

<div class="container ">
    <div class="row">
        
            <!-- Bài viết chi tiết -->
            <div class="article-detail card border-0 shadow-sm rounded-3">
                <img src="<?php echo e(asset('storage/' . $article->image)); ?>" class="card-img-top rounded-3"  alt="Article Image">
                <div class="card-body">
                    <h2 class="card-title fw-bold mb-3"><?php echo e($article->title); ?></h2>
                    <p class="small text-muted mb-3">Đăng vào <?php echo e($article->created_at->format('d/m/Y')); ?> 

                    <!-- Nội dung bài viết -->
                    <div class="content">
                        <?php echo $article->content; ?>

                    </div>

                    <!-- Thông tin chia sẻ -->
                    <div class="mt-4">
                        <button class="btn btn-primary rounded-5">
                            <i class="fa fa-share-alt"></i> Chia sẻ
                        </button>
                        <a href="#" class="btn btn-light rounded-5 border" onclick="copyReferralLink('<?php echo e(route('article_detail', $article->slug)); ?>')">
                            <i class="fa fa-link"></i> Sao chép link
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bài viết liên quan -->
            <div class="related-articles mt-5">
                <h5 class="fw-bold mb-3">Bài viết liên quan</h5>
                <div class="row">
                    <?php $__currentLoopData = $relatedArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-3">
                                <a href="<?php echo e(route('article_detail', $related->slug)); ?>">
                                    <img src="<?php echo e(asset('storage/' . $related->image)); ?>" class="card-img-top rounded-3" alt="Related Article Image" style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate mb-1"><?php echo e($related->title); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

        

        <!-- Sidebar -->
        
    </div>
</div>

<script>
    function copyReferralLink(link) {
        navigator.clipboard.writeText(link).then(() => {
            alert('Link bài viết đã được sao chép!');
        }).catch(err => {
            console.error('Không thể sao chép link', err);
        });
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/article_detail.blade.php ENDPATH**/ ?>