@extends('layout')

@section('h1', $title)

@section('main_content')
    <el-form id="main_form" ref="main_form" :model="settings" :rules="rules" label-position="top">
        <div id="main_form_left">
            @foreach ($items as $key => $item)
                <el-form-item prop="{{ $key }}" size="small" class="{{ isset($item['description'])?'has-helptext':'' }}">
                    <el-tooltip slot="label" content="{!! $item['tips'] !!}" popper-class="jc-twig-output" effect="dark" placement="right">
                        <span>{{ $item['label'] }}</span>
                    </el-tooltip>

                    @if ($key == 'site.mails')
                        <div v-for="(item, key) in settings['site.mails']" class="mail">
                            <el-input v-model="item.name" placeholder="收件人姓名"></el-input>
                            <el-input v-model="item.address" placeholder="收件人地址"></el-input>
                            <el-button type="primary" :disabled="!item.name || !item.address" @click="drawerOpen(key)">收件规则</el-button>
                            <el-button type="danger" :disabled="settings['site.mails'].length < 2" @click="settings['site.mails'].splice(key, 1)">删除</el-button>
                            <el-button
                                v-if="key + 1 == settings['site.mails'].length"
                                type="success"
                                :disabled="!item.name || !item.address"
                                @click="settings['site.mails'].push(_.cloneDeep(mail))"
                            >添加</el-button>
                        </div>
                    @else
                        <el-input v-model="settings['{{ $key }}']" native-size="80"></el-input>
                        @if (isset($item['description']))
                            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $item['description'] }}</span>
                        @endif
                    @endif
                </el-form-item>
            @endforeach

            <div id="main_form_bottom" class="is-button-item">
                <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="submit">
                    <div class="md-button-content">保存</div>
                </button>
            </div>
        </div>
        <div id="main_form_right">
            <h2 class="jc-form-info-item">通用非必填项</h2>
        </div>
    </el-form>

    <el-drawer :title="drawer.title" :visible.sync="drawer.status" size="50%">
        <p>邮箱默认接收所有邮件，你可以在下面配置收件规则，只有符合规则的邮件才会发送到该邮箱；</p>
        <p>你可以添加多条规则，邮件符合其中一条规则就会发送到该邮箱；</p>
        <p>每条规则中可以添加多个条件，同时满足这几个条件时，该规则才算通过；</p>
        <p>你也可以禁止该邮箱接收邮。</p>
        <div>
            <span>是否接收邮件</span>
            <el-switch v-model="drawer.data.receive"></el-switch>
        </div>

        <div v-if="drawer.data.receive" class="rules">
            <el-select v-model="drawer.add.condition" size="small">
                <el-option v-for="(language, code) in languages" :key="code" :value="'language:' + code" :label="'语言：' + language.name"></el-option>
            </el-select>
            <el-select v-model="drawer.add.rule" size="small">
                <el-option v-for="(rule, key) in drawer.data.rules" :key="key" :value="key" :label="'规则' + (key + 1)"></el-option>
                <el-option :value="-1" label="新规则"></el-option>
            </el-select>
            <el-button type="success" size="small" :disabled="!drawer.add.condition || drawer.add.rule === null" @click="ruleAdd">添加</el-button>
        </div>

        <div class="result">
            <p v-if="!drawer.data.receive">当前不接收邮件</p>
            <p v-else-if="drawer.data.rules.length == 0">当前接收全部邮件</p>

            <el-table v-else :data="drawer.data.rules">
                <el-table-column label="序号" type="index" width="60" align="center"></el-table-column>
                <el-table-column label="条件">
                    <template #default="{ row, $index }">
                        <el-tag v-for="(item, index) in row" size="small" :key="item" closable @close="ruleRemove($index, index)">@{{ all[item] }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="80" align="center">
                    <template #default="{ $index }">
                        <el-button type="danger" size="small" @click="ruleRemove($index)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="buttons">
            <el-button type="primary" size="small" @click="rulesSave">保存</el-button>
            <el-button size="small" @click="drawer.status = false">取消</el-button>
        </div>
    </el-drawer>
@endsection

@section('inline-style')
    <style type="text/css">
        .mail {
            margin-bottom: 10px;
        }
        .mail > .el-input, .mail > .el-button {
            margin: 0 12px 0 0;
        }

        .el-drawer__body {
            padding: 20px;
            position: revert;
        }
        .el-drawer__body .rules {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .el-drawer__body .rules > .el-select, .el-drawer__body .rules > .el-button {
            margin: 0 12px 0 0;
        }
        .el-drawer__body .result .el-table .el-tag {
            margin-right: 12px;
        }
        .el-drawer__body .buttons {
            left: 20px;
            bottom: 20px;
            position: absolute;
        }
    </style>
@endsection

@section('script')
<script>
    const app = new Vue({
        el: '#main_content',
        data() {
            return {
                settings: @json($settings, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
                rules: {
                    'site.url': [
                        {required:true, message:'不能为空', trigger:'submit'},
                        {type:'url', message:'格式错误', trigger:'blur'},
                    ],
                    'site.subject': [
                        {required:true, message:'不能为空', trigger:'submit'},
                    ],
                },
                all: {},
                languages: @json(config('lang.available'), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
                mail: {
                    name: '',
                    address: '',
                    receive: true,
                    rules: []
                },
                drawer: {
                    status: false,
                    title: '',
                    key: 0,
                    data: {
                        receive: false,
                        rules: []
                    },
                    add: {
                        condition: null,
                        rule: null
                    }
                },
            };
        },

        created() {
            if (!this.settings['site.mails']) {
                this.settings['site.mails'] = [_.cloneDeep(this.mail)];
            }

            for (let key in this.languages) {
                this.all['language:' + key] = '语言：' + this.languages[key].name;
            }

            this.original_settings = _.cloneDeep(this.settings);
        },

        methods: {
            drawerOpen(key) {
                var data = this.settings['site.mails'][key];
                this.drawer.title = '修改 ' + data.address + ' 邮箱的收件规则';
                this.drawer.key = key;
                this.drawer.data.receive = data.receive;
                this.drawer.data.rules = data.rules;
                this.drawer.status = true;
            },

            ruleAdd() {
                if (this.drawer.add.rule == -1) {
                    this.drawer.data.rules.push([this.drawer.add.condition]);
                } else {
                    if (this.drawer.data.rules[this.drawer.add.rule].indexOf(this.drawer.add.condition) === -1) {
                        this.drawer.data.rules[this.drawer.add.rule].push(this.drawer.add.condition);
                    }
                }

                this.drawer.add.condition = this.drawer.add.rule = null;
            },

            ruleRemove(rule, condition) {
                if (condition === undefined) {
                    this.drawer.data.rules.splice(rule, 1);
                } else {
                    this.drawer.data.rules[rule].splice(condition, 1);
                    if (this.drawer.data.rules[rule].length == 0) {
                        this.drawer.data.rules.splice(rule, 1);
                    }
                }
            },

            rulesSave() {
                this.settings['site.mails'][this.drawer.key].rules = this.drawer.data.rules;
                this.settings['site.mails'][this.drawer.key].receive = this.drawer.data.receive;
                this.drawer.status = false;
            },

            mailsVerify() {
                var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                for (var i = 0; i < this.settings['site.mails'].length; i++) {
                    let data = this.settings['site.mails'][i];

                    if (!data.name) {
                        this.$message.error('第' + (i + 1) + '个收件人没有姓名');
                        return false;
                    }

                    if (!data.address) {
                        this.$message.error('第' + (i + 1) + '个收件人没有地址');
                        return false;
                    }

                    if (!pattern.test(data.address)) {
                        this.$message.error('第' + (i + 1) + '个收件人的地址有误');
                        return false;
                    }
                }

                return true;
            },

            submit() {
                if (_.isEqual(this.settings, this.original_settings)) {
                    this.$message.info('未作任何更改');
                    return;
                }

                if (!this.mailsVerify()) return ;

                const loading = this.$loading({
                    lock: true,
                    text: '正在更新设置 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                this.$refs.main_form.validate().then(() => {
                    axios.post("{{ short_url('settings.update', $name) }}", this.settings).then(response => {
                        loading.close();
                        this.original_settings = _.cloneDeep(this.settings);

                        this.$message.success('设置已更新');
                    }).catch(err => {
                        loading.close();
                        console.error(err);
                        this.$message.error('发生错误，可查看控制台');
                    });
                }).catch(() => {
                    loading.close();
                });
            },
        },
    });
</script>
@endsection
