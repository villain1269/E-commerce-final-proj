<?php $__env->startSection('title', $product->name . " | Danielle's Crafted Pizza"); ?>

<?php $__env->startSection('content'); ?>
    <main class="container">
        <nav class="breadcrumb" id="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Home</a><span>›</span>
            <a href="<?php echo e(route('shop', ['category' => $product->category->key])); ?>"><?php echo e($product->category->label); ?></a>
            <span>›</span>
            <span class="here"><?php echo e($product->name); ?></span>
        </nav>

        <div class="detail-grid">
            <div>
                <?php $mainImg = $product->imageUrl(); ?>
                <div class="detail-main ph" style="border-radius:10px;<?php if($mainImg): ?> background:none;padding:0;<?php endif; ?>">
                    <?php if($mainImg): ?>
                        <img src="<?php echo e($mainImg); ?>" alt="<?php echo e($product->name); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:10px;display:block">
                    <?php else: ?>
                        [PRODUCT IMAGE]
                    <?php endif; ?>
                </div>
                <div class="thumbs">
                    <?php $__currentLoopData = range(2, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $thumbImg = null;
                            $thumbCandidates = [
                                $product->public_id . '-' . $i,
                                $product->sku . '-' . $i,
                                str($product->name)->slug() . '-' . $i,
                            ];
                            foreach ($thumbCandidates as $base) {
                                foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                                    $rel = "products/{$base}.{$ext}";
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) {
                                        $thumbImg = \Illuminate\Support\Facades\Storage::disk('public')->url($rel);
                                        break 2;
                                    }
                                }
                            }
                        ?>
                        <div class="ph" style="border-radius:6px;<?php if($thumbImg): ?> background:none;padding:0;<?php endif; ?>">
                            <?php if($thumbImg): ?>
                                <img src="<?php echo e($thumbImg); ?>" alt="<?php echo e($product->name); ?> view <?php echo e($i); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;display:block">
                            <?php else: ?>
                                [IMAGE <?php echo e($i); ?>]
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="detail-card">
                <span class="tag-label"><?php echo e($product->hasTag('Best Seller') ? 'BESTSELLER ' . $product->category->badge : strtoupper($product->category->label)); ?></span>
                <h1><?php echo e($product->name); ?></h1>
                <div class="rating">
                    <span class="stars" aria-label="<?php echo e($product->rating); ?> out of 5 stars"><?php echo e($product->starString()); ?></span>
                    <span>(<?php echo e($product->reviews); ?> reviews)</span>
                </div>
                <div class="detail-price"><?php echo e($product->peso()); ?> <small>/ <?php echo e($product->unit); ?></small></div>
                <p class="detail-desc"><?php echo e($product->desc); ?></p>

                <form method="post" action="<?php echo e(route('cart.add', ['id' => $product->public_id])); ?>" class="buy-row-form">
                    <?php echo csrf_field(); ?>
                    <div class="buy-row">
                        <div class="qty-control">
                            <button type="button" id="qty-minus" aria-label="Decrease">−</button>
                            <input id="detail-qty" name="qty" type="number" min="1" max="99" value="1" aria-label="Quantity">
                            <button type="button" id="qty-plus" aria-label="Increase">+</button>
                        </div>
                        <button class="btn" type="submit" id="detail-add">Add to Cart • <?php echo e($product->peso()); ?></button>
                    </div>
                </form>

                <div class="notice"><span>Selling pizza? Orders over <b>₱3,000 ship free</b>, and wholesale pricing is available for bigger orders.</span></div>
                <div class="meta">
                    <div><strong>SKU:</strong> <?php echo e($product->sku); ?></div>
                    <div><strong>Category:</strong> <?php echo e($product->category->label); ?></div>
                    <?php if($product->specSecondaryLabel()): ?>
                        <div><strong><?php echo e($product->specSecondaryLabel()); ?>:</strong> <?php echo e($product->specSecondaryValue()); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="product-extra">
            <div class="tabs" id="tabs" role="tablist">
                <button class="tab active" data-tab="desc">Description</button>
                <button class="tab" data-tab="specs">Ingredients / Specs</button>
                <button class="tab" data-tab="reviews">Customer Reviews (<?php echo e($product->reviews); ?>)</button>
            </div>

            <div class="tab-panel" id="tab-panel">
                <p><?php echo e($product->desc); ?></p>
                <p>Packed in seller-friendly quantities and prepared fresh for pickup or delivery. Message us for wholesale pricing or custom orders.</p>
            </div>

            <section class="related">
                <h2>Perfect Companions</h2>
                <div class="product-grid">
                    <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.product-card', ['product' => $relProduct], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    // Quantity + add-to-cart label sync
    var qty = document.getElementById('detail-qty'),
        addBtn = document.getElementById('detail-add');
    if (!qty || !addBtn) return;
    var unit = <?php echo e($product->price); ?>;
    var clamp = function () {
        var v = parseInt(qty.value, 10);
        if (isNaN(v) || v < 1) v = 1;
        if (v > 99) v = 99;
        qty.value = v;
        return v;
    };
    var updateLabel = function () {
        addBtn.textContent = 'Add to Cart • ₱' + (unit * clamp()).toLocaleString('en-PH');
    };
    document.getElementById('qty-minus').onclick = function () { qty.value = clamp() - 1; updateLabel(); };
    document.getElementById('qty-plus').onclick  = function () { qty.value = clamp() + 1; updateLabel(); };
    qty.addEventListener('change', updateLabel);
    updateLabel();

    // Tabs (Description / Specs / Reviews)
    var tabsEl = document.getElementById('tabs');
    var panel = document.getElementById('tab-panel');
    if (!tabsEl || !panel) return;

    var specRows = <?php echo json_encode(collect($product->specs)->map(fn ($s) => ['label' => $s[0], 'value' => $s[1]]), 512) ?>;
    var specHtml = '<table class="spec-table"><tbody>';
    specRows.forEach(function (s) {
        specHtml += '<tr><th>' + s.label + '</th><td>' + s.value + '</td></tr>';
    });
    specHtml += '<tr><th>SKU</th><td><?php echo e($product->sku); ?></td></tr>';
    specHtml += '</tbody></table>';

    var panels = {
        desc: '<p><?php echo e(e($product->desc)); ?></p>' +
              '<p>Packed in seller-friendly quantities and prepared fresh for pickup or delivery. Message us for wholesale pricing or custom orders.</p>',
        specs: specHtml,
        reviews: '<div class="review"><span class="stars">★★★★★</span> <strong>Chef Marco</strong>' +
                  '<p>Consistent every single batch. Our customers noticed the difference right away.</p></div>' +
                  '<div class="review"><span class="stars"><?php echo e($product->starString()); ?></span> <strong>Sarah L.</strong>' +
                  '<p>Great value for the pack size and easy to reorder. Sample review for demonstration.</p></div>'
    };

    tabsEl.addEventListener('click', function (e) {
        var b = e.target.closest('[data-tab]');
        if (!b) return;
        tabsEl.querySelectorAll('.tab').forEach(function (t) {
            t.classList.toggle('active', t === b);
        });
        panel.innerHTML = panels[b.dataset.tab] || '';
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/product.blade.php ENDPATH**/ ?>