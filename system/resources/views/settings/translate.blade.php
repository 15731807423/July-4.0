@extends('layout')

@section('h1', '翻译配置')

@section('inline-style')
<style type="text/css">
    .el-input {
        width: 100%
    }
</style>
@endsection

@section('main_content')
<el-form id="main_form" ref="main_form" :model="settings" label-position="top">
    <div id="main_form_left">
        <el-form-item prop="translate.mode" size="small" class="has-helptext">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{{ $items['translate.mode']['tips'] }}" placement="right">
                <span>{{ $items['translate.mode']['label'] }}</span>
            </el-tooltip>
            <el-radio-group v-model="settings['translate.mode']">
                <el-radio v-for="item in mode2" :label="item.label" :disabled="false">@{{ item.text }}</el-radio>
            </el-radio-group>
            @if ($items['translate.mode']['description'])
                <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['translate.mode']['description'] }}</span>
            @endif
        </el-form-item>

        <el-form-item prop="translate.code" size="small" class="{{ $items['translate.code']['description'] ? 'has-helptext' : '' }}">
            <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $items['translate.code']['tips'] !!}" placement="right">
                <span>{{ $items['translate.code']['label'] }}</span>
            </el-tooltip>
            <el-input type="textarea" v-model="settings['translate.code']" :rows="7" placeholder="{{ $items['translate.code']['placeholder'] ?? '' }}"></el-input>
            @if ($items['translate.code']['description'])
            <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $items['translate.code']['description'] }}</span>
            @endif
        </el-form-item>

        @include('spec_list.textarea', ['data' => $items['translate.fields'], 'index' => 'translate.fields', 'rows' => 7])

        @include('spec_list.textarea', ['data' => $items['translate.text'], 'index' => 'translate.text', 'rows' => 7])

        @include('spec_list.textarea', ['data' => $items['translate.replace'], 'index' => 'translate.replace', 'rows' => 7])

        <div id="main_form_bottom" class="is-button-item">
            <button type="button" class="md-button md-raised md-dense md-primary md-theme-default" @click.stop="submitMainForm">
                <div class="md-button-content">保存</div>
            </button>
        </div>
    </div>
</el-form>
@endsection

@section('script')
<script>
    let app = new Vue({
        el: '#main_content',

        data() {
            return {
                settings: @jjson($settings)
            }
        },

        computed: {
            mode2() {
                return [
                    { text: '任务模式', label: 'task' },
                    { text: '直接模式', label: 'direct' }
                ];
            }
        },

        created() {
            this.original_settings = _.cloneDeep(this.settings);
        },

        methods: {
            isPlainObject(value) {
                return value !== null && typeof value === 'object' && !Array.isArray(value);
            },

            isStringList(value) {
                return Array.isArray(value) && value.every(item => typeof item === 'string');
            },

            isStringMap(value) {
                return this.isPlainObject(value)
                    && Object.keys(value).every(key => typeof value[key] === 'string');
            },

            isCodeMapping(value) {
                if (Array.isArray(value) && value.length === 0) return true;
                if (!this.isPlainObject(value)) return false;

                const values = Object.keys(value).map(key => value[key]);
                return values.every(item => typeof item === 'string')
                    || values.every(item => this.isStringMap(item));
            },

            isStringListSetting(value) {
                return this.isStringList(value)
                    || (this.isPlainObject(value)
                        && Object.keys(value).every(key => this.isStringList(value[key])));
            },

            isReplacementSetting(value) {
                if (Array.isArray(value) && value.length === 0) return true;
                if (!this.isPlainObject(value)) return false;

                const values = Object.keys(value).map(key => value[key]);
                return values.every(item => typeof item === 'string')
                    || values.every(item => this.isStringMap(item)
                        || (Array.isArray(item) && item.length === 0));
            },

            validateJsonSetting(key, label, validator, structureDescription) {
                const raw = this.settings[key];
                if (raw === null || typeof raw === 'undefined' || raw === '') return null;
                if (typeof raw !== 'string') return label + '必须是 JSON 文本';
                if (!raw.trim()) return null;

                let value;
                try {
                    value = JSON.parse(raw);
                } catch (error) {
                    return label + '不是合法的 JSON，请检查引号、逗号和括号';
                }

                return validator(value) ? null : label + structureDescription;
            },

            validateJsonSettings() {
                const checks = [
                    {
                        key: 'translate.code',
                        label: '代码转换',
                        validator: value => this.isCodeMapping(value),
                        description: '应为语言代码映射对象'
                    },
                    {
                        key: 'translate.fields',
                        label: '全部不翻译的字段',
                        validator: value => this.isStringListSetting(value),
                        description: '应为字符串数组，或按语言分组的字符串数组对象'
                    },
                    {
                        key: 'translate.text',
                        label: '全部不翻译的内容',
                        validator: value => this.isStringListSetting(value),
                        description: '应为字符串数组，或按语言分组的字符串数组对象'
                    },
                    {
                        key: 'translate.replace',
                        label: '指定翻译结果',
                        validator: value => this.isReplacementSetting(value),
                        description: '应为原文与译文的映射对象，或按语言分组的映射对象'
                    }
                ];

                for (let i = 0; i < checks.length; i++) {
                    const check = checks[i];
                    const error = this.validateJsonSetting(
                        check.key,
                        check.label,
                        check.validator,
                        check.description
                    );
                    if (error) {
                        this.$message.error(error);
                        return false;
                    }
                }

                return true;
            },

            getRequestErrorMessage(error) {
                if (!error || !error.response) return '网络连接异常，请检查网络后重试';

                const data = error.response.data;
                if (data && typeof data.message === 'string' && data.message.trim()) {
                    return data.message;
                }

                if (data && data.errors && typeof data.errors === 'object') {
                    const keys = Object.keys(data.errors);
                    if (keys.length) {
                        const first = data.errors[keys[0]];
                        if (Array.isArray(first) && typeof first[0] === 'string') return first[0];
                        if (typeof first === 'string') return first;
                    }
                }

                return '保存翻译设置失败，请稍后重试';
            },

            submitMainForm() {
                if (!this.validateJsonSettings()) return;

                let form = this.$refs.main_form;

                const loading = this.$loading({
                    lock: true,
                    text: '正在保存修改 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                form.validate().then(() => {
                    axios.post("{{ short_url('settings.update', $name) }}", this.settings).then(() => {
                        loading.close();
                        this.original_settings = _.cloneDeep(this.settings);
                        this.$message.success('设置已更新');
                    }).catch(error => {
                        loading.close();
                        this.$message.error(this.getRequestErrorMessage(error));
                    });
                }).catch(() => {
                    loading.close();
                });
            },
        }
    });
</script>
@endsection
