<?php $__env->startSection('title', 'Professional Pizza Shop | Danielle\'s Crafted Pizza'); ?>

<?php $__env->startSection('content'); ?>
    <main class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Home</a><span>›</span><span class="here">All Pizza Supplies</span>
        </nav>
        <div class="page-title">
            <h1>Professional Pizza Shop</h1>
            <p id="shop-sub"><?php if($search): ?>
                    Search results for "<?php echo e($search); ?>"
                <?php else: ?>
                    Premium bases, bulk packaging, high-temp gear, and fresh preps.
                <?php endif; ?></p>
        </div>

        <button class="btn btn-outline filter-toggle" id="filter-toggle" type="button">Filters</button>

        <div class="shop-layout">
            <aside class="filter-card" id="filter-card">
                <form id="filter-form" autocomplete="off" method="get" action="<?php echo e(route('shop')); ?>">
                    
                    <?php if($search): ?> <input type="hidden" name="q" value="<?php echo e($search); ?>"> <?php endif; ?>

                    <div class="filter-head">
                        <h3>Filter Products</h3>
                        <a class="link-red" id="clear-filters" href="<?php echo e(route('shop')); ?>">Clear All</a>
                    </div>
                    <div class="filter-group">
                        <h4>Category</h4>
                        <div id="filter-cats">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="check-row">
                                    <input type="checkbox" name="category[]" value="<?php echo e($category->key); ?>" <?php echo e(in_array($category->key, $selectedCats) ? 'checked' : ''); ?>>
                                    <span><?php echo e($category->label); ?></span>
                                    <span class="count"><?php echo e($category->products()->count()); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-row">
                            <input type="number" name="min" min="0" placeholder="₱ Min" value="<?php echo e($min ?? ''); ?>" aria-label="Minimum price">
                            <span>to</span>
                            <input type="number" name="max" min="0" placeholder="₱ Max" value="<?php echo e($max ?? ''); ?>" aria-label="Maximum price">
                        </div>
                    </div>
                    <div class="filter-group">
                        <h4>Best Suited For</h4>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="home" <?php echo e(in_array('home', $selectedSuit) ? 'checked' : ''); ?>><span>Home Baking</span></label>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="commercial" <?php echo e(in_array('commercial', $selectedSuit) ? 'checked' : ''); ?>><span>Commercial Pizzeria</span></label>
                        <label class="check-row"><input type="checkbox" name="suit[]" value="bulk" <?php echo e(in_array('bulk', $selectedSuit) ? 'checked' : ''); ?>><span>Bulk Events</span></label>
                    </div>
                    <button type="submit" class="btn" style="margin-top:1rem">Apply Filters</button>
                </form>
            </aside>

            <section>
                <div class="results-bar" id="results-bar">
                    <span id="results-info">Showing <strong><?php echo e($count); ?></strong> of <?php echo e($total); ?> pizza item<?php echo e($total === 1 ? '' : 's'); ?></span>
                    <label class="sort-wrap">Sort By:
                        <form method="get" action="<?php echo e(route('shop')); ?>" id="sort-form" style="display:inline">
                            <?php echo csrf_field(); ?>
                            <?php if($search): ?> <input type="hidden" name="q" value="<?php echo e($search); ?>"> <?php endif; ?>
                            <?php $__currentLoopData = $selectedCats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <input type="hidden" name="category[]" value="<?php echo e($c); ?>"> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $selectedSuit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <input type="hidden" name="suit[]" value="<?php echo e($s); ?>"> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(is_numeric($min)): ?> <input type="hidden" name="min" value="<?php echo e($min); ?>"> <?php endif; ?>
                            <?php if(is_numeric($max)): ?> <input type="hidden" name="max" value="<?php echo e($max); ?>"> <?php endif; ?>
                            <select id="sort-select" name="sort" onchange="document.getElementById('sort-form').submit()">
                                <option value="best" <?php echo e($sort === 'best' ? 'selected' : ''); ?>>Best Selling</option>
                                <option value="low" <?php echo e($sort === 'low' ? 'selected' : ''); ?>>Price: Low to High</option>
                                <option value="high" <?php echo e($sort === 'high' ? 'selected' : ''); ?>>Price: High to Low</option>
                                <option value="name" <?php echo e($sort === 'name' ? 'selected' : ''); ?>>Name: A to Z</option>
                            </select>
                        </form>
                    </label>
                </div>

                <?php if($products->isEmpty()): ?>
                    <div class="no-results">
                        <h3>No products match your filters</h3>
                        <p>Try clearing a filter or widening the price range.</p>
                    </div>
                <?php else: ?>
                    <div class="product-grid three">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('partials.product-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <?php if($pages > 1): ?>
                    <div class="pagination">
                        <?php if($page > 1): ?>
                            <a class="page-btn" href="<?php echo e(\App\Helpers\Format::shopPageUrl($page - 1, request()->query())); ?>" aria-label="Previous page">←</a>
                        <?php else: ?>
                            <button class="page-btn" disabled>←</button>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $pages; $i++): ?>
                            <a class="page-btn <?php echo e($i === $page ? 'active' : ''); ?>" href="<?php echo e(\App\Helpers\Format::shopPageUrl($i, request()->query())); ?>"><?php echo e($i); ?></a>
                        <?php endfor; ?>

                        <?php if($page < $pages): ?>
                            <a class="page-btn" href="<?php echo e(\App\Helpers\Format::shopPageUrl($page + 1, request()->query())); ?>" aria-label="Next page">→</a>
                        <?php else: ?>
                            <button class="page-btn" disabled>→</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var toggle = document.getElementById('filter-toggle');
    if (toggle) {
        toggle.onclick = function () {
            document.getElementById('filter-card').classList.toggle('open');
        };
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/shop.blade.php ENDPATH**/ ?>