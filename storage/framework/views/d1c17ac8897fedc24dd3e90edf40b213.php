<?php
/** @var \App\Models\Product $product */
$img = $product->imageUrl();
?>
<article class="product-card">
    <a href="<?php echo e(route('product.show', ['id' => $product->public_id])); ?>" class="product-image ph" aria-label="<?php echo e($product->name); ?>">
        <?php if($product->wholesale): ?>
            <span class="img-badge">WHOLESALE PACK</span>
        <?php endif; ?>
        <?php if($img): ?>
            <img src="<?php echo e($img); ?>" alt="<?php echo e($product->name); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block">
        <?php else: ?>
            <span>[PRODUCT IMAGE]</span>
        <?php endif; ?>
    </a>
    <div class="product-info">
        <span class="product-cat"><?php echo e($product->category->label); ?></span>
        <h3><a href="<?php echo e(route('product.show', ['id' => $product->public_id])); ?>" title="<?php echo e($product->name); ?>"><?php echo e($product->name); ?></a></h3>
        <strong class="price"><?php echo e($product->peso()); ?></strong>
    </div>
    <form method="post" action="<?php echo e(route('cart.add', ['id' => $product->public_id])); ?>" class="add-row-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="qty" value="1">
        <button type="submit" class="add-row" data-add="<?php echo e($product->public_id); ?>" aria-label="Add <?php echo e($product->name); ?> to order">
            <span>Add to Order</span><span class="plus" aria-hidden="true">+</span>
        </button>
    </form>
</article>
<?php /**PATH /home/assistantuser/schoolproj/ecommerce/resources/views/partials/product-card.blade.php ENDPATH**/ ?>