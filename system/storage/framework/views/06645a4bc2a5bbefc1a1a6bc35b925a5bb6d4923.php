<?php $__env->startSection('h1', '选择类型'); ?>

<?php $__env->startSection('main_content'); ?>
  <div id="main_list">
    <div class="jc-table-wrapper">
      <table class="jc-table">
        <colgroup>
          <col width="200px">
          <col width="200px">
          <col width="auto">
        </colgroup>
        <thead>
          <tr>
            <th>内容类型</th>
            <th>类型 ID</th>
            <th>类型描述</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><a href="<?php echo e(short_url('nodes.create', $mold->id)); ?>"><?php echo e($mold->label); ?></a></td>
            <td><?php echo e($mold->id); ?></td>
            <td><?php echo e($mold->description); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\test\system\modules\july\node/views/node/choose_mold.blade.php ENDPATH**/ ?>