

<el-form-item prop="<?php echo e($id); ?>" size="small" class="<?php echo e(isset($helptext) ? 'has-helptext' : ''); ?>"
  :rules="[<?php echo implode(',', $rules); ?>]">
  <el-tooltip slot="label" content="<?php echo e($id); ?>" placement="right" effect="dark" popper-class="jc-twig-output">
    <span><?php echo e($label); ?></span>
  </el-tooltip>
  <el-input v-model="model.<?php echo e($id); ?>" native-size="100" placeholder="/index.html"></el-input>
  <?php if($helptext ?? false): ?>
  <span class="jc-form-item-help"><i class="el-icon-info"></i> <?php echo e($helptext); ?></span>
  <?php endif; ?>
</el-form-item>

<?php /**PATH D:\laragon\www\test\system\resources\views/field_type/path-alias.blade.php ENDPATH**/ ?>