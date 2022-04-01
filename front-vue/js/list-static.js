// 函数调用流程

// 页面加载
// sortChange(执行默认排序)
// group(获取可分组的字段的选项)->screenRegister(注册筛选项)
// handleData(处理数据)->search(搜索)->screen(筛选)->screenStatistics(计算数量)->sortChange(排序)

// 搜索
// handleData(处理数据)->search(搜索)->screen(筛选)->screenStatistics(计算数量)->sortChange(排序)

// 取消已选择的筛选项
// screenClear()->handleData(处理数据)->search(搜索)->screen(筛选)->screenStatistics(计算数量)->sortChange(排序)

// 清空已选择的筛选项
// screenClear()->handleData(处理数据)->search(搜索)->screen(筛选)->screenStatistics(计算数量)->sortChange(排序)

// 点击筛选
// screenChange()->handleData(处理数据)->search(搜索)->screen(筛选)->screenStatistics(计算数量)->sortChange(排序)

// 排序
// sortChange()

// 分页

// 默认排序和当前排序
// 如果tableHeader里某一列设置了sortableDefaultField为true 则用这个字段做默认排序字段
// 如果同时设置了sortableDefaultMode为asc或desc 则用设置的这个值做默认排序方式 如果没设置或设置了非法值则为asc
// 如果没有设置字段则用第一个能排序的字段正序做默认排序

// 函数的详细调用
// screen(筛选)->screenEffect(判断筛选项有没有生效，没有返回全部数据)->check(对每条数据进行判断符合不符合筛选项)
// screenStatistics(计算数量)->screenGroupCount(根据集合计算筛选组里每个筛选项对应的数据数量)->checkList(获取列表里某个属性符合某个值的集合)
// screenClear()->screenReset(重置筛选项，如果清空全部重置，如果删除一项则重置一项，不包括多选)

// 计算数量的详细过程 待优化
// 循环单选组和多选组 循环每个组里的选项 循环全部数据判断是否符合 符合的放到一个集合里计算总量

// 筛选类型
// 筛选项传参格式 ['name' => $value, 'field' => $value, 'type' => 1]
// 插件的配置详细信息见https://element-plus.gitee.io/zh-CN/
// 1 单选 选项从数据里获取 config属性配置‘Radio 属性’ configGroup属性配置‘Radio-group 属性’ 单选选项前面自动添加‘All’ 默认值‘All’ button为true设置为按钮 例：
// 单选的值是‘All’时表示没有筛选
// {
//     name: 'name',       筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 1,            筛选类型
//     default: 'apple',   默认值
//     config: {           Radio属性
//                         属性详见官网
//     },
//     configGroup: {      RadioGroup属性
//                         属性详见官网
//     }
// }
// 2 多选 选项从数据里获取 config属性配置‘Checkbox 属性’ configGroup属性配置‘Checkbox-group 属性’ 默认值‘[]’ button为true设置为按钮 例：
// 多选的值是‘[]’时表示没有筛选
// {
//     name: 'name',       筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 2,            筛选类型
//     default: ['apple'], 默认值
//     config: {           Checkbox属性
//                         属性详见官网
//     },
//     configGroup: {      CheckboxGroup属性
//                         属性详见官网
//     }
// }
// 3 滑块 最大值和最小值必传 如果是范围选择 默认值为‘[最大值, 最小值]’ 如果不是范围选择 默认值为‘0’ 例：
// {
//     name: 'age',        筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 3,            筛选类型
//     default: 5,         默认的默认值
//     default: [5, 10],   范围选择的默认值
//     config: {           Slider属性
//                         属性详见官网
//     }
// }
// 4 日期范围 根据组件配置传值 例：
// {
//     name: 'time',       筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 4,            筛选类型
//     default: '',        默认值
//     config: {           DateTimePicker属性
//                         属性详见官网
//     }
// }
// 5 下拉菜单 根据组件配置传值 如果是多选 默认值必须是一维数组 例：
// {
//     name: 'name',       筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 5,            筛选类型
//     default: 'apple',   单选的默认值
//     default: ['apple'], 多选的默认值
//     config: {           Select属性
//                         属性详见官网
//     }
// }

