<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>切换数据库</title>
    <link rel="stylesheet" href="/themes/backend/fonts/fonts.css">
    <link rel="stylesheet" href="/themes/backend/vendor/normalize.css/normalize.css">
    <link rel="stylesheet" href="/themes/backend/vendor/vue-material/vue-material.css">
    <link rel="stylesheet" href="/themes/backend/vendor/vue-material/theme/default.css">
    <link rel="stylesheet" href="/themes/backend/vendor/element-ui/theme-chalk/index.css">
    <link rel="stylesheet" href="/themes/backend/css/july.css">
    <link rel="stylesheet" href="/themes/backend/css/install.css">
</head>

<body>
    <div id="install" class="md-elevation-7">
        <h1 class="jc-install-title">切换数据库</h1>
        <div id="install_steps">
            <div class="jc-install-step">
                <div class="jc-install-step-content">
                    <el-form ref="data_form" :model="data" label-width="120px">
                        <el-form-item size="small" label="数据库类型">
                            <el-radio-group v-model="data.model">
                                <el-radio-button label="mysql">mysql</el-radio-button>
                                <el-radio-button label="sqlite">sqlite</el-radio-button>
                            </el-radio-group>
                        </el-form-item>

                        <template v-if="data.model == 'mysql'">
                            <el-form-item size="small" label="数据库用户名">
                                <el-input v-model="data.mysql.username"></el-input>
                            </el-form-item>

                            <el-form-item size="small" label="数据库密码">
                                <el-input v-model="data.mysql.password"></el-input>
                            </el-form-item>

                            <el-form-item size="small" label="数据库名称">
                                <el-input v-model="data.mysql.database" :disabled="mysqlDatabaseDisabled"></el-input>
                                <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="randomDatabase('mysql')" :disabled="mysqlDatabaseDisabled">
                                    <div class="md-ripple">
                                        <div class="md-button-content">随机</div>
                                    </div>
                                </button>
                            </el-form-item>
                        </template>

                        <template v-if="data.model == 'sqlite'">
                            <el-form-item size="small" label="数据库文件">
                                <el-input v-model="data.sqlite.database" :disabled="sqliteDatabaseDisabled"></el-input>
                                <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="randomDatabase('sqlite')" :disabled="sqliteDatabaseDisabled">
                                    <div class="md-ripple">
                                        <div class="md-button-content">随机</div>
                                    </div>
                                </button>
                                <span class="jc-form-item-help"><i class="el-icon-info"></i>不包含文件后缀</span>
                            </el-form-item>
                        </template>
                    </el-form>
                </div>
                <div class="jc-install-step-footer">
                    <button type="button" class="md-button md-raised md-primary md-theme-default" @click.stop="update">
                        <div class="md-ripple">
                            <div class="md-button-content">修改</div>
                        </div>
                    </button>
                    <button type="button" class="md-button md-raised md-primary md-theme-default" @click.stop="login">
                        <div class="md-ripple">
                            <div class="md-button-content">后台</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="/themes/backend/js/app.js"></script>
    <script src="/themes/backend/vendor/element-ui/index.js"></script>
    <script src="/themes/backend/js/utils.js"></script>
    <script>
    const app = new Vue({
        el: '#install',
        data() {
            return {
                data: @jjson($data),
                currentStep: 0,
                mysqlDatabaseDisabled: false,
                sqliteDatabaseDisabled: false,

                isMounted: false,
            };
        },

        created() {
            this.data.sqlite.database = this.data.sqlite.database ? this.data.sqlite.database.replace('.db3', '') : '';
            if (this.data.mysql.database.length > 0) this.mysqlDatabaseDisabled = true;
            if (this.data.sqlite.database.length > 0) this.sqliteDatabaseDisabled = true;
        },

        mounted() {
            this.isMounted = true;
        },

        methods: {
            randomDatabase(name) {
                const chars = 'abcdefghijklmnopqrstuvwxyz_0123456789';
                const maxPos = chars.length;
                let db = '';
                for (let i = 0; i < 12; i++) {
                    db += chars.charAt(Math.floor(Math.random() * maxPos));
                }
                this.data[name].database = db;
            },

            update() {
                const form = this.data, loading = this.$loading({
                    lock: true,
                    text: '正在修改 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                switch(form.model) {
                    case 'mysql':
                        if (form.mysql.username.length == 0) {
                            this.$message.error('MySQL的数据库用户名必填');
                            loading.close();
                            return false;
                        }
                        if (form.mysql.database.length == 0) {
                            this.$message.error('MySQL的数据库名称必填');
                            loading.close();
                            return false;
                        }
                        break;

                    case 'sqlite':
                        if (form.sqlite.database.length == 0) {
                            this.$message.error('sqlite的数据库文件必填');
                            loading.close();
                            return false;
                        }
                        break;

                    default:
                        this.$message.error('不支持的数据库类型');
                        loading.close();
                        return false;
                } 

                axios.post('/update/db', this.data).then(response => {
                    loading.close();
                    if (typeof response.data == 'string') {
                        this.$message.error(response.data);
                        return false;
                    }
                    this.$message.success('操作完成');
                }).catch(err => {
                    loading.close();
                    console.error(err);
                    this.$message.error('发生错误，可查看控制台');
                });
            },

            login() {
                location.href = "{{ short_url('admin.login') }}";
            },
        },
    });
    </script>
</body>

</html>