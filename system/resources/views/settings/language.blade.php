@extends('layout')

@section('h1', $title)

@section('main_content')
<el-form id="main_form" ref="main_form" :model="settings" label-position="top">
    <div id="main_form_left">
        <el-form-item prop="lang.multiple" size="small" class="{{ $items['lang.multiple']['description']?'has-helptext':'' }}">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $items['lang.multiple']['tips'] !!}" placement="right">
                <span>{{ $items['lang.multiple']['label'] }}</span>
            </el-tooltip>
            <el-switch v-model="settings['lang.multiple']" active-text="启用" inactive-text="不启用">
            </el-switch>
            @if ($items['lang.multiple']['description'])
            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['lang.multiple']['description'] }}</span>
            @endif
        </el-form-item>
        <div class="el-form-item el-form-item--small jc-embeded-field {{ $items['lang.available']['description']?'has-helptext':'' }}" v-if="settings['lang.multiple']">
            <div class="el-form-item__content">
                <div class="jc-embeded-field__header">
                    <el-tooltip popper-class="jc-twig-output" effect="dark" content="{!! $items['lang.available']['tips'] !!}" placement="right">
                        <label class="el-form-item__label">{{ $items['lang.available']['label'] }}</label>
                    </el-tooltip>
                    <div class="jc-embeded-field__buttons">
                        <el-select v-model="selected" placeholder="--选择语言--" size="small" filterable>
                            <el-option v-for="(langname, langcode) in langnames" :key="langcode" :label="langname" :value="langname">
                            </el-option>
                        </el-select>
                        <el-input v-model="langcode" size="small" placeholder="输入代码"></el-input>
                        <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="addLanguage" :disabled="!isLangcodeSelectable">
                            <div class="md-ripple">
                                <div class="md-button-content">添加到列表</div>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="jc-table-wrapper">
                    <table class="jc-table jc-dense is-draggable with-operators">
                        <!-- <colgroup>
                            <col width="180px">
                            <col width="auto">
                            <col width="auto">
                            <col width="100px">
                        </colgroup> -->
                        <thead>
                            <tr>
                                <th></th>
                                <th>语言 [代码]</th>
                                <th>可翻译</th>
                                <th>可访问</th>
                                <th>图标</th>
                                <th>生成模板</th>
                                <th>删除</th>
                            </tr>
                        </thead>
                        <tbody is="draggable" v-model="settings['lang.available']" :animation="150" ghost-class="jc-drag-ghost" handle=".jc-drag-handle" tag="tbody">
                            <tr v-for="(info, key) in settings['lang.available']" :key="key">
                                <td>
                                    <i class="md-icon md-icon-font md-theme-default jc-drag-handle">swap_vert</i>
                                </td>
                                <td>
                                    <span>@{{ info.name+' ['+info.key+']' }}</span>
                                </td>
                                <td>
                                    <el-switch v-model="info['translatable']" :disabled="info.key==='en'" @change="handleTranslatableChange(info.key)"></el-switch>
                                </td>
                                <td>
                                    <el-switch v-model="info['accessible']" :disabled="info.key==='en'" @change="handleAccessibleChange(info.key)"></el-switch>
                                </td>
                                <td>
                                    <el-upload
                                    class="upload-demo"
                                    ref="upload"
                                    name="files"
                                    action="{{ short_url('media.upload') }}"
                                    :limit="1"
                                    :on-success="uploadSuccess"
                                    :auto-upload="true"
                                    :show-file-list="false"
                                    :data="data">
                                        <div class="jc-operators">
                                            <button type="button" class="md-button md-icon-button md-primary md-theme-default" title="上传" @click="select(info.key)">
                                                <img v-if="info.icon" class="icon" :src="info.icon">
                                                <i v-else class="md-icon md-icon-font md-theme-default">upload</i>
                                            </button>
                                        </div>
                                    </el-upload>
                                </td>
                                <td>
                                    <div class="jc-operators">
                                        <!-- <el-button type="primary" :disabled="info.key == 'en' || !info.translatable || !info.accessible" @click.stop="generateTemplate(info.key)">生成模板</el-button> -->
                                        <button type="button" class="md-button md-icon-button md-primary md-theme-default" title="生成" :disabled="info.key == 'en' || !info.translatable || !info.accessible" @click.stop="generateTemplate(info.key)">
                                            <i class="md-icon md-icon-font md-theme-default">done</i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="jc-operators">
                                        <button type="button" class="md-button md-icon-button md-accent md-theme-default" title="删除" :disabled="isReserved(info.key)" @click.stop="removeLanguage(info.key)">
                                            <i class="md-icon md-icon-font md-theme-default">remove_circle</i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if ($items['lang.available']['description'])
                <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['lang.available']['description'] }}</span>
                @endif
            </div>
        </div>
        <el-form-item prop="lang.icon" size="small" class="{{ $items['lang.icon']['description']?'has-helptext':'' }}" v-if="settings['lang.multiple']">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $items['lang.icon']['tips'] !!}" placement="right">
                <span>{{ $items['lang.icon']['label'] }}</span>
            </el-tooltip>
            <el-switch v-model="settings['lang.icon']" active-text="启用" inactive-text="不启用">
            </el-switch>
            @if ($items['lang.icon']['description'])
            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['lang.icon']['description'] }}</span>
            @endif
        </el-form-item>
        <el-form-item size="small" class="{{ $items['lang.content']['description']?'has-helptext':'' }}" v-if="settings['lang.multiple']">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $items['lang.content']['tips'] !!}" placement="right">
                <span>{{ $items['lang.content']['label'] }}</span>
            </el-tooltip>
            <el-select v-model="settings['lang.content']">
                <el-option v-for="langcode in translatableLangcodes" :key="langcode" :label="'['+langcode+'] '+ getInfoByCode(langcode).name" :value="langcode">
                </el-option>
            </el-select>
            @if ($items['lang.content']['description'])
            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['lang.content']['description'] }}</span>
            @endif
        </el-form-item>
        <el-form-item size="small" class="{{ $items['lang.frontend']['description']?'has-helptext':'' }}" v-if="settings['lang.multiple']">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $items['lang.frontend']['tips'] !!}" placement="right">
                <span>{{ $items['lang.frontend']['label'] }}</span>
            </el-tooltip>
            <el-select v-model="settings['lang.frontend']">
                <el-option v-for="langcode in accessibleLangcodes" :key="langcode" :label="'['+langcode+'] '+ getInfoByCode(langcode).name" :value="langcode">
                </el-option>
            </el-select>
            @if ($items['lang.frontend']['description'])
            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['lang.frontend']['description'] }}</span>
            @endif
        </el-form-item>
        <div id="main_form_bottom" class="is-button-item">
            <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="submit">
                <div class="md-button-content">保存</div>
            </button>
        </div>
    </div>
