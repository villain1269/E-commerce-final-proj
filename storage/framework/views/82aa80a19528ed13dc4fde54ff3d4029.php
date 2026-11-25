<?php $__env->startSection('title', 'Starter Kit Builder | Danielle\'s Crafted Pizza'); ?>

<?php $__env->startSection('content'); ?>
    <main class="starter-kit-page">
        <div class="page-title">
            <span class="tag-label">Starter Kit Builder</span>
            <h1>Start your pizza business in one click</h1>
            <p>Tell us your business type and budget. We'll recommend a starter package of dough, boxes and equipment with an estimated total.</p>
        </div>

        <?php if(session('status')): ?>
            <div class="sandbox-banner" style="background:#fdecea;border-left-color:var(--red);color:#6e1f1a">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <div class="starter-kit-layout">
            <!-- LEFT: Form -->
            <div class="kit-form-card">
                <h2>Build Your Kit</h2>
                <form id="kit-form" method="post" action="<?php echo e(route('starter-kit.recommend')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <fieldset>
                        <legend>Business Type</legend>
                        <div class="option-row">
                            <?php $__currentLoopData = $kitBusinessLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label>
                                    <input type="radio" name="businessType" value="<?php echo e($value); ?>" <?php echo e(old('businessType') === $value ? 'checked' : ''); ?>>
                                    <span><?php echo e($label); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Budget</legend>
                        <div class="option-row">
                            <label>
                                <input type="radio" name="budget" value="3000" <?php echo e(old('budget') === '3000' ? 'checked' : ''); ?>>
                                <span>₱3,000</span>
                            </label>
                            <label>
                                <input type="radio" name="budget" value="5000" <?php echo e(old('budget') === '5000' ? 'checked' : ''); ?>>
                                <span>₱5,000</span>
                            </label>
                            <label>
                                <input type="radio" name="budget" value="10000" <?php echo e(old('budget') === '10000' ? 'checked' : ''); ?>>
                                <span>₱10,000</span>
                            </label>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-large" style="width:100%">Recommend My Kit</button>
                </form>
            </div>

            <!-- RIGHT: Result -->
            <div class="kit-result-card" id="kit-result">
                <?php if($kit): ?>
                    <?php $kitData = $kit; ?>
                    <span class="tag"><?php echo e($kitData['business_label']); ?> · ₱<?php echo e(number_format($kitData['budget'])); ?> budget</span>
                    <h3><?php echo e($kitData['kit']['name']); ?></h3>
                    <p><?php echo e($kitData['kit']['note']); ?></p>
                    <div class="table-wrap">
                        <table class="kit-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="num">Qty</th>
                                    <th class="num">Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $kitData['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('product.show', ['id' => $i['product']->public_id])); ?>"><?php echo e($i['product']->name); ?></a>
                                            <span class="product-cat"><?php echo e($i['product']->category->label); ?></span>
                                        </td>
                                        <td class="num"><?php echo e($i['qty']); ?></td>
                                        <td class="num"><?php echo e($i['product']->peso($i['qty'])); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Estimated Total</td>
                                    <td class="num"><?php echo e(\App\Helpers\Format::peso($kitData['total'])); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="note ok"><?php echo e(\App\Helpers\Format::peso($kitData['left'])); ?> left in your budget.</p>
                    <form method="post" action="<?php echo e(route('starter-kit.recommend')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="businessType" value="<?php echo e($kitData['type']); ?>">
                        <input type="hidden" name="budget" value="<?php echo e($kitData['budget']); ?>">
                        <input type="hidden" name="add_to_cart" value="1">
                        <button class="btn btn-large" type="submit">Add Entire Kit To Cart</button>
                    </form>
                <?php else: ?>
                    <div class="kit-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" style="margin:0 auto 1rem;display:block">
                            <path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/>
                        </svg>
                        <h3>Your recommended kit will appear here</h3>
                        <p style="margin:0">Pick a business type and budget, then click "Recommend My Kit".</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <p style="text-align:center;margin-top:2.5rem">
            <a href="<?php echo e(route('home')); ?>" style="font-size:.9rem;color:var(--muted)">← Back to Home</a>
            &nbsp;·&nbsp;
            <a href="<?php echo e(route('shop')); ?>" style="font-size:.9rem;color:var(--muted)">Browse all products →</a>
        </p>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/pages/starter-kit.blade.php ENDPATH**/ ?>