// 筛选组之间关联的类型
// 1 根据screenAll的内容排序 逐级计算数量 0.5
// 2 根据screenAll的内容排序 逐级计算数量 且点击高级的筛选组时会重置低级的筛选组 0.5
// 3 根据点击筛选组的顺序排序 逐级计算数量 0.4
// 4 根据其他筛选组筛选的结果计算当前筛选组每个筛选项的数量 1

// 默认值的选择 比如数组里五个对象 可以指定其中一个为当前对象 但是指定了多个 系统在找到一个之后就会终止进程 所以设置了多个的时候用设置了的第一个 如果没设置但是必须设置则用第一个
// element组件的参数默认为undefined 因为传undefined不会影响组件本身的默认值 传'', 0, null, [], false等都达不到这个效果

// 需要用到的element-plus
// 搜索框     ElInput
// 搜索按钮   ElButton
// 筛选单选   ElRadioGroup ElRadio ElRadioButton
// 筛选多选   ElCheckboxGroup ElCheckbox ElCheckboxButton
// 滑块       ElSlider
// 日期范围   ElDatePicker
// 下拉菜单   ElSelect ElOption
// 模式选择器 ElRadioGroup ElRadio ElRadioButton
// 表格       ElTable ElTableColumn
// 分页       ElPagination


