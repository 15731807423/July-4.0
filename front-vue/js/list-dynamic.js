const dataList = {
    template: `
        <div :class="className">
            <div v-if="searchInside.status" :class="searchInside.class">
                <el-input
                    v-if="searchInside.status"
                    v-model="keywords"
                    :class="searchInside.inputConfig.class"
                    :type="searchInside.inputConfig.type"
                    :maxlength="searchInside.inputConfig.maxlength"
                    :minlength="searchInside.inputConfig.minlength"
                    :show-word-limit="searchInside.inputConfig.showWordLimit"
                    :placeholder="searchInside.inputConfig.placeholder"
                    :clearable="searchInside.inputConfig.clearable"
                    :show-password="searchInside.inputConfig.showPassword"
                    :disabled="searchInside.inputConfig.disabled"
                    :size="searchInside.inputConfig.size"
                    :prefix-icon="searchInside.inputConfig.prefixIcon"
                    :suffix-icon="searchInside.inputConfig.suffixIcon"
                    :rows="searchInside.inputConfig.rows"
                    :autosize="searchInside.inputConfig.autosize"
                    :autocomplete="searchInside.inputConfig.autocomplete"
                    :name="searchInside.inputConfig.name"
                    :readonly="searchInside.inputConfig.readonly"
                    :max="searchInside.inputConfig.max"
                    :min="searchInside.inputConfig.min"
                    :step="searchInside.inputConfig.step"
                    :resize="searchInside.inputConfig.resize"
                    :autofocus="searchInside.inputConfig.autofocus"
                    :form="searchInside.inputConfig.form"
                    :label="searchInside.inputConfig.label"
                    :tabindex="searchInside.inputConfig.tabindex"
                    :validate-event="searchInside.inputConfig.validateEvent"
                    :input-style="searchInside.inputConfig.inputStyle"
                    @input="handleData('searchInput')"
                    @change="handleData('searchChange')"
                ></el-input>
                <el-button
                    v-if="searchInside.status && searchInside.buttonConfig.status"
                    :class="searchInside.buttonConfig.class"
                    :size="searchInside.buttonConfig.size"
                    :type="searchInside.buttonConfig.type"
                    :plain="searchInside.buttonConfig.plain"
                    :round="searchInside.buttonConfig.round"
                    :circle="searchInside.buttonConfig.circle"
                    :loading="searchInside.buttonConfig.loading"
                    :loading-icon="searchInside.buttonConfig.loadingIcon"
                    :disabled="searchInside.buttonConfig.disabled"
                    :icon="searchInside.buttonConfig.icon"
                    :autofocus="searchInside.buttonConfig.autofocus"
                    :native-type="searchInside.buttonConfig.nativeType"
                    :auto-insert-space="searchInside.buttonConfig.autoInsertSpace"
                    @click="handleData"
                >{{ searchInside.buttonConfig.text }}</el-button>
            </div>

            <div :class="contentClass">
                <div v-if="screenInside.status" :class="screenInside.class">
                    <div v-if="screenInside.status && screenInside.userStatus && userSelectedList.length > 0" :class="screenInside.selectedClass">
                        <span v-for="item in userSelectedList" @click="screenClear(item.field, item.value)">{{ item.value }}</span>
                        <span @click="screenClear()">{{ screenInside.clearText }}</span>
                    </div>

                    <div v-if="screenInside.status" :class="screenInside.allClass">
                        <div v-for="(item, key) in screenAll">
                            <span>{{ item.name }}</span>

                            <el-radio-group
                                v-if="item.type == 1"
                                v-model="screenAllSelected[key].value"
                                :size="item.configGroup.size"
                                :disabled="item.configGroup.disabled"
                                :text-color="item.configGroup.textColor"
                                :fill="item.configGroup.fill"
                                @change="screenChange(key)"
                            >
                                <template v-for="it in item.list">
                                    <el-radio-button
                                        v-if="(it.count > 0 || !screenInside.nullHidden) && item.config.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                    >{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(1) != -1 ? '(' + it.count + ')' : '' }}</el-radio-button>
                                    <el-radio
                                        v-if="(it.count > 0 || !screenInside.nullHidden) && !item.config.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                    >{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(1) != -1 ? '(' + it.count + ')' : '' }}</el-radio>
                                </template>
                            </el-radio-group>

                            <el-checkbox-group
                                v-if="item.type == 2"
                                v-model="screenAllSelected[key].value"
                                :size="item.configGroup.size"
                                :disabled="item.configGroup.disabled"
                                :min="item.configGroup.min"
                                :max="item.configGroup.max"
                                :text-color="item.configGroup.textColor"
                                :fill="item.configGroup.fill"
                                @change="screenChange(key)"
                            >
                                <template v-for="it in item.list">
                                    <el-checkbox-button
                                        v-if="(it.count > 0 || !screenInside.nullHidden) && item.config.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                        :indeterminate="item.config.indeterminate"
                                    >{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(2) != -1 ? '(' + it.count + ')' : '' }}</el-checkbox-button>
                                    <el-checkbox
                                        v-if="(it.count > 0 || !screenInside.nullHidden) && !item.config.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                        :indeterminate="item.config.indeterminate"
                                    >{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(2) != -1 ? '(' + it.count + ')' : '' }}</el-checkbox>
                                </template>
                            </el-checkbox-group>

                            <el-slider
                                v-if="item.type == 3"
                                v-model="screenAllSelected[key].value"
                                :min="item.config.min"
                                :max="item.config.max"
                                :disabled="item.config.disabled"
                                :step="item.config.step"
                                :show-input="item.config.showInput"
                                :show-input-controls="item.config.showInputControls"
                                :size="item.config.size"
                                :input-size="item.config.inputSize"
                                :show-stops="item.config.showStops"
                                :show-tooltip="item.config.showTooltip"
                                :format-tooltip="item.config.formatTooltip"
                                :range="item.config.range"
                                :vertical="item.config.vertical"
                                :height="item.config.height"
                                :label="item.config.label"
                                :debounce="item.config.debounce"
                                :tooltip-class="item.config.tooltipClass"
                                :marks="item.config.marks"
                                @change="screenChange(key)"
                            ></el-slider>

                            <el-date-picker
                                v-if="item.type == 4"
                                v-model="screenAllSelected[key].value"
                                value-format="X"
                                :type="item.config.type"
                                :disabled="item.config.disabled"
                                :editable="item.config.editable"
                                :clearable="item.config.clearable"
                                :size="item.config.size"
                                :placeholder="item.config.placeholder"
                                :start-placeholder="item.config.startPlaceholder"
                                :end-placeholder="item.config.endPlaceholder"
                                :time-arrow-control="item.config.timeArrowControl"
                                :format="item.config.format"
                                :popper-class="item.config.popperClass"
                                :range-separator="item.config.rangeSeparator"
                                :default-value="item.config.defaultValue"
                                :default-time="item.config.defaultTime"
                                :id="item.config.id"
                                :name="item.config.name"
                                :unlink-panels="item.config.unlinkPanels"
                                :prefix-icon="item.config.prefixIcon"
                                :clear-icon="item.config.clearIcon"
                                :shortcuts="item.config.shortcuts"
                                :disabledDate="item.config.disabledDate"
                                :cellClassName="item.config.cellClassName"
                                :teleported="item.config.teleported"
                                @change="screenChange(key)"
                            ></el-date-picker>

                            <el-select
                                v-if="item.type == 5"
                                v-model="screenAllSelected[key].value"
                                class="m-2"
                                :multiple="item.config.multiple"
                                :disabled="item.config.disabled"
                                :value-key="item.config.valueKey"
                                :size="item.config.size"
                                :clearable="item.config.clearable"
                                :collapse-tags="item.config.collapseTags"
                                :collapse-tags-tooltip="item.config.collapseTagsTooltip"
                                :multiple-limit="item.config.multipleLimit"
                                :name="item.config.name"
                                :effect="item.config.effect"
                                :autocomplete="item.config.autocomplete"
                                :placeholder="item.config.placeholder"
                                :filterable="item.config.filterable"
                                :allow-create="item.config.allowCreate"
                                :filter-method="item.config.filterMethod"
                                :remote="item.config.remote"
                                :remote-method="item.config.remoteMethod"
                                :loading="item.config.loading"
                                :loading-text="item.config.loadingText"
                                :no-match-text="item.config.noMatchText"
                                :no-data-text="item.config.noDataText"
                                :popper-class="item.config.popperClass"
                                :reserve-keyword="item.config.reserveKeyword"
                                :default-first-option="item.config.defaultFirstOption"
                                :teleported="item.config.teleported"
                                :persistent="item.config.persistent"
                                :automatic-dropdown="item.config.automaticDropdown"
                                :clear-icon="item.config.clearIcon"
                                :fit-input-width="item.config.fitInputWidth"
                                :suffix-icon="item.config.suffixIcon"
                                :tag-type="item.config.tagType"
                                @change="screenChange(key)"
                            >
                                <el-option v-for="item in screenAll[key].list" :key="item.name" :label="item.name" :value="item.name">{{ item.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(5) != -1 ? '(' + item.count + ')' : '' }}</el-option>
                            </el-select>
                        </div>
                    </div>
                </div>

                <div :class="dataClass">
                    <div v-if="tableInside.status && listItem.length > 0" :class="selectorInside.class">
                        <el-radio-group
                            v-if="selectorInside"
                            v-model="selectorInside.value"
                            :size="selectorInside.config.size"
                            :disabled="selectorInside.config.disabled"
                            :text-color="selectorInside.config.textColor"
                            :fill="selectorInside.config.fill"
                        >
                            <el-radio-button
                                v-for="(item, key) in selectorInside.list"
                                :label="key"
                                :disabled="item.disabled"
                                :border="item.border"
                                :size="item.size"
                                :name="item.name"
                            >{{ item.text }}</el-radio-button>
                        </el-radio-group>
                    </div>

                    <div :class="listClass">
                        <el-table
                            v-if="tableInside.status && (!selectorInside || selectorInside.value == 'table')"
                            :data="list"
                            :height="tableInside.config.height"
                            :max-height="tableInside.config.maxHeight"
                            :stripe="tableInside.config.stripe"
                            :border="tableInside.config.border"
                            :size="tableInside.config.size"
                            :fit="tableInside.config.fit"
                            :show-header="tableInside.config.showHeader"
                            :highlight-current-row="tableInside.config.highlightCurrentRow"
                            :current-row-key="tableInside.config.currentRowKey"
                            :row-class-name="tableInside.config.rowClassName"
                            :row-style="tableInside.config.rowStyle"
                            :cell-class-name="tableInside.config.cellClassName"
                            :cell-style="tableInside.config.cellStyle"
                            :header-row-class-name="tableInside.config.headerRowClassName"
                            :header-row-style="tableInside.config.headerRowStyle"
                            :header-cell-class-name="tableInside.config.headerCellClassName"
                            :header-cell-style="tableInside.config.headerCellStyle"
                            :row-key="tableInside.config.rowKey"
                            :empty-text="tableInside.config.emptyText"
                            :default-expand-all="tableInside.config.defaultExpandAll"
                            :expand-row-keys="tableInside.config.expandRowKeys"
                            :tooltip-effect="tableInside.config.tooltipEffect"
                            :show-summary="tableInside.config.showSummary"
                            :sum-text="tableInside.config.sumText"
                            :summary-method="tableInside.config.summaryMethod"
                            :span-method="tableInside.config.spanMethod"
                            :select-on-indeterminate="tableInside.config.selectOnIndeterminate"
                            :indent="tableInside.config.indent"
                            :lazy="tableInside.config.lazy"
                            :load="tableInside.config.load"
                            :tree-props="tableInside.config.treeProps"
                            :table-layout="tableInside.config.tableLayout"
                            :scrollbar-always-on="tableInside.config.scrollbarAlwaysOn"
                            @sort-change="sortChange"
                            style="width: 100%"
                        >
                            <el-table-column
                                v-for="item in tableInside.column"
                                :prop="item.field"
                                :label="item.title || item.field"
                                :sortable="item.sortable === true ? 'custom' : false"
                                :formatter="item.formatter"
                                :type="item.type"
                                :index="item.index"
                                :column-key="item.columnKey"
                                :width="item.width"
                                :min-width="item.minWidth"
                                :fixed="item.fixed"
                                :render-header="item.renderHeader"
                                :sort-method="item.sortMethod"
                                :sort-by="item.sortBy"
                                :sort-orders="item.sortOrders"
                                :resizable="item.resizable"
                                :show-overflow-tooltip="item.showOverflowTooltip"
                                :align="item.align"
                                :header-align="item.headerAlign"
                                :class-name="item.className"
                                :label-class-name="item.labelClassName"
                                :selectable="item.selectable"
                                :reserve-selection="item.reserveSelection"
                                :filters="item.filters"
                                :filter-placement="item.filterPlacement"
                                :filter-multiple="item.filterMultiple"
                                :filter-method="item.filterMethod"
                                :filtered-value="item.filteredValue"
                            ></el-table-column>
                        </el-table>

                        <template v-if="listItem.length > 0 && (!selectorInside || selectorInside.value == 'list')">
                            <p class="empty" v-if="list.length == 0">{{ dataEmptyText }}</p>

                            <div v-else v-for="item in list" :class="listItemClass" v-html="handleHtml(item)"></div>
                        </template>
                    </div>

                    <div :class="paginationInside.class">
                        <el-pagination
                            v-model:current-page="paginationInside.currentPage"
                            v-model:page-size="paginationInside.pageSize"
                            :small="paginationInside.small"
                            :background="paginationInside.background"
                            :default-page-size="paginationInside.defaultPageSize"
                            :total="paginationInside.total"
                            :page-count="paginationInside.pageCount"
                            :pager-count="paginationInside.pagerCount"
                            :default-current-page="paginationInside.defaultCurrentPage"
                            :layout="paginationInside.layout"
                            :page-sizes="paginationInside.pageSizes"
                            :popper-class="paginationInside.popperClass"
                            :prev-text="paginationInside.prevText"
                            :next-text="paginationInside.nextText"
                            :disabled="paginationInside.disabled"
                            :hide-on-single-page="paginationInside.hideOnSinglePage"
                        />
                    </div>
                </div>
            </div>
        </div>
    `,

    props: {
        // 规格的名称
        name: { type: Object, required: true },

        // 搜索的配置信息 默认searchInside
        search: { type: Object, default: {} },

        // 筛选的配置信息 默认screenInside
        screen: { type: Object, default: {} },

        // 模式选择器的配置信息 默认selectorInside
        selector: { type: Object, default: {} },

        // 表格的配置信息
        table: { type: Object, default: {} },

        // 列表的布局html
        listItem: { type: String, default: '' },

        // 分页的配置信息
        pagination: { type: Object, default: {} },

        // 加载的配置信息
        loading: { type: Object, default: {} },

        // 数据里多个数据放在一个属性里时的切割符号 空字符串表示不存在多个数据 暂不支持多个切割符号
        cuttingSymbol: { type: String, default: '' },

        // 数据为空时的提示
        dataEmptyText: { type: String, default: 'No Data' },

        // 排序大小写敏感
        sortCaseSensitive: { type: Boolean, default: false },

        // 一些元素的class
        className: { type: String, default: 'data' },
        contentClass: { type: String, default: 'data-content' },
        dataClass: { type: String, default: 'data-data' },
        listClass: { type: String, default: 'data-list' },
        listItemClass: { type: String, default: 'data-list-item' }
    },

    data: function() {
        return {
            // 搜索和筛选后的数据的集合
            list: [],

            // 搜索的关键词
            keywords: this.search.default ? this.search.default : '',

            // screenInside.type = 3 时储存点击顺序的数组
            screenSort: [],

            // 当前排序方式
            currentSort: {},

            // 默认排序方式
            defaultSort: {},

            // 全部筛选组
            screenAll: {},

            // 当前的筛选情况
            screenAllSelected: {},

            // 取消axios
            cancel: null,

            // 搜索的默认配置信息
            searchInside: {
                // 搜索功能的状态
                status: true,

                // 默认值 在这里没用 传参时赋值给this.keywords
                default: '',

                // 大小写敏感
                caseSensitive: true,

                // 类名
                class: 'data-search',

                // 允许搜索的字段 传一维数组 *会替换为全部字段
                field: '*',

                // 搜索框的默认配置信息
                inputConfig: {
                    // 搜索框的input事件触发搜索
                    onInput: false,

                    // 搜索框的change事件触发搜索
                    onChange: true,

                    // 搜索框的类名
                    class: 'data-search-input'

                    // ... 组件配置
                },

                // 搜索按钮的默认配置信息
                buttonConfig: {
                    // 搜索按钮的状态
                    status: true,

                    // 搜索按钮的文本
                    text: 'search',

                    // 搜索按钮的类名
                    class: 'data-search-button'

                    // ... 组件配置
                }
            },

            // 筛选组的默认配置信息
            screenItem: {
                // 筛选组的名字
                name: '',

                // 筛选的字段
                field: '',

                // 筛选组之间关联的类型 上面有注释
                type: 1,

                // 默认值 上面有注释 注意格式
                default: null,

                // 组件配置
                config: {
                    button: false
                },

                // 组件配置 单选是把radio包裹在radio-group里 此时把group的配置放在这里 radio的配置放在config里 只有一个组件的时候传config即可
                configGroup: {

                }
            },

            // 筛选的默认配置信息
            screenInside: {
                // 是否启用筛选功能
                status: true,

                // 是否启用显示已筛选项
                userStatus: true,

                // 清空全部筛选的文本
                clearText: 'reset',

                // 是否启用显示筛选项后面的数值
                countStatus: false,

                // 允许计算数值的筛选组类型 默认为单选、多选和下拉菜单
                groupCountType: [1, 2, 5],

                // 筛选组之间的关联方式
                // 1 根据screenAll的内容排序 逐级计算数量 0.5
                // 2 根据screenAll的内容排序 逐级计算数量 且点击高级的筛选组时会重置低级的筛选组 0.5
                // 3 根据点击筛选组的顺序排序 逐级计算数量 0.4
                // 4 根据其他筛选组筛选的结果计算当前筛选组每个筛选项的数量 1
                type: 1,

                // 隐藏对应数据数量为0的选项
                nullHidden: false,

                // class
                class: 'data-screen',

                // 已筛选项的class
                selectedClass: 'data-screen-selected',

                // 全部筛选项的class
                allClass: 'data-screen-all',

                // 全部筛选组
                list: []
            },

            // 模式选择器的默认配置信息
            selectorInside: {
                value: '',

                class: 'data-selector',

                // 模式列表
                list: {
                    // 表格模式配置
                    table: {
                        // 按钮文本
                        text: 'table'

                        // config
                    },
                    // 列表模式配置
                    list: {
                        // 按钮文本
                        text: 'list'

                        // config
                    }
                },

                // 组件配置
                config: {

                }
            },

            // 表格的默认配置信息
            tableInside: {
                // 开启表格模式
                status: true,

                // 每一列的信息   field 字段  title 标题 sortable 排序传true
                column: [],

                // 组件配置
                config: {
                    emptyText: this.dataEmptyText
                }
            },

            // 表格列的默认配置信息
            tableColumn: {
                // 字段
                field: '',

                // 标题
                title: '',

                // 排序
                sortable: false,

                // 该列为默认排序列 只允许一列设置为true 多列设置为true时取设置为true的第一列 没有列设置为true时取允许排序列的第一列 不排序的列设置true无效
                sortableDefaultField: false,

                // 默认的排序方式 默认为正序 asc为正序 desc为倒序 其他非法值为正序
                sortableDefaultMode: 'asc',

                // config
            },

            // 分页的默认配置信息
            paginationInside: {
                class: 'data-page',
                pageSize: 10,
                currentPage: 1,
                total: 0
            },

            // 加载的默认配置信息
            loadingInside: {
                // 加载功能的状态
                status: true,

                // 加载的默认配置信息
                config: {

                }
            }
        };
    },

    mounted: function() {
        this.init();
    },

    watch: {
        'paginationInside.currentPage': {
            handler (newVal, oldVal) {
                this.handleData();
            },
            deep: true
        },
        'paginationInside.pageSize': {
            handler (newVal, oldVal) {
                this.handleData();
            },
            deep: true
        }
    },

    computed: {
        // 全部已筛选项
        userSelectedList() {
            var list = [];

            for (let key in this.screenAllSelected) {
                let item = this.screenAllSelected[key];

                if (item.type == 1 && this.screenEffect(key, item.value)) {
                    list.push({ field: key, value: item.value });
                }

                if (item.type == 2 && this.screenEffect(key, item.value)) {
                    for (var i = 0; i < item.value.length; i++) {
                        list.push({ field: key, value: item.value[i] });
                    }
                }

                if (item.type == 3 && this.screenEffect(key, item.value)) {
                    if (item.value instanceof Array) {
                        list.push({ field: key, value: item.value[0] + ' - ' + item.value[1] });
                    }

                    if (typeof item.value == 'string') {
                        list.push({ field: key, value: item.value });
                    }
                }

                if (item.type == 4 && this.screenEffect(key, item.value)) {
                    if (item.config.type == 'date' || item.config.type == 'datetime') {
                        list.push({ field: key, value: this.date(item.value, true) });
                    } else if (item.config.type == 'datetimerange') {
                        let range = this.dateRange(item.value, item.config.type);
                        list.push({ field: key, value: this.date(range[0], true) + ' - ' + this.date(range[1], true) });
                    } else if (item.config.type == 'dates') {
                        for (var i = 0; i < item.value.length; i++) {
                            list.push({ field: key, value: this.date(item.value[i], false) });
                        }
                    } else {
                        let range = this.dateRange(item.value, item.config.type);
                        list.push({ field: key, value: this.date(range[0], false) + ' - ' + this.date(range[1], false) });
                    }
                }

                if (item.type == 5 && this.screenEffect(key, item.value)) {
                    if (item.value instanceof Array) {
                        for (var i = 0; i < item.value.length; i++) {
                            list.push({ field: key, value: item.value[i] });
                        }
                    }

                    if (typeof item.value == 'string') {
                        list.push({ field: key, value: item.value });
                    }
                }
            }

            return list;
        }
    },

    methods: {
        // 初始化组件
        init() {
            // 处理组件配置
            this.handleComponentConfig();

            // 处理传进来的默认排序
            this.handleDefaultSort();

            // 注册设置了分组的筛选
            this.group();

            // 处理数据
            this.handleData();
        },

        // 处理组件配置
        handleComponentConfig() {
            // 处理搜索配置
            this.searchInside = this.configTemplateRecursion(this.search, this.searchInside);

            // 处理筛选配置
            this.screenInside = this.configTemplateRecursion(this.screen, this.screenInside);
            this.screenInside.groupCountType = this.screen.groupCountType.length > 0 ? this.screen.groupCountType : this.screenInside.groupCountType;
            for (var i = 0; i < this.screen.list.length; i++) {
                let data = this.configTemplateRecursion(this.screen.list[i], this.assign(this.screenItem));
                if (data.type == 4 && data.config.defaultTime) {
                    data.config.defaultTime[0] = new Date(data.config.defaultTime[0] * 1000);
                    data.config.defaultTime[1] = new Date(data.config.defaultTime[1] * 1000);
                }
                this.screenInside.list.push(data);
            }

            // 处理表格配置
            this.tableInside = this.configTemplateRecursion(this.table, this.tableInside);

            if (this.table.column instanceof Array) {
                for (var i = 0; i < this.table.column.length; i++) {
                    this.tableInside.column.push(this.configTemplate(this.table.column[i], this.assign(this.tableColumn), true));
                }
            }

            this.tableInside.config = this.configTemplate(this.table.config, this.tableInside.config, true);

            // 处理选择器配置
            if (!this.tableInside.status || this.listItem.length == 0) {
                this.selectorInside = false;
            } else {
                this.selectorInside.config = this.configTemplate(this.selector.config, this.selectorInside.config, true);

                for (let key in this.selectorInside.list) {
                    this.selectorInside.list[key] = this.configTemplate(this.selector.list ? this.selector.list[key] : {}, this.selectorInside.list[key], true);
                }

                if (this.selectorInside.value.length == 0) {
                    for (let key in this.selectorInside.list) {
                        if (this.selectorInside.list[key].default === true) {
                            this.selectorInside.value = key;
                            break;
                        }
                    }
                }

                if (this.selectorInside.value.length == 0) {
                    this.selectorInside.value = Object.keys(this.selectorInside.list)[0];
                }
            }

            // 处理分页组件配置
            this.paginationInside = this.configTemplate(this.pagination, this.paginationInside, true);

            // 处理加载组件配置
            this.loadingInside = this.configTemplateRecursion(this.loading, this.loadingInside);
            this.loadingInside.config = this.configTemplate(this.loading.config, this.loadingInside.config, true);
        },

        // 处理默认排序
        handleDefaultSort() {
            if (!this.tableInside.status) return false;

            for (var i = 0; i < this.tableInside.column.length; i++) {
                // 默认排序的列必须开启排序
                // if (this.tableInside.column[i].sortable === true && this.tableInside.column[i].sortableDefaultField === true) {}

                // 默认排序的列可以不开启排序
                if (this.tableInside.column[i].sortableDefaultField === true) {
                    this.currentSort.prop = this.tableInside.column[i].field;
                    this.currentSort.order = this.tableInside.column[i].sortableDefaultMode === 'desc' ? 'descending' : 'ascending';
                    break;
                }
            }

            if (!this.currentSort.prop || !this.currentSort.order) {
                for (var i = 0; i < this.tableInside.column.length; i++) {
                    if (this.tableInside.column[i].sortable === true) {
                        this.currentSort.prop = this.tableInside.column[i].field;
                        this.currentSort.order = 'ascending';
                        break;
                    }
                }
            }

            if (!this.currentSort.prop || !this.currentSort.order) {
                this.currentSort.prop = this.tableInside.column[0].field;
                this.currentSort.order = 'ascending';
            }

            this.currentSort.order = this.currentSort.order.replace('ending', '');

            this.defaultSort = this.currentSort;
        },

        // 获取可分组的字段的选项
        group() {
            for (var i = 0; i < this.screenInside.list.length; i++) {
                if (this.screenInside.list[i].type == 1) {
                    this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : 'All', 'All');
                }
                if (this.screenInside.list[i].type == 2) {
                    this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : this.assign([]), []);
                }
                if (this.screenInside.list[i].type == 3) {
                    if (this.screenInside.list[i].config.range === true) {
                        let max = this.screenInside.list[i].config.max === undefined ? 100 : this.screenInside.list[i].config.max,
                            min = this.screenInside.list[i].config.min === undefined ? 0 : this.screenInside.list[i].config.min;
                        this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : [min, max], [min, max]);
                    } else {
                        this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : 0, 0);
                    }
                }
                if (this.screenInside.list[i].type == 4) {
                    this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default, null);
                }
                if (this.screenInside.list[i].type == 5) {
                    this.screenRegister(this.screenInside.list[i], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : '', '');
                }
            }

            if (this.screenInside.type == 3) {
                for (let key in this.screenAll) {
                    if (this.screenEffect(key, this.screenAllSelected[key].value)) this.screenChange(key);
                }
            }
        },

        // 注册筛选项
        screenRegister(data, defaultValue, all) {
            var config = data.config, configGroup = data.configGroup;

            if (data.type == 1 || data.type == 2) {
                this.screenAllSelected[data.field] = { type: data.type, value: defaultValue, config: config };
                this.screenAll[data.field] = { name: data.name, label: data.field, list: [], type: data.type, default: defaultValue, config: config, configGroup: configGroup, all: all };
                return false;
            }

            if (data.type == 4) {
                config.type = config.type || 'datetimerange';
                if (config.type == 'dates' || config.type == 'datetimerange' || config.type == 'daterange') {
                    all = [];
                }
            }

            if (data.type == 5) {
                if (config.multiple === true) {
                    all = [];
                } else if (config.clearable === true) {
                    all = '';
                } else {
                    all = 'All';
                    defaultValue = defaultValue.length == 0 ? 'All' : defaultValue;
                }
            }

            this.screenAllSelected[data.field] = { type: data.type, value: defaultValue, config: config };
            this.screenAll[data.field] = { name: data.name, label: data.field, list: [], type: data.type, default: defaultValue, config: config, all: all };
        },

        // 处理数据
        handleData(source = '') {
            if (source === 'searchInput' && this.searchInside.inputConfig.onInput !== true) return false;
            if (source === 'searchChange' && this.searchInside.inputConfig.onChange !== true) return false;

            var data = {
                search: this.keywords,
                screen: {},
                sort: this.currentSort,
                page: [this.paginationInside.currentPage, this.paginationInside.pageSize]
            };

            if (this.screenInside.type == 3 && this.screenSort.length > 0) {
                data.screenSort = this.screenSort;
            }

            for (let key in this.screenAllSelected) {
                data.screen[key] = this.screenAllSelected[key].value;
            }

            this.send(data);
        },

        // 发送请求
        send(data) {
            if (this.cancel) this.cancel();

            const loading = this.loadingInside.status ? ElementPlus.ElLoading.service(this.loadingInside.config) : null;

            axios.post('/specs/getlist/' + this.name, data, {
                cancelToken: new axios.CancelToken((c) => {
                    this.cancel = c;
                })
            }).then((data) => {
                if (data.status == 200 && data.data.status > 0) {
                    this.list = data.data.data.list;
                    this.paginationInside.total = data.data.data.count;

                    for (let key in data.data.data.screen) {
                        this.screenAll[key].list = data.data.data.screen[key];
                    }
                }
                if (loading) loading.close();
            }).catch((data) => {
                console.log(data)
                if (loading) loading.close();
            });
        },

        // 将传进来的html中的属性转换成对应的值
        handleHtml(data) {
            var html = this.listItem;
            for (let key in data) html = html.replaceAll('{ ' + key + ' }', data[key]);
            return html;
        },

        // 用户点击进行筛选
        screenChange(name) {
            var index = Object.keys(this.screenAll).indexOf(name);

            // 点击筛选时重置下级组
            if (this.screenInside.type == 2) {
                var i = -2;
                for (let key in this.screenAllSelected) {
                    i++;
                    if (i < index) continue;
                    this.screenAllSelected[key] = this.screenReset(key);
                }
            }

            // 把点击的顺序存进数组
            if (this.screenInside.type == 3) {
                if (this.screenSort.indexOf(name) === -1) {
                    this.screenSort.push(name);
                } else {
                    this.screenSort.splice(this.screenSort.indexOf(name), 1);
                    this.screenSort.push(name);
                }
            }

            this.handleData();
        },

        // 取消筛选
        screenClear(name = null, value = null) {
            if (name === null && value === null) {
                for (let key in this.screenAllSelected) this.screenAllSelected[key] = this.screenReset(key);

                this.screenSort = [];
            } else {
                if (this.screenAllSelected[name].type == 2 || (this.screenAllSelected[name].type == 4 && this.screenAllSelected[name].config.type == 'dates') || (this.screenAllSelected[name].type == 5 && this.screenAllSelected[name].value instanceof Array)) {
                    this.screenAllSelected[name].value.splice(this.screenAllSelected[name].value.indexOf(value), 1);
                } else {
                    this.screenAllSelected[name] = this.screenReset(name);
                }

                if (this.screenSort.length > 0 && this.screenSort.indexOf(name) !== -1) {
                    this.screenSort.splice(this.screenSort.indexOf(name), 1);
                }
            }

            this.handleData();
        },

        // 判断一个筛选项的结果有没有生效
        screenEffect(name, data) {
            return JSON.stringify(this.screenAll[name].all) != JSON.stringify(data) && data !== null;
        },

        // 根据筛选结果的类型返回重置结果
        screenReset(name) {
            return { type: this.screenAll[name].type, value: this.screenAll[name].all, config: this.screenAll[name].config };
        },

        // 排序时对整个筛选后的数组排序
        sortChange(data) {
            if (data.order === null) data = this.defaultSort;

            data.order = data.order.replace('ending', '');

            this.currentSort = data;

            this.handleData();
        },

        // 日期格式化
        date(time, detailed) {
            if (detailed) {
                return new Date(parseInt(time) * 1000).toLocaleString().replaceAll('/', '-');
            } else {
                return new Date(parseInt(time) * 1000).toLocaleString().replaceAll('/', '-').split(' ')[0];
            }
        },

        // 获取时间戳对应时间类型的时间戳范围
        dateRange(data, type) {
            if (data instanceof Array && type != 'dates' && type != 'monthrange') {
                data[0] = parseInt(data[0]);
                data[1] = parseInt(data[1]);
                return data;
            }

            if (type == 'dates') {
                for (var i = 0; i < data.length; i++) {
                    data[i] = parseInt(data[i]) * 1000;
                    let date    = new Date(data[i]),
                        year    = date.getFullYear(),
                        month   = date.getMonth(),
                        day     = date.getDate(),
                        start   = new Date(year, month, day).getTime() / 1000,
                        end     = new Date(year, month, day + 1).getTime() / 1000 - 1;
                    data[i] = [start, end];
                }
                return data;
            }

            if (type == 'monthrange') {
                data[0] = parseInt(data[0]) * 1000;
                data[1] = parseInt(data[1]) * 1000;

                var date    = new Date(data[0]),
                    year    = date.getFullYear(),
                    month   = date.getMonth();

                data[0] = new Date(year, month, 1).getTime() / 1000;

                var date    = new Date(data[1]),
                    year    = date.getFullYear(),
                    month   = date.getMonth();

                data[1] = new Date(year, month + 1, 1).getTime() / 1000 - 1;
                return data;
            }

            data = parseInt(data) * 1000;

            var date = new Date(data);

            if (type == 'year') {
                var year    = date.getFullYear(),
                    start   = new Date(year, 0, 1).getTime() / 1000,
                    end     = new Date(year + 1, 0, 1).getTime() / 1000 - 1;
                return [start, end];
            }

            if (type == 'month') {
                var year    = date.getFullYear(),
                    month   = date.getMonth(),
                    start   = new Date(year, month, 1).getTime() / 1000,
                    end     = new Date(year, month + 1, 1).getTime() / 1000 - 1;
                return [start, end];
            }

            if (type == 'date') {
                var year    = date.getFullYear(),
                    month   = date.getMonth(),
                    day     = date.getDate(),
                    start   = new Date(year, month, day).getTime() / 1000,
                    end     = new Date(year, month, day + 1).getTime() / 1000 - 1;
                return [start, end];
            }

            if (type == 'datetime') {
                return [data / 1000, data / 1000];
            }

            if (type == 'week') {
                return [data / 1000, data / 1000 + 86400 * 7 - 1];
            }
        },

        // 将一个二维数组的参数放在该参数的默认参数上
        configTemplateRecursion(config, configInside) {
            configInside = this.configTemplate(config, configInside, false);

            for (let key in configInside) {
                if (!(configInside[key] instanceof Object) || configInside[key] instanceof Array) continue;

                configInside[key] = this.configTemplate(config[key], configInside[key], true);
            }

            return configInside;
        },

        // 将一个一维数组的参数放在该参数的默认参数上
        configTemplate(data, tpl, extra) {
            tpl = tpl || {};
            data = data || {};
            if (extra) {
                for (let key in data) {
                    if (tpl[key] instanceof Array || tpl[key] instanceof Object) continue;
                    tpl[key] = data[key] === undefined || data[key] === null ? tpl[key] : data[key];
                }
            } else {
                for (let key in tpl) {
                    if (tpl[key] instanceof Array || tpl[key] instanceof Object) continue;
                    tpl[key] = data[key] === undefined || data[key] === null ? tpl[key] : data[key];
                }
            }
            return tpl;
        },

        // 获取变量的数据
        assign(a) {
            return JSON.parse(JSON.stringify(a));
        }
    }
};