

<el-form-item prop="<?php echo e($id); ?>" size="small" class="<?php echo e(isset($helptext) ? 'has-helptext' : ''); ?>">
  <el-tooltip slot="label" content="<?php echo e($id); ?>" placement="right" effect="dark" popper-class="jc-twig-output">
    <span><?php echo e($label); ?></span>
  </el-tooltip>
  <ckeditor
    ref="ckeditor_<?php echo e($id); ?>"
    v-model="model.<?php echo e($id); ?>"
    tag-name="textarea"
    :config="{filebrowserImageBrowseUrl: '<?php echo e(short_url('media.select')); ?>'}"></ckeditor>
  <?php if($helptext ?? false): ?>
  <span class="jc-form-item-help"><i class="el-icon-info"></i> <?php echo e($helptext); ?></span>
  <?php endif; ?>
</el-form-item>
<?php /**PATH D:\laragon\www\test\system\resources\views/field_type/html.blade.php ENDPATH**/ ?>