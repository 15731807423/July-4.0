<?php $__env->startSection('h1', '选择语言'); ?>

<?php $__env->startSection('main_content'); ?>
  <div class="jc-table-wrapper">
    <table class="jc-table">
      <colgroup>
        <col width="80px">
        <col width="auto">
        <col width="200px">
      </colgroup>
      <thead>
        <tr>
          <th>语言码</th>
          <th>语言</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $langcode => $langname): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($langcode); ?></td>
          <td><?php echo e($langname); ?> <?php echo e($langcode === $original_langcode ? '(源语言)' : ''); ?> </td>
          <td>
            <div class="jc-operaters">
              <?php if($langcode === $original_langcode): ?>
              <a href="<?php echo e($models[$content_id]['url']); ?>" target="_blank" title="预览页面" class="md-button md-fab md-dense md-primary md-theme-default">
                <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">visibility</i></div></div>
              </a>

              <a href="<?php echo e(short_url($edit_route, [$content_id])); ?>" title="编辑" class="md-button md-fab md-mini md-light-primary md-theme-default">
                <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">edit</i></div></div>
              </a>
              <?php else: ?>
              <a href="<?php echo e('/'.$langcode.$models[$content_id]['url']); ?>" target="_blank" title="预览页面" class="md-button md-fab md-dense md-primary md-theme-default">
                <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">visibility</i></div></div>
              </a>
              <a href="<?php echo e(short_url($translate_route, [$content_id, $langcode])); ?>" title="翻译" class="md-button md-fab md-mini md-light-primary md-theme-default">
                <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">translate</i></div></div>
              </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\test\system\resources\views/languages.blade.php ENDPATH**/ ?>