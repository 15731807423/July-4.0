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

        <!-- 搜索功能的状态 -->
        @include('spec_list.switch', ['data' => $items['specList.search.status'], 'index' => 'specList.search.status'])

        <template v-if="settings['specList.search.status']">
            <!-- 默认值 -->
            @include('spec_list.input', ['data' => $items['specList.search.default'], 'index' => 'specList.search.default', 'class' => 'indent-1'])

            <!-- 大小写敏感 -->
            @include('spec_list.switch', ['data' => $items['specList.search.caseSensitive'], 'index' => 'specList.search.caseSensitive', 'class' => 'indent-1'])

            <!-- class -->
            @include('spec_list.input', ['data' => $items['specList.search.class'], 'index' => 'specList.search.class', 'class' => 'indent-1'])

            <!-- 搜索框的input事件触发搜索 -->
            @include('spec_list.switch', ['data' => $items['specList.search.inputConfig.onInput'], 'index' => 'specList.search.inputConfig.onInput', 'class' => 'indent-1'])

            <!-- 搜索框的change事件触发搜索 -->
            @include('spec_list.switch', ['data' => $items['specList.search.inputConfig.onChange'], 'index' => 'specList.search.inputConfig.onChange', 'class' => 'indent-1'])

            <!-- 搜索框的class -->
            @include('spec_list.input', ['data' => $items['specList.search.inputConfig.class'], 'index' => 'specList.search.inputConfig.class', 'class' => 'indent-1'])

            <!-- 搜索框的组件配置 -->
            @include('spec_list.textarea', ['data' => $items['specList.search.inputConfig.componentConfig'], 'index' => 'specList.search.inputConfig.componentConfig', 'class' => 'indent-1'])

            <!-- 搜索按钮的状态 -->
            @include('spec_list.switch', ['data' => $items['specList.search.buttonConfig.status'], 'index' => 'specList.search.buttonConfig.status', 'class' => 'indent-1'])

            <template v-if="settings['specList.search.buttonConfig.status']">
                <!-- 搜索按钮的文本 -->
                @include('spec_list.input', ['data' => $items['specList.search.buttonConfig.text'], 'index' => 'specList.search.buttonConfig.text', 'class' => 'indent-2'])

                <!-- 搜索按钮的class -->
                @include('spec_list.input', ['data' => $items['specList.search.buttonConfig.class'], 'index' => 'specList.search.buttonConfig.class', 'class' => 'indent-2'])

                <!-- 搜索按钮的组件配置 -->
                @include('spec_list.textarea', ['data' => $items['specList.search.buttonConfig.componentConfig'], 'index' => 'specList.search.buttonConfig.componentConfig', 'class' => 'indent-2'])
            </template>
        </template>

        <p class="split-line"></p>

        <!-- 筛选功能 -->
        @include('spec_list.switch', ['data' => $items['specList.screen.status'], 'index' => 'specList.screen.status'])

        <template v-if="settings['specList.screen.status']">
            <!-- 是否启用显示已筛选项 -->
            @include('spec_list.switch', ['data' => $items['specList.screen.userStatus'], 'index' => 'specList.screen.userStatus', 'class' => 'indent-1'])

            <template v-if="settings['specList.screen.userStatus']">
                <!-- 清空已筛选的文本 -->
                @include('spec_list.input', ['data' => $items['specList.screen.clearText'], 'index' => 'specList.screen.clearText', 'class' => 'indent-2'])

                <!-- class -->
                @include('spec_list.input', ['data' => $items['specList.screen.selectedClass'], 'index' => 'specList.screen.selectedClass', 'class' => 'indent-2'])
            </template>

            <!-- 显示筛选项后面的数值 -->
            @include('spec_list.switch', ['data' => $items['specList.screen.countStatus'], 'index' => 'specList.screen.countStatus', 'class' => 'indent-1'])

            <template v-if="settings['specList.screen.countStatus']">
                <!-- 允许计算数值的筛选组类型 -->
                @include('spec_list.checkbox', [
                    'data' => $items['specList.screen.groupCountType'],
                    'index' => 'specList.screen.groupCountType',
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
                'data' => $items['specList.screen.type'],
                'index' => 'specList.screen.type',
                'class' => 'indent-1',
                'list' => [
                    ['text' => '1', 'label' => 1],
                    ['text' => '2', 'label' => 2],
                    ['text' => '3', 'label' => 3],
                    ['text' => '4', 'label' => 4]
                ]
            ])

            <!-- 隐藏对应数据数量为0的选项 -->
            @include('spec_list.switch', ['data' => $items['specList.screen.nullHidden'], 'index' => 'specList.screen.nullHidden', 'class' => 'indent-1'])

            <!-- class -->
            @include('spec_list.input', ['data' => $items['specList.screen.class'], 'index' => 'specList.screen.class', 'class' => 'indent-1'])

            <!-- class -->
            @include('spec_list.input', ['data' => $items['specList.screen.allClass'], 'index' => 'specList.screen.allClass', 'class' => 'indent-1'])
        </template>

        <p class="split-line"></p>

        <template v-if="settings['specList.search.status'] || settings['specList.screen.status']">
            <!-- 重置全部按钮的状态 -->
            @include('spec_list.switch', ['data' => $items['specList.reset.status'], 'index' => 'specList.reset.status'])

            <template v-if="settings['specList.reset.status']">
                <!-- 重置按钮的文本 -->
                @include('spec_list.input', ['data' => $items['specList.reset.text'], 'index' => 'specList.reset.text', 'class' => 'indent-2'])

                <!-- 重置按钮的class -->
                @include('spec_list.input', ['data' => $items['specList.reset.class'], 'index' => 'specList.reset.class', 'class' => 'indent-2'])

                <!-- 重置按钮的组件配置 -->
                @include('spec_list.textarea', ['data' => $items['specList.reset.componentConfig'], 'index' => 'specList.reset.componentConfig', 'class' => 'indent-2'])
            </template>

            <p class="split-line"></p>
        </template>

        <!-- class -->
        @include('spec_list.input', ['data' => $items['specList.selector.class'], 'index' => 'specList.selector.class'])

        <!-- 表格按钮文本 -->
        @include('spec_list.input', ['data' => $items['specList.selector.list.table.text'], 'index' => 'specList.selector.list.table.text'])

        <!-- 表格默认 -->
        @include('spec_list.switch', ['data' => $items['specList.selector.list.table.default'], 'index' => 'specList.selector.list.table.default'])

        <!-- 列表按钮文本 -->
        @include('spec_list.input', ['data' => $items['specList.selector.list.list.text'], 'index' => 'specList.selector.list.list.text'])

        <!-- 列表默认 -->
        @include('spec_list.switch', ['data' => $items['specList.selector.list.list.default'], 'index' => 'specList.selector.list.list.default'])

        <!-- 模式选择器组件的配置信息 -->
        @include('spec_list.textarea', ['data' => $items['specList.selector.componentConfig'], 'index' => 'specList.selector.componentConfig'])

        <p class="split-line"></p>

        <!-- class -->
        @include('spec_list.input', ['data' => $items['specList.pagination.class'], 'index' => 'specList.pagination.class'])

        <!-- 每页数量 -->
        @include('spec_list.input', ['data' => $items['specList.pagination.pageSize'], 'index' => 'specList.pagination.pageSize'])

        <!-- 默认页 -->
        @include('spec_list.input', ['data' => $items['specList.pagination.currentPage'], 'index' => 'specList.pagination.currentPage'])

        <!-- 分页组件的配置信息 -->
        @include('spec_list.textarea', ['data' => $items['specList.pagination.componentConfig'], 'index' => 'specList.pagination.componentConfig'])

        <p class="split-line"></p>

        <!-- 加载功能 -->
        @include('spec_list.switch', ['data' => $items['specList.loading.status'], 'index' => 'specList.loading.status'])

        <template v-if="settings['specList.loading.status']">
            <!-- 加载组件的配置信息 -->
            @include('spec_list.textarea', ['data' => $items['specList.loading.componentConfig'], 'index' => 'specList.loading.componentConfig', 'class' => 'indent-1'])
        </template>

        <p class="split-line"></p>

        <!-- 查看全部规格时使用的配置信息 -->
        @include('spec_list.select', ['data' => $items['specList.specAll.specConfig'], 'index' => 'specList.specAll.specConfig', 'list' => specs_name()])

        <!-- 查看全部规格时‘规格’信息的配置 -->
        @include('spec_list.switch', ['data' => $items['specList.specAll.status'], 'index' => 'specList.specAll.status'])

        <template v-if="settings['specList.specAll.status']">
            <!-- 标题 -->
            @include('spec_list.input', ['data' => $items['specList.specAll.title'], 'index' => 'specList.specAll.title', 'class' => 'indent-1'])

            <!-- 位置 -->
            @include('spec_list.input', ['data' => $items['specList.specAll.order'], 'index' => 'specList.specAll.order', 'class' => 'indent-1'])

            <!-- 可排序 -->
            @include('spec_list.switch', ['data' => $items['specList.specAll.sortable'], 'index' => 'specList.specAll.sortable', 'class' => 'indent-1'])

            <!-- 可搜索 -->
            @include('spec_list.switch', ['data' => $items['specList.specAll.searchable'], 'index' => 'specList.specAll.searchable', 'class' => 'indent-1'])

            <!-- 可筛选 -->
            @include('spec_list.switch', ['data' => $items['specList.specAll.screenable'], 'index' => 'specList.specAll.screenable', 'class' => 'indent-1'])

            <template v-if="settings['specList.specAll.screenable']">
                <!-- 筛选类型 -->
                @include('spec_list.radio', [
                    'data' => $items['specList.specAll.screenType'],
                    'index' => 'specList.specAll.screenType',
                    'class' => 'indent-2',
                    'list' => [
                        ['text' => '单选', 'label' => 1],
                        ['text' => '多选', 'label' => 2],
                        ['text' => '滑块', 'label' => 3],
                        ['text' => '时间', 'label' => 4],
                        ['text' => '下拉菜单', 'label' => 5],
                    ]
                ])

                <!-- 筛选组位置 -->
                @include('spec_list.input', ['data' => $items['specList.specAll.screenOrder'], 'index' => 'specList.specAll.screenOrder', 'class' => 'indent-2'])

                <!-- 筛选默认值 -->
                @include('spec_list.input', ['data' => $items['specList.specAll.screenDefault'], 'index' => 'specList.specAll.screenDefault', 'class' => 'indent-2'])

                <!-- 筛选默认值 -->
                @include('spec_list.input', ['data' => $items['specList.specAll.screenItemOrder'], 'index' => 'specList.specAll.screenItemOrder', 'class' => 'indent-2'])

                <!-- 组件配置 -->
                @include('spec_list.input', ['data' => $items['specList.specAll.screenConfig'], 'index' => 'specList.specAll.screenConfig', 'class' => 'indent-2'])

                <!-- 组件group配置 -->
                <template v-if="settings['specList.specAll.screenType'] == 1 || settings['specList.specAll.screenType'] == 2">
                    @include('spec_list.input', ['data' => $items['specList.specAll.screenGroupConfig'], 'index' => 'specList.specAll.screenGroupConfig', 'class' => 'indent-2'])
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
