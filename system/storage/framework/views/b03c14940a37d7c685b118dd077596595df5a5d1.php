<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登录后台</title>
  <link rel="stylesheet" href="/themes/backend/vendor/element-ui/theme-chalk/index.css">
  <style>
    .el-form-item {
      margin-bottom: 0;
    }
    .el-form-item + .el-form-item {
      margin-top: 20px;
    }
    .el-dialog {
      padding: 20px 30px;
    }
    .el-dialog__header, .el-dialog__body, .el-dialog__footer {
      padding: 0;
      margin: 0;
    }
    .el-dialog__header + .el-dialog__body {
      margin-top: 30px;
    }
    .el-dialog__body + .el-dialog__footer {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div id="app">
    <el-dialog title="登录后台" width="400px" :visible="true" :show-close="false">
      <el-form label-width="80px" method="POST" action="<?php echo e(short_url('admin.auth')); ?>" ref="form">
        <?php echo csrf_field(); ?>
        <el-form-item label="用户名：" size="medium">
          
          <div class="el-input el-input--medium">
            <input autocomplete="off" name="name" class="el-input__inner"
              v-model="name" @keyup.enter="focusPassword">
          </div>
        </el-form-item>
        <el-form-item label="密码：" size="medium">
          
          <div class="el-input el-input--medium">
            <input type="password" autocomplete="off" name="password" class="el-input__inner"
              ref="password" v-model="password" @keyup.enter="submit">
          </div>
        </el-form-item>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <el-form-item size="medium" error="<?php echo e($message); ?>"></el-form-item>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <el-form-item size="medium">
          <el-checkbox name="remember" v-model="remember">记住我</el-checkbox>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="medium" type="primary" :loading="loading" @click.stop="submit">登 录</el-button>
      </div>
    </el-dialog>
  </div>

  <script src="/themes/backend/js/app.js"></script>
  <script src="/themes/backend/vendor/element-ui/index.js"></script>
  <script>
    let dialog = new Vue({
      el: '#app',
      data() {
        return {
          name: "<?php echo e(old('name')); ?>",
          password: "<?php echo e(old('password')); ?>",
          remember: false,
          loading: false,
        };
      },

      methods: {
        focusPassword() {
          this.$refs.password.focus();
        },
        submit() {
          this.loading = true;
          this.$refs.form.$el.submit();
        },
      },
    });
  </script>
</body>
</html>
<?php /**PATH D:\laragon\www\test\system\resources\views/auth/login.blade.php ENDPATH**/ ?>