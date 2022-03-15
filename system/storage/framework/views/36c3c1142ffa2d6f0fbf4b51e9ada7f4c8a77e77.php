<?php $attributes = $attributes->exceptProps([
  'title',
  'icon' => null,
  'href' => null,
  'click' => null,
  'target' => null,
  'disabled' => null,
  'theme' => 'md-primary',
]); ?>
<?php foreach (array_filter(([
  'title',
  'icon' => null,
  'href' => null,
  'click' => null,
  'target' => null,
  'disabled' => null,
  'theme' => 'md-primary',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if($href): ?>
<li class="md-list-item">
  <div v-if="<?php echo e($disabled ?? 'false'); ?>" class="md-list-item-container md-button-clean" disabled>
    <div class="md-list-item-content">
      <i class="md-icon md-icon-font <?php echo e($theme); ?> md-theme-default"><?php echo e($icon); ?></i>
      <span class="md-list-item-text"><?php echo e($title); ?></span>
    </div>
  </div>
  <a v-else :href="<?php echo e($href); ?>" target="<?php echo e($target); ?>" class="md-list-item-link md-list-item-container md-button-clean">
    <div class="md-list-item-content">
      <i class="md-icon md-icon-font <?php echo e($theme); ?> md-theme-default"><?php echo e($icon); ?></i>
      <span class="md-list-item-text"><?php echo e($title); ?></span>
    </div>
  </a>
</li>
<?php elseif($click): ?>
<li class="md-list-item">
  <div class="md-list-item-container md-button-clean" @click.stop="<?php echo e($click); ?>" :disabled="<?php echo e($disabled ?? 'false'); ?>">
    <div class="md-list-item-content md-ripple">
      <i class="md-icon md-icon-font <?php echo e($theme); ?> md-theme-default"><?php echo e($icon); ?></i>
      <span class="md-list-item-text"><?php echo e($title); ?></span>
    </div>
  </div>
</li>
<?php else: ?>
<li class="md-list-item">
  <div class="md-list-item-container md-button-clean" :disabled="<?php echo e($disabled ?? 'false'); ?>">
    <div class="md-list-item-content md-ripple">
      <i class="md-icon md-icon-font <?php echo e($theme); ?> md-theme-default"><?php echo e($icon); ?></i>
      <span class="md-list-item-text"><?php echo e($title); ?></span>
    </div>
  </div>
</li>
<?php endif; ?>
<?php /**PATH D:\laragon\www\test\system\resources\views/components/menu-item.blade.php ENDPATH**/ ?>