</el-form>
@endsection

@section('script')
<script>
const app = new Vue({
    el: '#main_content',
    data() {
        return {
            settings: {},
            selected: null,
            langnames: @jjson(lang()->getLangnames()),
            langcode: '',
            fileList: [],
            uploadCode: '',
            data: {
                _token: '{{ csrf_token() }}',
                cwd: 'images',
            }
        };
    },

    created() {
        var data = @jjson($settings), available = [];
        for (let key in data['lang.available']) {
            data['lang.available'][key].key = key;
        }
        data['lang.available'] = Object.values(data['lang.available']);
        this.settings = data;
        this.original_settings = _.cloneDeep(this.settings);
    },

    computed: {
        isLangcodeSelectable() {
            const code = this.langcode, name = this.selected;
            return code && name && !this.settings['lang.available'][code] && this.checkLanguageName(name);
        },

        translatableLangcodes() {
            const list = this.settings['lang.available'], langcodes = [];
            for (var i = 0; i < list.length; i++) {
                if (list[i].translatable) {
                    langcodes.push(list[i].key);
                }
            }
            return langcodes;
        },

        accessibleLangcodes() {
            const list = this.settings['lang.available'], langcodes = [];
            for (var i = 0; i < list.length; i++) {
                if (list[i].accessible) {
                    langcodes.push(list[i].key);
                }
            }
            return langcodes;
        },

        fieldTypeHelper() {console.log(this.mold.globalFields)
            const meta = this.fieldTypes[this.newField.model.field_type];
            if (meta) {
                return meta.description;
            }
            return '（请选择字段类型）';
        },

        fieldMetakeys() {console.log(this.mold.globalFields)
            const meta = this.fieldTypes[this.newField.model.field_type];
            if (meta) {
                return meta.metakeys;
            }
            return [];
        },
    },

    methods: {
        // 添加语言到可用列表
        addLanguage() {
            if (!this.isLangcodeSelectable) {
                return;
            }
            const code = this.langcode, name = this.selected;
            if (code.length > 5) {
                this.$message.warning('代码最长5位');
            } else if (this.settings['lang.available'][code]) {
                this.$message.warning('代码已存在');
            } else if (!this.checkLanguageName(name)) {
                this.$message.warning('语言已存在');
            } else {
                const list = _.cloneDeep(this.settings['lang.available']);
                for (let key in this.langnames) {
                    if (this.langnames[key] == name) var code2 = key;
                }
                list.push({
                    translatable: true,
                    accessible: true,
                    name: name,
                    code: code2,
                    key: code
                })
                this.$set(this.settings, 'lang.available', list);
                this.selected = null;
                this.langcode = null;
            }
        },

        // 根据语言代码获取信息
        getInfoByCode(code) {
            var data = {};
            for (var i = 0; i < this.settings['lang.available'].length; i++) {
                if (this.settings['lang.available'][i].key == code) data = this.settings['lang.available'][i];
            }
            return data;
        },

        // 判断一个语言是否存在
        checkLanguageName(name) {
            for (var i = 0; i < this.settings['lang.available'].length; i++) {
                if (name == this.settings['lang.available'][i].name) return false;
            }
            return true;
        },

        // 从可用列表移除指定语言
        removeLanguage(langcode) {
            for (var i = 0; i < this.settings['lang.available'].length; i++) {
                if (this.settings['lang.available'][i].key == langcode) {
                    var list = _.cloneDeep(this.settings['lang.available']);
                    list.splice(i, 1);
                    this.$set(this.settings, 'lang.available', list);
                }
            }
            if (this.settings['lang.available'][langcode]) {
                const list = _.cloneDeep(this.settings['lang.available']);
                delete list[langcode];
                this.$set(this.settings, 'lang.available', list);
            }

            this.resetDefaultLangcode('lang.content', langcode);
            this.resetDefaultLangcode('lang.frontend', langcode);
        },

        // 如果设置值指向了一个不可用的语言（已从可用列表移除），则重置为初始值
        resetDefaultLangcode(key, langcode) {
            if (this.settings[key] === langcode) {
                this.settings[key] = this.original_settings[key];
            }
        },

        // 响应语言可访问性改变事件
        handleAccessibleChange(langcode) {
            const config = this.getInfoByCode(langcode);
            if (config['accessible'] && !config['translatable']) {
                config['translatable'] = true;
            }
            if (!config['accessible']) {
                this.resetDefaultLangcode('lang.frontend', langcode);
            }
        },

        // 响应语言可翻译性改变事件
        handleTranslatableChange(langcode) {
            const config = this.getInfoByCode(langcode);
            if (config['accessible'] && !config['translatable']) {
                config['accessible'] = false;
            }

            if (!config['accessible']) {
                this.resetDefaultLangcode('lang.frontend', langcode);
            }
            if (!config['translatable']) {
                this.resetDefaultLangcode('lang.content', langcode);
            }
        },

        // 是否预留设置，预留设置不可更改
        isReserved(langcode) {
            return langcode === 'en' || langcode === 'zh';
        },

        // 确定上传图标的语言
        select(code) {
            this.uploadCode = code;
        },

        // 上传成功
        uploadSuccess(response, file, fileList) {
            var _this = this, path = '/images/' + file.name;
            $('.upload-demo').each(function (index) {
                _this.$refs.upload[index].clearFiles();
            });
            var data = this.getInfoByCode(this.uploadCode);
            data.icon = path;
            this.$forceUpdate();
        },

        // 生成模板
        generateTemplate(code) {
            const loading = this.$loading({
                lock: true,
                text: '正在生成模板 ...',
                background: 'rgba(255, 255, 255, 0.7)',
            });

            axios.post("{{ short_url('manage.translate.tpl', 'code') }}".replace('code', code), {}).then(response => {
                loading.close();
                if (typeof response.data == 'string') {
                    this.$message.error(response.data);
                } else {
                    this.$message.success('生成模板成功');
                }
            }).catch(err => {
                loading.close();
                console.error(err);
                this.$message.error('发生错误，可查看控制台');
            });
        },

        // 提交
        submit() {
            if (_.isEqual(this.settings, this.original_settings)) {
                this.$message.info('未作任何更改');
                return;
            }

            const loading = this.$loading({
                lock: true,
                text: '正在更新设置 ...',
                background: 'rgba(255, 255, 255, 0.7)',
            });

            this.$refs.main_form.validate().then(() => {
                var data = JSON.parse(JSON.stringify(this.settings)), available = {};
                for (var i = 0; i < this.settings['lang.available'].length; i++) {
                    available[this.settings['lang.available'][i].key] = JSON.parse(JSON.stringify(this.settings['lang.available'][i]));
                    delete available[this.settings['lang.available'][i].key].key;
                }
                data['lang.available'] = available;

                axios.post("{{ short_url('settings.update', $name) }}", data).then(response => {
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