const dataList = {
    template: `
        <div :class="className">
            <div v-if="searchInside.status" :class="searchInside.class">
                <el-input
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
                    @input="handleData(-1, 'searchInput')"
                    @change="handleData(-1, 'searchChange')"
                ></el-input>
                <el-button
                    v-if="searchInside.buttonConfig.status"
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
                    <div v-if="screenInside.userStatus" :class="screenInside.selectedClass">
                        <div>
                            <template v-for="(item, key) in screenAllSelected">
                                <span v-if="item.type == 1 && screenEffect(key, item.value)" @click="screenClear(key, item.value)">{{ item.value }}</span>

                                <span v-if="item.type == 2 && screenEffect(key, item.value)" v-for="(it, k) in item.value" @click="screenClear(key, it)">{{ it }}</span>

                                <template v-if="item.type == 3 && screenEffect(key, item.value)">
                                    <span v-if="item.value instanceof Array" @click="screenClear(key, it)">{{ item.value[0] }} - {{ item.value[1] }}</span>
                                    <span v-if="typeof item.value == 'string'" @click="screenClear(key, it)">{{ item.value }}</span>
                                </template>

                                <span v-if="item.type == 4 && screenEffect(key, item.value)">
                                    <template v-if="item.config.type == 'date' || item.config.type == 'datetime'">{{ date(item.value, true) }}</template>

                                    <template v-else-if="item.config.type == 'datetimerange'">{{ date(dateRange(item.value, item.config.type)[0], true) }} - {{ date(dateRange(item.value, item.config.type)[1], true) }}</template>

                                    <template v-else>{{ date(dateRange(item.value, item.config.type)[0], false) }} - {{ date(dateRange(item.value, item.config.type)[1], false) }}</template>

                                </span>

                                <template v-if="item.type == 5 && screenEffect(key, item.value)">
                                    <span v-if="item.value instanceof Array" v-for="it in item.value" @click="screenClear(key, it)">{{ it }}</span>
                                    <span v-if="typeof item.value == 'string'" @click="screenClear(key, item.value)">{{ item.value }}</span>
                                </template>
                            </template>
                            <span v-if="screenSelectedStatus" @click="screenClear()">{{ screenInside.clearText }}</span>
                        </div>
                    </div>

                    <div :class="screenInside.allClass">
                        <div v-for="(item, key) in screenAll">
                            <span>{{ item.label }}</span>

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
                    <div :class="selectorInside.class">
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
                            :data="currentList"
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
                            <p class="empty" v-if="currentList.length == 0">{{ dataEmptyText }}</p>

                            <div v-else v-for="item in currentList" :class="listItemClass" v-html="handleHtml(item)"></div>
                        </template>
                    </div>

                    <div :class="paginationInside.class">
                        <el-pagination
                            v-model:current-page="paginationInside.currentPage"
                            v-model:page-size="paginationInside.pageSize"
                            :small="paginationInside.small"
                            :background="paginationInside.background"
                            :default-page-size="paginationInside.defaultPageSize"
                            :total="paginationInside.total ? paginationInside.total : screenList.length"
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
        // 全部数据的集合 对象数组
        list: { type: Array, default: [] },

        // 搜索的配置信息 默认searchInside
        search: { type: Object, default: {} },

        // 筛选的配置信息 默认screenInside
        screen: { type: Object, default: {} },

        // 模式选择器的配置信息 默认selectorInside
        selector: { type: Object, default: {} },

        // 表格的配置信息
        table: { type: Object, default: {} },

        // 如果列表是列表 传一项的html
        listItem: { type: String, default: '' },

        // 分页的配置信息
        pagination: { type: Object, default: {} },

        // 加载的配置信息
        loading: { type: Object, default: {} },

        // 数据里多个数据放在一个属性里时的切割符号 空字符串表示不存在多个数据 暂不支持多个切割符号
        cuttingSymbol: { type: String, default: '' },

        // 数据为空时的提示
        dataEmptyText: { type: String, default: 'No Data' },

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
            screenList: this.list,

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

                // 全部筛选项
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
                    },
                    // 列表模式配置
                    list: {
                        // 按钮文本
                        text: 'list'
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
            },

            // 分页的默认配置信息
            paginationInside: {
                class: 'data-page',
                pageSize: 10,
                currentPage: 1,
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

    created: function() {
        this.init();
    },

    computed: {
        // 获取当前页数据
        currentList() {
            return this.assign(this.screenList.slice((this.paginationInside.currentPage - 1) * this.paginationInside.pageSize, this.paginationInside.currentPage * this.paginationInside.pageSize));
        },

        // 判断当前是否进行了筛选
        screenSelectedStatus() {
            for (let key in this.screenAllSelected) {
                if (this.screenEffect(key, this.screenAllSelected[key].value)) return true;
            }
            return false;
        }
    },

    methods: {
        // 初始化组件
        init() {
            // 处理组件配置
            this.handleComponentConfig();

            // 处理传进来的默认排序
            this.handleCurrentSort();

            // 默认排序
            this.sortChange(this.currentSort);

            // 注册设置了分组的筛选
            this.group();

            // 处理数据
            this.handleData();
        },

        // 处理组件配置
        handleComponentConfig() {
            // 处理搜索配置
            this.searchInside = this.configTemplateRecursion(this.search, this.searchInside);
            if (this.searchInside.field === '*') this.searchInside.field = Object.keys(this.list[0]);

            // 处理筛选配置
            this.screenInside = this.configTemplateRecursion(this.screen, this.screenInside);
            this.screenInside.groupCountType = this.screen.groupCountType.length > 0 ? this.screen.groupCountType : this.screenInside.groupCountType;
            for (var i = 0; i < this.screen.list.length; i++) {
                this.screenInside.list.push(this.configTemplateRecursion(this.screen.list[i], this.assign(this.screenItem)));
            }

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

            // 处理表格配置
            this.tableInside = this.configTemplateRecursion(this.table, this.tableInside);

            for (var i = 0; i < this.table.column.length; i++) {
                this.tableInside.column.push(this.configTemplate(this.table.column[i], this.assign(this.tableColumn), true));
            }

            this.tableInside.config = this.configTemplate(this.table.config, this.tableInside.config, true);

            // 处理分页组件配置
            this.paginationInside = this.configTemplate(this.pagination, this.paginationInside, true);

            // 处理加载组件配置
            this.loadingInside = this.configTemplateRecursion(this.loading, this.loadingInside);
            this.loadingInside.config = this.configTemplate(this.loading.config, this.loadingInside.config, true);
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

        // 处理数据 先搜索 再筛选 再排序
        // 每次搜索或筛选执行这里 排序直接执行sortChange
        handleData(index = -1, source = '') {
            if (source === 'searchInput' && this.searchInside.inputConfig.onInput !== true) return false;
            if (source === 'searchChange' && this.searchInside.inputConfig.onChange !== true) return false;

            const loading = this.loadingInside.status ? ElementPlus.ElLoading.service(this.loadingInside.config) : null;

            setTimeout(() => {
                setTimeout(() => {
                    // 计算出每个筛选选项的数量
                    if (this.screenInside.status) this.screenStatistics(index);
                }, 500);

                // 先处理搜索 把结果赋值给screenList
                this.searchFunc(this.assign(this.list));

                // 再从screenList里筛选 把结果赋值给screenList
                this.screenFunc();

                // 再从screenList里根据currentSort排序 把结果赋值给screenList
                this.sortChange(this.currentSort);

                if (loading) loading.close();
            }, 30);
        },

        // 将传进来的html中的属性转换成对应的值
        handleHtml(data) {
            var html = this.listItem;
            for (let key in data) html = html.replaceAll('{ ' + key + ' }', data[key]);
            return html;
        },

        // 根据关键词对可搜索的字段筛选查询
        searchFunc(list) {
            if (this.keywords.length > 0 && this.searchInside.field.length == 0) {
                this.screenList = [];
                return false;
            }

            if (!this.searchInside.status) {
                this.screenList = list;
                return false;
            }

            var screenList = [], keywords = this.searchInside.caseSensitive ? this.keywords : this.keywords.toLowerCase();
            for (var i = 0; i < list.length; i++) {
                for (var j = 0; j < this.searchInside.field.length; j++) {
                    let value = this.searchInside.caseSensitive
                    ? list[i][this.searchInside.field[j]].toString()
                    : list[i][this.searchInside.field[j]].toString().toLowerCase();
                    if (value.indexOf(keywords) !== -1) {
                        screenList.push(list[i]);
                        break;
                    }
                }
            }
            this.screenList = screenList;
        },

        // 获取可分组的字段的选项
        group() {
            for (var i = 0; i < this.screenInside.list.length; i++) {
                if (this.screenInside.list[i].type == 1) {
                    this.screenRegister(this.screenInside.list[i], this.valueListByAttr(this.screenInside.list[i].field), this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : 'All', 'All');
                }
                if (this.screenInside.list[i].type == 2) {
                    this.screenRegister(this.screenInside.list[i], this.valueListByAttr(this.screenInside.list[i].field), this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : this.assign([]), []);
                }
                if (this.screenInside.list[i].type == 3) {
                    if (this.screenInside.list[i].config.range === true) {
                        this.screenRegister(this.screenInside.list[i], [], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : [this.screenInside.list[i].config.min, this.screenInside.list[i].config.max], [this.screenInside.list[i].config.min, this.screenInside.list[i].config.max]);
                    } else {
                        this.screenRegister(this.screenInside.list[i], [], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : 0, 0);
                    }
                }
                if (this.screenInside.list[i].type == 4) {
                    this.screenRegister(this.screenInside.list[i], [], this.screenInside.list[i].default, null);
                }
                if (this.screenInside.list[i].type == 5) {
                    this.screenRegister(this.screenInside.list[i], this.valueListByAttr(this.screenInside.list[i].field), this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : '', '');
                }
            }

            if (this.screenInside.type == 3) {
                for (let key in this.screenAll) {
                    if (this.screenEffect(key, this.screenAllSelected[key].value)) this.screenChange(key);
                }
            }
        },

        // 注册筛选项
        screenRegister(data, list, defaultValue, all) {
            for (var i = 0; i < list.length; i++) list[i] = { name: list[i], count: 0 };

            if (data.type == 1) list.unshift({ name: 'All', count: 0 });

            var config = data.config, configGroup = data.configGroup;

            if (data.type == 1 || data.type == 2) {
                this.screenAllSelected[data.name] = { type: data.type, value: defaultValue, config: config };
                this.screenAll[data.name] = { label: data.field, list: list, type: data.type, default: defaultValue, config: config, configGroup: configGroup, all: all };
                return false;
            }

            if (data.type == 4) {
                config.type = config.type || 'datetimerange';
                if (config.type == 'datetimerange' || config.type == 'daterange') {
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
                    list.unshift({ name: 'All', count: 0 });
                }
            }

            this.screenAllSelected[data.name] = { type: data.type, value: defaultValue, config: config };

            this.screenAll[data.name] = { label: data.field, list: list, type: data.type, default: defaultValue, config: config, all: all };
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

            this.handleData(index);
        },

        // 筛选
        screenFunc() {
            var data = this.assign(this.screenAllSelected), list = this.assign(this.screenList), screenList = [], condition = {};

            if (!this.screenInside.status) {
                this.screenList = list;
                return false;
            }

            for (var key in data) {
                if (this.screenEffect(key, data[key].value)) condition[key] = data[key];
            }

            if (Object.keys(condition).length == 0) {
                return false;
            }

            for (var i = 0; i < list.length; i++) {
                if (this.check(list[i], condition)) screenList.push(list[i]);
            }

            this.screenList = screenList;
        },

        // 根据screenInside.type计算出每个筛选选项的数量
        screenStatistics(index) {
            if (!this.screenInside.countStatus && !this.screenInside.nullHidden) return false;

            var list = this.assign(this.list);

            if (this.screenInside.type == 1 || this.screenInside.type == 2) {
                // 耗时0.5 - 0.6
                for (let key in this.screenAll) {
                    this.screenGroupCount(list, key);

                    if (this.screenEffect(key, this.screenAllSelected[key].value)) {
                        list = this.checkList(list, key, this.screenAllSelected[key]);
                    }
                }
            }

            if (this.screenInside.type == 3) {
                for (var i = 0; i < this.screenSort.length; i++) {
                    this.screenGroupCount(list, this.screenSort[i]);

                    if (this.screenEffect(this.screenSort[i], this.screenAllSelected[this.screenSort[i]].value)) {
                        list = this.checkList(list, this.screenSort[i], this.screenAllSelected[this.screenSort[i]]);
                    }
                }

                for (let key in this.screenAll) {
                    if (this.screenSort.indexOf(key) !== -1) continue;

                    this.screenGroupCount(list, key);

                    if (this.screenEffect(key, this.screenAllSelected[key].value)) {
                        list = this.checkList(list, key, this.screenAllSelected[key]);
                    }
                }
            }

            if (this.screenInside.type == 4) {
                for (let key in this.screenAll) {
                    var list = this.assign(this.list);

                    for (let key2 in this.screenAll) {
                        if (key == key2) continue;

                        if (this.screenEffect(key2, this.screenAllSelected[key2].value)) {
                            list = this.checkList(list, key2, this.screenAllSelected[key2]);
                        }
                    }

                    this.screenGroupCount(list, key);
                }
            }
        },

        // 判断一个筛选项的结果有没有生效
        screenEffect(name, data) {
            return JSON.stringify(this.screenAll[name].all) != JSON.stringify(data) && data !== null;
        },

        // 根据集合计算筛选组里每个筛选项对应的数据数量
        screenGroupCount(list, name) {
            if (this.screenInside.groupCountType.indexOf(this.screenAll[name].type) === -1) return false;

            for (var i = 0; i < this.screenAll[name].list.length; i++) {
                let value = { type: this.screenAll[name].type, value: this.screenAll[name].list[i].name };
                if (value.type == 2 || this.screenAll[name].config.multiple === true) value.value = [value.value];
                this.screenAll[name].list[i].count = this.checkList(list, name, value).length;
            }
        },

        // 获取列表里某个属性符合某个值的集合
        checkList(list, name, value) {
            var list2 = [];

            for (var i = 0; i < list.length; i++) {
                if (this.check(list[i], { [name]: value })) list2.push(list[i]);
            }

            return list2;
        },

        // 获取list里某个属性存在的值
        valueListByAttr(attr) {
            let list = [];
            for (var j = 0; j < this.list.length; j++) {
                if (this.cuttingSymbol.length == 0 || this.list[j][attr].indexOf(this.cuttingSymbol) === -1) {
                    if (list.indexOf(this.list[j][attr]) == -1) {
                        list.push(this.list[j][attr]);
                    }
                } else {
                    let list2 = this.list[j][attr].split(this.cuttingSymbol);
                    for (var k = 0; k < list2.length; k++) {
                        if (list.indexOf(list2[k]) == -1) {
                            list.push(list2[k]);
                        }
                    }
                }
            }
            return list;
        },

        // 判断一条数据符合不符合条件
        check(data, condition) {
            for (let key in condition) {
                if (condition[key].type == 1) {
                    let value = this.cuttingSymbol.length == 0 ? data[key] : data[key].split(this.cuttingSymbol);
                    if (condition[key].value !== 'All' && value.indexOf(condition[key].value) === -1) return false;
                }
                if (condition[key].type == 2) {
                    let value = this.cuttingSymbol.length == 0 ? data[key] : data[key].split(this.cuttingSymbol);
                    if (condition[key].value.length > 0 && this.intersect(value, condition[key].value).length == 0) return false;
                }
                if (condition[key].type == 3) {
                    let value = parseInt(data[key]);
                    if (this.screenAll[key].config.range === true) {
                        if (value < condition[key].value[0] || value > condition[key].value[1]) return false;
                    } else {
                        if (value != condition[key].value) return false;
                    }
                }
                if (condition[key].type == 4) {
                    let value = typeof data[key] == 'number' ? data[key] : Date.parse(data[key]) / 1000;
                    let date = this.dateRange(condition[key].value, condition[key].config.type);
                    if (value < date[0] || value > date[1]) return false;
                }
                if (condition[key].type == 5) {
                    let value = this.cuttingSymbol.length == 0 ? data[key] : data[key].split(this.cuttingSymbol);
                    if (this.screenAll[key].config.multiple === true) {
                        if (condition[key].value.length > 0 && this.intersect(value, condition[key].value).length == 0) return false;
                    } else if (this.screenAll[key].config.clearable === true) {
                        if (value.indexOf(condition[key].value) === -1) return false;
                    } else {
                        if (condition[key].value !== 'All' && value.indexOf(condition[key].value) === -1) return false;
                    }
                }
            }
            return true;
        },

        // 取消筛选
        screenClear(name = null, value = null) {
            if (name === null && value === null) {
                for (let key in this.screenAllSelected) this.screenAllSelected[key] = this.screenReset(key);

                this.screenSort = [];
            } else {
                if (this.screenAllSelected[name].type == 2 || (this.screenAllSelected[name].type == 5 && this.screenAllSelected[name].value instanceof Array)) {
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

        // 根据筛选结果的类型返回重置结果
        screenReset(name) {
            return { type: this.screenAll[name].type, value: this.screenAll[name].all };
        },

        // 处理默认排序
        handleCurrentSort() {
            if (this.tableInside.status) {
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
                this.defaultSort = this.currentSort;
            }
        },

        // 排序时对整个筛选后的数组排序，默认某个字段正序
        sortChange(data) {
            if (data.order === null) data = this.defaultSort;

            this.currentSort = data;

            var number = typeof this.list[0][data.prop] === 'number';

            if (data.order === 'ascending') {
                this.screenList = this.sort(this.screenList, data.prop, 'asc', number ? 1 : 2);
            }
            if (data.order === 'descending') {
                this.screenList = this.sort(this.screenList, data.prop, 'desc', number ? 1 : 2);
            }
        },

        // 数组根据属性值排序，数字、字母和汉字
        sort(list, field, order, type) {
            const _this = this;
            list = this.assign(list);

            if (type == 1 && order == 'asc') {
                var func = function (a, b) {
                    return _this.sortFunc(a[field], b[field], 1);
                }
            }
            if (type == 1 && order == 'desc') {
                var func = function (a, b) {
                    return _this.sortFunc(b[field], a[field], 1);
                }
            }
            if (type == 2 && order == 'asc') {
                var func = function (a, b) {
                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();
                    return _this.sortFunc(a, b, 2);
                }
            }
            if (type == 2 && order == 'desc') {
                var func = function (a, b) {
                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();
                    return _this.sortFunc(b, a, 2);
                }
            }
            if (type == 3 && order == 'asc') {
                var func = function (a, b) {
                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();
                    return a.localeCompare(b, 'zh');
                }
            }
            if (type == 3 && order == 'desc') {
                var func = function (a, b) {
                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();
                    return b.localeCompare(a, 'zh');
                }
            }

            return list.sort(func);
        },

        // 日期格式化
        date(time, detailed) {
            if (detailed) {
                return new Date(parseInt(time) * 1000).toLocaleString().replaceAll('/', '-');
            } else {
                return new Date(parseInt(time) * 1000).toLocaleString().replaceAll('/', '-').split(' ')[0];
            }
        },

        dateRange(data, type) {
            if (data instanceof Array) {
                data[0] = parseInt(data[0]);
                data[1] = parseInt(data[1]);
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

        // 筛选函数
        sortFunc(a, b, n, i = 0) {
            if (n == 1) return a - b;

            if (n == 2) {
                if (a.charCodeAt(i) == b.charCodeAt(i)) {
                    return this.sortFunc(a, b, n, i + 1);
                } else {
                    return a.charCodeAt(i) - b.charCodeAt(i);
                }
            }
        },

        // 获取变量的数据
        assign(a) {
            return JSON.parse(JSON.stringify(a));
        },

        // 取数组交集
        intersect(a, b) {
            return a.filter(item => new Set(b).has(item))
        },

        // 数组去重
        unique(a) {
            return [...new Set(a)];
        }
    },
};