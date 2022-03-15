

<el-form-item prop="<?php echo e($id); ?>" size="small" class="<?php echo e(isset($helptext) ? 'has-helptext' : ''); ?>">
  <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="<?php echo e($id); ?>" placement="right">
    <span><?php echo e($label); ?></span>
  </el-tooltip>
  <el-input
    v-model="model.<?php echo e($id); ?>"
    type="textarea"
    rows="2"></el-input>
  <?php if($helptext ?? false): ?>
  <span class="jc-form-item-help"><i class="el-icon-info"></i> <?php echo e($helptext); ?></span>
  <?php endif; ?>
</el-form-item>
<?php /**PATH D:\laragon\www\test\system\resources\views/field_type/text.blade.php ENDPATH**/ ?>