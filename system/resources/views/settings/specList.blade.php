@extends('layout')

@section('h1', '规格列表配置')

@section('inline-style')
<style type="text/css">
    .indent-1 {
        padding-left: 20px;
    }
    .indent-2 {
        padding-left: 40px;
    }
    .split-line {
        margin: 0;
        padding: 15px 0 0;
        border-top: 1px dashed #aaa;
    }
</style>
@endsection

@section('main_content')
<el-form id="main_form" ref="main_form" :model="settings" label-position="top">
    <div id="main_form_left">

        <!-- 切换列表模式 -->
        @include('spec_list.radio_button', [
            'data' => $items['specList.model'],
            'index' => 'specList.model',
            'list' => [
                ['text' => 'PHP处理数据', 'label' => 'dynamic'],
                ['text' => 'JS处理数据', 'label' => 'static']
            ]
        ])

        <p class="split-line"></p>

        <!-- 切割符号 -->
        @include('spec_list.input', ['data' => $items['specList.cuttingSymbol'], 'index' => 'specList.cuttingSymbol'])

        <p class="split-line"></p>

        <!-- 数据为空的提示 -->
        @include('spec_list.input', ['data' => $items['specList.dataEmptyText'], 'index' => 'specList.dataEmptyText'])

        <p class="split-line"></p>

        <!-- 排序大小写敏感 -->
        @include('spec_list.switch', ['data' => $items['specList.sortCaseSensitive'], 'index' => 'specList.sortCaseSensitive'])

        <p class="split-line"></p>

        <!-- JS处理数据模式下 -->
        <template v-if="settings['specList.model'] == 'static'">
            <!-- 搜索功能的状态 -->
            @include('spec_list.switch', ['data' => $items['specList.static.search.status'], 'index' => 'specList.static.search.status'])

            <template v-if="settings['specList.static.search.status']">
                <!-- 默认值 -->
                @include('spec_list.input', ['data' => $items['specList.static.search.default'], 'index' => 'specList.static.search.default', 'class' => 'indent-1'])

                <!-- 大小写敏感 -->
                @include('spec_list.switch', ['data' => $items['specList.static.search.caseSensitive'], 'index' => 'specList.static.search.caseSensitive', 'class' => 'indent-1'])

                <!-- class -->
                @include('spec_list.input', ['data' => $items['specList.static.search.class'], 'index' => 'specList.static.search.class', 'class' => 'indent-1'])

                <!-- 搜索框的input事件触发搜索 -->
                @include('spec_list.switch', ['data' => $items['specList.static.search.inputConfig.onInput'], 'index' => 'specList.static.search.inputConfig.onInput', 'class' => 'indent-1'])

                <!-- 搜索框的change事件触发搜索 -->
                @include('spec_list.switch', ['data' => $items['specList.static.search.inputConfig.onChange'], 'index' => 'specList.static.search.inputConfig.onChange', 'class' => 'indent-1'])

                <!-- 搜索框的class -->
                @include('spec_list.input', ['data' => $items['specList.static.search.inputConfig.class'], 'index' => 'specList.static.search.inputConfig.class', 'class' => 'indent-1'])

                <!-- 搜索框的组件配置 -->
                @include('spec_list.textarea', ['data' => $items['specList.static.search.inputConfig.componentConfig'], 'index' => 'specList.static.search.inputConfig.componentConfig', 'class' => 'indent-1'])

                <!-- 搜索按钮的状态 -->
                @include('spec_list.switch', ['data' => $items['specList.static.search.buttonConfig.status'], 'index' => 'specList.static.search.buttonConfig.status', 'class' => 'indent-1'])

                <template v-if="settings['specList.static.search.buttonConfig.status']">
                    <!-- 搜索按钮的文本 -->
                    @include('spec_list.input', ['data' => $items['specList.static.search.buttonConfig.text'], 'index' => 'specList.static.search.buttonConfig.text', 'class' => 'indent-2'])

                    <!-- 搜索按钮的class -->
                    @include('spec_list.input', ['data' => $items['specList.static.search.buttonConfig.class'], 'index' => 'specList.static.search.buttonConfig.class', 'class' => 'indent-2'])

                    <!-- 搜索按钮的组件配置 -->
                    @include('spec_list.textarea', ['data' => $items['specList.static.search.buttonConfig.componentConfig'], 'index' => 'specList.static.search.buttonConfig.componentConfig', 'class' => 'indent-2'])
                </template>
            </template>

            <p class="split-line"></p>

            <!-- 筛选功能 -->
            @include('spec_list.switch', ['data' => $items['specList.static.screen.status'], 'index' => 'specList.static.screen.status'])

            <template v-if="settings['specList.static.screen.status']">
                <!-- 是否启用显示已筛选项 -->
                @include('spec_list.switch', ['data' => $items['specList.static.screen.userStatus'], 'index' => 'specList.static.screen.userStatus', 'class' => 'indent-1'])

                <template v-if="settings['specList.static.screen.userStatus']">
                    <!-- 清空已筛选的文本 -->
                    @include('spec_list.input', ['data' => $items['specList.static.screen.clearText'], 'index' => 'specList.static.screen.clearText', 'class' => 'indent-2'])

                    <!-- class -->
                    @include('spec_list.input', ['data' => $items['specList.static.screen.selectedClass'], 'index' => 'specList.static.screen.selectedClass', 'class' => 'indent-2'])
                </template>

                <!-- 显示筛选项后面的数值 -->
                @include('spec_list.switch', ['data' => $items['specList.static.screen.countStatus'], 'index' => 'specList.static.screen.countStatus', 'class' => 'indent-1'])

                <template v-if="settings['specList.static.screen.countStatus']">
                    <!-- 允许计算数值的筛选组类型 -->
                    @include('spec_list.checkbox', [
                        'data' => $items['specList.static.screen.groupCountType'],
                        'index' => 'specList.static.screen.groupCountType',
                        'class' => 'indent-2',
                        'list' => [
                            ['text' => '单选', 'label' => 1],
                            ['text' => '多选', 'label' => 2],
                            ['text' => '滑块', 'label' => 3, 'disabled' => true],
                            ['text' => '日期范围', 'label' => 4, 'disabled' => true],
                            ['text' => '下拉菜单', 'label' => 5]
                        ]
                    ])
                </template>

                <!-- 筛选组之间的关联方式 -->
                @include('spec_list.radio', [
                    'data' => $items['specList.static.screen.type'],
                    'index' => 'specList.static.screen.type',
                    'class' => 'indent-1',
                    'list' => [
                        ['text' => '1', 'label' => 1],
                        ['text' => '2', 'label' => 2],
                        ['text' => '3', 'label' => 3],
                        ['text' => '4', 'label' => 4]
                    ]
                ])

                <!-- 隐藏对应数据数量为0的选项 -->
                @include('spec_list.switch', ['data' => $items['specList.static.screen.nullHidden'], 'index' => 'specList.static.screen.nullHidden', 'class' => 'indent-1'])

                <!-- class -->
                @include('spec_list.input', ['data' => $items['specList.static.screen.class'], 'index' => 'specList.static.screen.class', 'class' => 'indent-1'])

                <!-- class -->
                @include('spec_list.input', ['data' => $items['specList.static.screen.allClass'], 'index' => 'specList.static.screen.allClass', 'class' => 'indent-1'])
            </template>

            <p class="split-line"></p>

            <!-- class -->
            @include('spec_list.input', ['data' => $items['specList.static.selector.class'], 'index' => 'specList.static.selector.class'])

            <!-- 表格按钮文本 -->
            @include('spec_list.input', ['data' => $items['specList.static.selector.list.table.text'], 'index' => 'specList.static.selector.list.table.text'])

            <!-- 列表按钮文本 -->
            @include('spec_list.input', ['data' => $items['specList.static.selector.list.list.text'], 'index' => 'specList.static.selector.list.list.text'])

            <!-- 模式选择器组件的配置信息 -->
            @include('spec_list.textarea', ['data' => $items['specList.static.selector.config.componentConfig'], 'index' => 'specList.static.selector.config.componentConfig'])

            <p class="split-line"></p>

            <!-- class -->
            @include('spec_list.input', ['data' => $items['specList.static.pagination.class'], 'index' => 'specList.static.pagination.class'])

            <!-- 每页数量 -->
            @include('spec_list.input', ['data' => $items['specList.static.pagination.pageSize'], 'index' => 'specList.static.pagination.pageSize'])

            <!-- 默认页 -->
            @include('spec_list.input', ['data' => $items['specList.static.pagination.currentPage'], 'index' => 'specList.static.pagination.currentPage'])

            <!-- 分页组件的配置信息 -->
            @include('spec_list.textarea', ['data' => $items['specList.static.pagination.componentConfig'], 'index' => 'specList.static.pagination.componentConfig'])

            <p class="split-line"></p>

            <!-- 加载功能 -->
            @include('spec_list.switch', ['data' => $items['specList.static.loading.status'], 'index' => 'specList.static.loading.status'])

            <template v-if="settings['specList.static.loading.status']">
                <!-- 加载组件的配置信息 -->
                @include('spec_list.textarea', ['data' => $items['specList.static.loading.config.componentConfig'], 'index' => 'specList.static.loading.config.componentConfig', 'class' => 'indent-1'])
            </template>

            <p class="split-line"></p>

            <!-- 展示全部规格时‘规格’信息的配置 -->
            @include('spec_list.switch', ['data' => $items['specList.static.specAll.status'], 'index' => 'specList.static.specAll.status'])

            <template v-if="settings['specList.static.specAll.status']">
                <!-- 标题 -->
                @include('spec_list.input', ['data' => $items['specList.static.specAll.title'], 'index' => 'specList.static.specAll.title', 'class' => 'indent-1'])

                <!-- 可排序 -->
                @include('spec_list.switch', ['data' => $items['specList.static.specAll.sortable'], 'index' => 'specList.static.specAll.sortable', 'class' => 'indent-1'])

                <!-- 可搜索 -->
                @include('spec_list.switch', ['data' => $items['specList.static.specAll.searchable'], 'index' => 'specList.static.specAll.searchable', 'class' => 'indent-1'])

                <!-- 可筛选 -->
                @include('spec_list.switch', ['data' => $items['specList.static.specAll.screenable'], 'index' => 'specList.static.specAll.screenable', 'class' => 'indent-1'])

                <template v-if="settings['specList.static.specAll.screenable']">
                    <!-- 筛选类型 -->
                    @include('spec_list.radio', [
                        'data' => $items['specList.static.specAll.screenType'],
                        'index' => 'specList.static.specAll.screenType',
                        'class' => 'indent-2',
                        'list' => [
                            ['text' => '单选', 'label' => 1],
                            ['text' => '多选', 'label' => 2],
                            ['text' => '滑块', 'label' => 3],
                            ['text' => '时间', 'label' => 4],
                            ['text' => '下拉菜单', 'label' => 5],
                        ]
                    ])

                    <!-- 筛选默认值 -->
                    @include('spec_list.textarea', ['data' => $items['specList.static.specAll.screenDefault'], 'index' => 'specList.static.specAll.screenDefault', 'class' => 'indent-2'])

                    <!-- 组件配置 -->
                    @include('spec_list.textarea', ['data' => $items['specList.static.specAll.screenConfig'], 'index' => 'specList.static.specAll.screenConfig', 'class' => 'indent-2'])

                    <!-- 组件group配置 -->
                    <template v-if="settings['specList.static.specAll.screenType'] == 1 || settings['specList.static.specAll.screenType'] == 2">
                        @include('spec_list.textarea', ['data' => $items['specList.static.specAll.screenGroupConfig'], 'index' => 'specList.static.specAll.screenGroupConfig', 'class' => 'indent-2'])
                    </template>
                </template>
            </template>
        </template>

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
                        window.location.href = "{{ short_url('manage.specs.index') }}";
                        loading.close();
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
