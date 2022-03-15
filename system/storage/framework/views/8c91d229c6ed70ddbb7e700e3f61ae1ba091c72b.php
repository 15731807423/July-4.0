<?php $__env->startSection('h1'); ?>
  <?php echo e(__('backend.'.$context['mode'])); ?>内容 <span id="content_locale">[ <?php echo e($context['mold']->label); ?>(<?php echo e($context['mold']->id); ?>), <?php echo e(langname($langcode)); ?>(<?php echo e($langcode); ?>) ]</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
  <el-form id="main_form" ref="main_form"
    :model="model"
    :rules="rules"
    label-position="top">
    <div id="main_form_left">
      
      <el-form-item prop="title" size="small" class="has-helptext" :rules="[{required:true, message:'标题不能为空', trigger:'blur'}]">
        <el-tooltip slot="label" content="title" placement="right" effect="dark" popper-class="jc-twig-output">
          <span>标题</span>
        </el-tooltip>
        <el-input v-model="model.title" native-size="100"></el-input>
        <span class="jc-form-item-help"><i class="el-icon-info"></i> 标题，可用作链接文字等。</span>
      </el-form-item>

      
      <?php $__currentLoopData = $context['local_fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php echo $field->render($model[$field['id']] ?? null); ?>

      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <el-form-item prop="view" size="small" class="has-helptext"
        :rules="[{pattern:/^(?:[a-z0-9\-_]+\/)*[a-z0-9\-_]+\.(?:twig|html?)$/, message:'格式不正确', trigger:'change'}]">
        <el-tooltip slot="label" content="view" placement="right" effect="dark" popper-class="jc-twig-output">
          <span>模板</span>
        </el-tooltip>
        <el-select v-model="model.view" filterable allow-create default-first-option style="width:100%;max-width:360px">
          <?php $__currentLoopData = $context['views']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $view): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <el-option value="<?php echo e($view); ?>"></el-option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </el-select>
        <span class="jc-form-item-help"><i class="el-icon-info"></i> 指定模板</span>
      </el-form-item>

      
      <el-form-item size="small" label="红绿蓝">
        <el-tooltip popper-class="jc-twig-output" effect="dark" content="is_red" placement="top">
          <el-switch style="margin-right: 1em" v-model="model.is_red" active-color="#F44336" inactive-color="#FFCDD2"></el-switch>
        </el-tooltip>
        <el-tooltip popper-class="jc-twig-output" effect="dark" content="is_green" placement="top">
          <el-switch style="margin-right: 1em" v-model="model.is_green" active-color="#4caf50" inactive-color="#C8E6C9"></el-switch>
        </el-tooltip>
        <el-tooltip popper-class="jc-twig-output" effect="dark" content="is_blue" placement="top">
          <el-switch style="margin-right: 1em" v-model="model.is_blue" active-color="#2196F3" inactive-color="#BBDEFB"></el-switch>
        </el-tooltip>
      </el-form-item>

      
      <div id="main_form_bottom" class="is-button-item">
        <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="submit">
          <div class="md-button-content">保存</div>
        </button>
      </div>
    </div>
    <div id="main_form_right">
      <h2 class="jc-form-info-item">通用非必填项</h2>

      
      <el-collapse :value="expanded">
        <?php $__currentLoopData = $context['global_fields']->groupBy('field_group'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldGroup => $globalFields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <el-collapse-item name="<?php echo e($fieldGroup); ?>" title="<?php echo e($fieldGroup); ?>">
          <?php $__currentLoopData = $globalFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $field->render($model[$field['id']] ?? null); ?>

          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </el-collapse-item>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </el-collapse>
    </div>
  </el-form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
  window.showMediasWindow = function() {
    let mediaWindow = null;

    return function showMediasWindow() {
      const screenWidth = window.screen.availWidth;
      const screenHeight = window.screen.availHeight;

      const width = screenWidth*.8;
      const height = screenHeight*.8 - 60;
      const left = screenWidth*.1;
      const top = screenHeight*.15;

      if (!mediaWindow || mediaWindow.closed) {
        mediaWindow = window.open(
          "<?php echo e(short_url('media.select')); ?>",
          'chooseMedia',
          `resizable,scrollbars,status,top=${top},left=${left},width=${width},height=${height}`
        );
      } else {
        mediaWindow.focus()
      }
    }
  }();

  function recieveMediaUrl(url) {
    app.recieveMediaUrl(url)
  }

  let app = new Vue({
    el: '#main_content',
    data() {
      return {
        model: <?php echo json_encode($model, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE, 512) ?>,
        rules: {},
        expanded: <?php echo json_encode($context['global_fields']->pluck('field_group')->unique()->values()->all(), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE, 512) ?>,
      };
    },

    created: function() {
      this.original_model = _.cloneDeep(this.model);
    },

    methods: {
      getChanged() {
        const changed = [];
        for (const key in this.model) {
          if (! _.isEqual(this.model[key], this.original_model[key])) {
            changed.push(key);
          }
        }
        return changed;
      },

      showMedias(field) {
        this.recieveMediaUrlFor = field;
        showMediasWindow();
      },

      recieveMediaUrl(url) {
        if (this.recieveMediaUrlFor) {
          this.model[this.recieveMediaUrlFor] = url;
        }
      },

      submit() {
        const loading = this.$loading({
          lock: true,
          text: '正在保存内容 ...',
          background: 'rgba(255, 255, 255, 0.7)',
        });

        for (const key in this.model) {
          const editor = this.$refs['ckeditor_' + key];
          // console.log('ckeditor_' + key);
          // console.log(editor);
          if (editor && editor.instance && editor.instance.mode != 'wysiwyg') {
            editor.instance.setMode('wysiwyg');
          }
        }

        setTimeout(() => {
          this.$refs.main_form.validate().then(() => {
            const changed = this.getChanged();
            <?php if($context['mode'] === 'edit'): ?>
              if (!changed.length) {
                document.referrer === '' ? window.location.href = "<?php echo e(short_url('nodes.index')); ?>" : window.location.href = document.referrer;
                return;
              }
            <?php endif; ?>

            const model = _.cloneDeep(this.model);
            model.langcode = '<?php echo e($langcode); ?>';
            model._changed = changed;

            <?php if($context['mode'] !== 'create'): ?>
            const action = "<?php echo e(short_url('nodes.update', $model['id'])); ?>";
            <?php else: ?>
            const action = "<?php echo e(short_url('nodes.store')); ?>";
            <?php endif; ?>

            axios.<?php echo e($context['mode'] !== 'create' ? 'put' : 'post'); ?>(action, model)
              .then((response) => {
                document.referrer === '' ? window.location.href = "<?php echo e(short_url('nodes.index')); ?>" : window.location.href = document.referrer;
              })
              .catch((error) => {
                loading.close();
                this.$message.error(error);
              });
          }).catch((error) => {
            // console.error(error);
            loading.close();
          });
        }, 100);
      },
    }
  })
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\test\system\modules\july\node/views/node/create-edit.blade.php ENDPATH**/ ?>