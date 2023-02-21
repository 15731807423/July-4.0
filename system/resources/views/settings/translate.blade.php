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
        @php ($list = [])
        @foreach (config('translate.list') as $key => $value)
            @php ($list[] = [
                'text'  => $value['name'],
                'label' => $key,
                'mode'  => $value['mode']
            ])
        @endforeach

        @include('spec_list.radio', ['data' => $items['translate.tool'], 'index' => 'translate.tool', 'list' => $list])

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
                settings: @jjson($settings),
                list: @jjson($list)
            }
        },

        computed: {
            mode2() {
                var mode = [{ text: '直接返回结果', label: 'direct'}];

                for (var i = 0; i < this.list.length; i++) {
                    if (this.list[i].label != this.settings['translate.tool']) continue;

                    if (this.list[i].mode == 'task') mode.unshift({ text: '创建任务后获取任务结果', label: 'task'});
                }

                return mode;
            }
        },

        created() {
            this.original_settings = _.cloneDeep(this.settings);
        },

        methods: {
            submitMainForm() {
                let form = this.$refs.main_form;

                const loading = app.$loading({
                    lock: true,
                    text: '正在保存修改 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                form.validate().then(() => {
                    axios.post("{{ short_url('settings.update', $name) }}", this.settings).then(function(response) {
                        loading.close();
                        this.original_settings = _.cloneDeep(this.settings);
                        app.$message.success('设置已更新');
                    }).catch(function(error) {
                        loading.close();
                        console.error(error);
                        app.$message.error('发生错误，可查看控制台');
                    });
                }).catch(function(error) {
                    loading.close();
                    console.error(error);
                })
            },
        }
    });
</script>
@endsection
