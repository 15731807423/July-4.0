@extends('layout')

@section('h1', '规格列表配置')

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

        @include('spec_list.textarea', ['data' => $items['translate.fields'], 'index' => 'translate.fields', 'rows' => 7])

        @include('spec_list.textarea', ['data' => $items['translate.text'], 'index' => 'translate.text', 'rows' => 7])

        @include('spec_list.textarea', ['data' => $items['translate.replace'], 'index' => 'translate.replace', 'rows' => 7])

        <p>‘全部不翻译的字段’、‘全部不翻译的内容’和‘指定翻译结果’编辑时需要严格遵循PHP语法：</p>
        <p>索引数组：['value1','value2','value3'...]。数组用中括号表示，数组中的多个键值用逗号隔开，字符串值用引号引起来。</p>
        <p>关联数组：['key1'=>'value1','key2'=>'value2'...]。数组用中括号表示，数组中每个键值都有一个键名，键名和键值之间用‘=>’，多个值之间用逗号隔开。</p>
        <p>首先确定是否区分语言，如果不区分语言，表示对所有语言设置。如果区分语言，用语言设置里设置的语言代码做键名。上面的占位文本添加的回车和空格是为了看得更清楚，写的时候可以不加。</p>
        <p>‘全部不翻译的字段’和‘全部不翻译的内容’：如果不区分语言，用索引数组设置内容。</p>
        <p>‘全部不翻译的字段’和‘全部不翻译的内容’：如果区分语言，用关联数组设置内容，键名为语言代码，键值为索引数组，表示该语言设置的内容。</p>
        <p>‘指定翻译结果’：如果不区分语言，用关联数组设置内容，键名为被翻译的内容，键值为指定的翻译结果。</p>
        <p>‘指定翻译结果’：如果区分语言，用关联数组设置内容，键名为语言代码，键值为该语言设置的内容。</p>

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
