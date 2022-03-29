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
//     button: true,       按钮样式
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
//     button: true,       按钮样式
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
// 6 日期范围 如果不想选择时间 可以只选择日期 时间自行配置 根据组件配置传值 例：
// {
//     name: 'time',       筛选组前面的名字
//     field: 'field',     筛选的字段
//     type: 6,            筛选类型
//     default: '',        默认值
//     config: {           DatePicker属性
//                         属性详见官网
//     }
// }

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
            <div v-if="searchStatus" :class="searchClass">
                <el-input
                    v-model="keywords"
                    :type="searchInputInside.type"
                    :maxlength="searchInputInside.maxlength"
                    :minlength="searchInputInside.minlength"
                    :show-word-limit="searchInputInside.showWordLimit"
                    :placeholder="searchInputInside.placeholder"
                    :clearable="searchInputInside.clearable"
                    :show-password="searchInputInside.showPassword"
                    :disabled="searchInputInside.disabled"
                    :size="searchInputInside.size"
                    :prefix-icon="searchInputInside.prefixIcon"
                    :suffix-icon="searchInputInside.suffixIcon"
                    :rows="searchInputInside.rows"
                    :autosize="searchInputInside.autosize"
                    :autocomplete="searchInputInside.autocomplete"
                    :name="searchInputInside.name"
                    :readonly="searchInputInside.readonly"
                    :max="searchInputInside.max"
                    :min="searchInputInside.min"
                    :step="searchInputInside.step"
                    :resize="searchInputInside.resize"
                    :autofocus="searchInputInside.autofocus"
                    :form="searchInputInside.form"
                    :label="searchInputInside.label"
                    :tabindex="searchInputInside.tabindex"
                    :validate-event="searchInputInside.validateEvent"
                    :input-style="searchInputInside.inputStyle"
                    @input="handleData(-1, 'searchInput')"
                    @change="handleData(-1, 'searchChange')"
                ></el-input>
                <el-button
                    v-if="searchButtonInside.status"
                    :size="searchButtonInside.size"
                    :type="searchButtonInside.type"
                    :plain="searchButtonInside.plain"
                    :round="searchButtonInside.round"
                    :circle="searchButtonInside.circle"
                    :loading="searchButtonInside.loading"
                    :loading-icon="searchButtonInside.loadingIcon"
                    :disabled="searchButtonInside.disabled"
                    :icon="searchButtonInside.icon"
                    :autofocus="searchButtonInside.autofocus"
                    :native-type="searchButtonInside.nativeType"
                    :auto-insert-space="searchButtonInside.autoInsertSpace"
                    @click="handleData"
                >{{ searchButtonInside.text }}</el-button>
            </div>

            <div :class="contentClass">
                <div v-if="screenStatus" :class="screenClass">
                    <div v-if="screenUserStatus" :class="screenSelectedClass">
                        <div v-if="Object.keys(screenAllSelected).length > 0">
                            <template v-for="(item, key) in screenAllSelected">
                                <span v-if="item.type == 1 && screenEffect(key, item.value)" @click="screenClear(key, item.value)">{{ item.value }}</span>

                                <span v-if="item.type == 2 && screenEffect(key, item.value)" v-for="(it, k) in item.value" @click="screenClear(key, it)">{{ it }}</span>

                                <template v-if="item.type == 3 && screenEffect(key, item.value)">
                                    <span v-if="item.value instanceof Array" @click="screenClear(key, it)">{{ item.value[0] }} - {{ item.value[1] }}</span>
                                    <span v-if="typeof item.value == 'string'" @click="screenClear(key, it)">{{ item.value }}</span>
                                </template>

                                <span v-if="item.type == 4 && screenEffect(key, item.value)" @click="screenClear(key)">{{ date(parseInt(item.value[0])) }} - {{ date(parseInt(item.value[1])) }}</span>

                                <template v-if="item.type == 5 && screenEffect(key, item.value)">
                                    <span v-if="item.value instanceof Array" v-for="it in item.value" @click="screenClear(key, it)">{{ it }}</span>
                                    <span v-if="typeof item.value == 'string'" @click="screenClear(key, item.value)">{{ item.value }}</span>
                                </template>

                                <span v-if="item.type == 6 && screenEffect(key, item.value)" @click="screenClear(key)">{{ date(parseInt(item.value[0])) }} - {{ date(parseInt(item.value[1])) }}</span>
                            </template>
                            <span v-if="screenSelectedStatus" @click="screenClear()">{{ screenClearText }}</span>
                        </div>
                    </div>

                    <div :class="screenAllClass">
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
                                        v-if="(it.count > 0 || !screenNullHidden) && item.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                    >{{ it.name }}{{ screenCountStatus ? '(' + it.count + ')' : '' }}</el-radio-button>
                                    <el-radio
                                        v-if="(it.count > 0 || !screenNullHidden) && !item.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                    >{{ it.name }}{{ screenCountStatus ? '(' + it.count + ')' : '' }}</el-radio>
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
                                        v-if="(it.count > 0 || !screenNullHidden) && item.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                        :indeterminate="item.config.indeterminate"
                                    >{{ it.name }}{{ screenCountStatus ? '(' + it.count + ')' : '' }}</el-checkbox-button>
                                    <el-checkbox
                                        v-if="(it.count > 0 || !screenNullHidden) && !item.button"
                                        :label="it.name"
                                        :border="item.config.border"
                                        :size="item.config.size"
                                        :name="item.config.name"
                                        :indeterminate="item.config.indeterminate"
                                    >{{ it.name }}{{ screenCountStatus ? '(' + it.count + ')' : '' }}</el-checkbox>
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
                                type="datetimerange"
                                value-format="X"
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
                                <el-option v-for="item in screenAll[key].list" :key="item.name" :label="item.name" :value="item.name">{{ item.name }}{{ screenCountStatus ? '(' + item.count + ')' : '' }}</el-option>
                            </el-select>

                            <el-date-picker
                                v-if="item.type == 6"
                                v-model="screenAllSelected[key].value"
                                type="daterange"
                                value-format="X"
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
                        </div>
                    </div>
                </div>

                <div :class="dataClass">
                    <el-radio-group
                        v-if="selectorInside"
                        v-model="selectorInside.value"
                        :size="selectorInside.size"
                        :disabled="selectorInside.disabled"
                        :text-color="selectorInside.textColor"
                        :fill="selectorInside.fill"
                    >
                        <el-radio-button
                            v-for="(item, key) in selectorInside.list"
                            :label="key"
                        >{{ item.text }}</el-radio-button>
                    </el-radio-group>

                    <div :class="listClass">
                        <el-table
                            v-if="tableHeader.length > 0 && (!selectorInside || selectorInside.value == 'table')"
                            :data="currentList"
                            :height="tableInside.height"
                            :max-height="tableInside.maxHeight"
                            :stripe="tableInside.stripe"
                            :border="tableInside.border"
                            :size="tableInside.size"
                            :fit="tableInside.fit"
                            :show-header="tableInside.showHeader"
                            :highlight-current-row="tableInside.highlightCurrentRow"
                            :current-row-key="tableInside.currentRowKey"
                            :row-class-name="tableInside.rowClassName"
                            :row-style="tableInside.rowStyle"
                            :cell-class-name="tableInside.cellClassName"
                            :cell-style="tableInside.cellStyle"
                            :header-row-class-name="tableInside.headerRowClassName"
                            :header-row-style="tableInside.headerRowStyle"
                            :header-cell-class-name="tableInside.headerCellClassName"
                            :header-cell-style="tableInside.headerCellStyle"
                            :row-key="tableInside.rowKey"
                            :empty-text="tableInside.emptyText"
                            :default-expand-all="tableInside.defaultExpandAll"
                            :expand-row-keys="tableInside.expandRowKeys"
                            :tooltip-effect="tableInside.tooltipEffect"
                            :show-summary="tableInside.showSummary"
                            :sum-text="tableInside.sumText"
                            :summary-method="tableInside.summaryMethod"
                            :span-method="tableInside.spanMethod"
                            :select-on-indeterminate="tableInside.selectOnIndeterminate"
                            :indent="tableInside.indent"
                            :lazy="tableInside.lazy"
                            :load="tableInside.load"
                            :tree-props="tableInside.treeProps"
                            :table-layout="tableInside.tableLayout"
                            :scrollbar-always-on="tableInside.scrollbarAlwaysOn"
                            @sort-change="sortChange"
                            style="width: 100%"
                        >
                            <el-table-column
                                v-for="item in tableHeader"
                                :prop="item.field"
                                :label="item.title || item.field"
                                :sortable="item.sortable === true ? 'custom' : false"
                                :formatter="checkConfig2(item, tableColumnInside, 'formatter')"
                                :type="checkConfig2(item, tableColumnInside, 'type')"
                                :index="checkConfig2(item, tableColumnInside, 'index')"
                                :column-key="checkConfig2(item, tableColumnInside, 'columnKey')"
                                :width="checkConfig2(item, tableColumnInside, 'width')"
                                :min-width="checkConfig2(item, tableColumnInside, 'minWidth')"
                                :fixed="checkConfig2(item, tableColumnInside, 'fixed')"
                                :render-header="checkConfig2(item, tableColumnInside, 'renderHeader')"
                                :sort-method="checkConfig2(item, tableColumnInside, 'sortMethod')"
                                :sort-by="checkConfig2(item, tableColumnInside, 'sortBy')"
                                :sort-orders="checkConfig2(item, tableColumnInside, 'sortOrders')"
                                :resizable="checkConfig2(item, tableColumnInside, 'resizable')"
                                :show-overflow-tooltip="checkConfig2(item, tableColumnInside, 'showOverflowTooltip')"
                                :align="checkConfig2(item, tableColumnInside, 'align')"
                                :header-align="checkConfig2(item, tableColumnInside, 'headerAlign')"
                                :class-name="checkConfig2(item, tableColumnInside, 'className')"
                                :label-class-name="checkConfig2(item, tableColumnInside, 'labelClassName')"
                                :selectable="checkConfig2(item, tableColumnInside, 'selectable')"
                                :reserve-selection="checkConfig2(item, tableColumnInside, 'reserveSelection')"
                                :filters="checkConfig2(item, tableColumnInside, 'filters')"
                                :filter-placement="checkConfig2(item, tableColumnInside, 'filterPlacement')"
                                :filter-multiple="checkConfig2(item, tableColumnInside, 'filterMultiple')"
                                :filter-method="checkConfig2(item, tableColumnInside, 'filterMethod')"
                                :filtered-value="checkConfig2(item, tableColumnInside, 'filteredValue')"
                            ></el-table-column>
                        </el-table>

                        <template v-if="listItem.length > 0 && (!selectorInside || selectorInside.value == 'list')">
                            <p v-if="currentList.length == 0">{{ dataEmptyText }}</p>

                            <div v-else v-for="item in currentList" :class="listItemClass" v-html="handleHtml(item)"></div>
                        </template>
                    </div>

                    <div :class="pageClass">
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

        // 如果列表是表格 传表头 对象数组
        // 对象的属性
        // 属性 必填 合法值 默认值 注释
        // field                    是       list里的属性     无                  字段名字
        // title                    否       任意字符串       field值             表格表头显示的名字 不传用字段名字
        // sortable                 否       true            false               true表示该字段允许排序
        // sortableDefaultField     否       true            false               默认排序用这个字段 不传用sortable的第一个字段
        // sortableDefaultMode      否       asc,desc        asc                 默认排序的方式

        // 例：[{ field: 'name', sortable: true }, { field: 'sex', title: 'Sex' }]
        tableHeader: { type: Array, default: [] },

        // 如果列表是列表 传一项的html
        listItem: { type: String, default: '' },

        // 搜索框配置 onInput input的input事件触发搜索 onChange input的失去焦点或回车事件触发搜索
        searchInput: { type: Object, default: {} },

        // 搜索按钮配置 status 启用搜索按钮 text 搜索按钮的文本
        searchButton: { type: Object, default: {} },

        // 模式选择器配置
        selector: { type: Object, default: {} },

        // 表格配置
        table: { type: Object, default: {} },

        // 分页组件配置
        pagination: { type: Object, default: {} },

        // 允许加载内容时出现加载的动态效果
        loadingStatus: { type: Boolean, default: false },

        // 加载配置
        loading: { type: Object, default: {} },

        // 是否启用搜索功能
        searchStatus: { type: Boolean, default: true },

        // 默认关键词
        keywordsDefault: { type: String, default: '' },

        // 数据里多个数据放在一个属性里时的切割符号 空字符串表示不存在多个数据 暂不支持多个切割符号
        cuttingSymbol: { type: String, default: ', ' },

        // 是否启用筛选功能
        screenStatus: { type: Boolean, default: true },

        // 清空全部筛选的文本
        screenClearText: { type: String, default: 'reset' },

        // 是否启用显示已筛选项
        screenUserStatus: { type: Boolean, default: true },

        // 是否启用显示筛选项后面的数值
        screenCountStatus: { type: Boolean, default: false },

        // 允许计算数值的筛选组类型 默认为单选、多选和下拉菜单
        screenGroupCountType: { type: Array, default: [1, 2, 5] },

        // 筛选组之间的关联方式
        // 1 根据screenAll的内容排序 逐级计算数量 0.5
        // 2 根据screenAll的内容排序 逐级计算数量 且点击高级的筛选组时会重置低级的筛选组 0.5
        // 3 根据点击筛选组的顺序排序 逐级计算数量 0.4
        // 4 根据其他筛选组筛选的结果计算当前筛选组每个筛选项的数量 1
        screenType: { type: Number, default: 1 },

        // 隐藏对应数据数量为0的选项
        screenNullHidden: { type: Boolean, default: false },

        // 排序时大小写敏感
        sortCaseSensitive: { type: Boolean, default: true },

        // 搜索时大小写敏感
        searchCaseSensitive: { type: Boolean, default: true },

        // 允许搜索的字段 一维数组 ['name', 'sex']
        searchable: { type: Array, default: [] },

        // 允许筛选的字段 对象数组 筛选组的名字（name）、字段（field）和筛选类型（type）
        // 筛选类型 1.单选 2.多选 3.滑块 4.日期范围 5.下拉菜单
        // 滑块在传范围时范围必须包含全部数值 否则在范围选择最大值到最小值时同样会展示出来 因为范围选择了最大值到最小值则不会做任何筛选
        // [{ name: 'by name', field: 'name', type: 1 }]
        groupable: { type: Array, default: [] },

        // 数据为空时的提示
        dataEmptyText: { type: String, default: 'No Data' },

        // 一些元素的class
        className: { type: String, default: 'data' },
        searchClass: { type: String, default: 'data-search' },
        contentClass: { type: String, default: 'data-content' },
        screenClass: { type: String, default: 'data-screen' },
        screenSelectedClass: { type: String, default: 'data-screen-selected' },
        screenAllClass: { type: String, default: 'data-screen-all' },
        dataClass: { type: String, default: 'data-data' },
        listClass: { type: String, default: 'data-list' },
        listItemClass: { type: String, default: 'data-list-item' },
        pageClass: { type: String, default: 'data-page' }
    },

    data: function() {
        return {
            // 搜索和筛选后的数据的集合
            screenList: this.list,

            // 搜索的关键词
            keywords: this.keywordsDefault,

            // screenType = 3 时储存点击顺序的数组
            screenSort: [],

            // 当前排序方式
            currentSort: {},

            // 默认排序方式
            defaultSort: {},

            // 全部筛选组
            screenAll: {},

            // 当前的筛选情况
            screenAllSelected: {},

            // 内容正在加载的提示
            loadingObject: null,

            // 搜索框默认配置
            searchInputInside: {
                // input的input事件触发搜索
                onInput: true,
                // input的失去焦点或回车事件触发搜索
                onChange: true,
                type: undefined,
                maxlength: undefined,
                minlength: undefined,
                showWordLimit: undefined,
                placeholder: undefined,
                clearable: undefined,
                showPassword: undefined,
                disabled: undefined,
                size: undefined,
                prefixIcon: undefined,
                suffixIcon: undefined,
                rows: undefined,
                autosize: undefined,
                autocomplete: undefined,
                name: undefined,
                readonly: undefined,
                max: undefined,
                min: undefined,
                step: undefined,
                resize: undefined,
                autofocus: undefined,
                form: undefined,
                label: undefined,
                tabindex: undefined,
                validateEvent: undefined,
                inputStyle: undefined
            },

            // 搜索按钮默认配置
            searchButtonInside: {
                // 启用搜索按钮
                status: true,
                // 搜索按钮的文本
                text: 'search',
                size: undefined,
                type: undefined,
                plain: undefined,
                round: undefined,
                circle: undefined,
                loading: undefined,
                loadingIcon: undefined,
                disabled: undefined,
                icon: undefined,
                autofocus: undefined,
                nativeType: undefined,
                autoInsertSpace: undefined
            },

            // 多选框默认配置
            checkboxInside: {
                // label: undefined,
                // trueLabel: undefined,
                // falseLabel: undefined,
                // disabled: undefined,
                border: undefined,
                size: undefined,
                name: undefined,
                // checked: undefined,
                indeterminate: undefined
            },

            // 多选框组默认配置
            checkboxGroupInside: {
                size: undefined,
                disabled: undefined,
                min: undefined,
                max: undefined,
                textColor: undefined,
                fill: undefined
            },

            // 单选框默认配置
            radioInside: {
                // label: undefined,
                // disabled: undefined,
                border: undefined,
                size: undefined,
                name: undefined
            },

            // 单选框组默认配置
            radioGroupInside: {
                size: undefined,
                disabled: undefined,
                textColor: undefined,
                fill: undefined
            },

            // 滑块默认配置
            sliderInside: {
                min: undefined,
                max: undefined,
                disabled: undefined,
                step: undefined,
                showInput: undefined,
                showInputControls: undefined,
                size: undefined,
                inputSize: undefined,
                showStops: undefined,
                showTooltip: undefined,
                formatTooltip: undefined,
                range: undefined,
                vertical: undefined,
                height: undefined,
                label: undefined,
                debounce: undefined,
                tooltipClass: undefined,
                marks: undefined,
            },

            // 日期时间选择器默认配置
            dateTimePickerInside: {
                disabled: undefined,
                editable: undefined,
                clearable: undefined,
                size: undefined,
                placeholder: undefined,
                startPlaceholder: 'Start Date',
                endPlaceholder: 'End Date',
                timeArrowControl: undefined,
                format: undefined,
                popperClass: undefined,
                rangeSeparator: 'to',
                defaultValue: undefined,
                defaultTime: undefined,
                valueFormat: undefined,
                id: undefined,
                name: undefined,
                unlinkPanels: undefined,
                prefixIcon: undefined,
                clearIcon: undefined,
                shortcuts: undefined,
                disabledDate: undefined,
                cellClassName: undefined,
                teleported: undefined
            },

            // 选择器默认配置
            selectInside: {
                multiple: undefined,
                disabled: undefined,
                valueKey: undefined,
                size: undefined,
                clearable: undefined,
                collapseTags: undefined,
                collapseTagsTooltip: undefined,
                multipleLimit: undefined,
                name: undefined,
                effect: undefined,
                autocomplete: undefined,
                placeholder: undefined,
                filterable: undefined,
                allowCreate: undefined,
                filterMethod: undefined,
                remote: undefined,
                remoteMethod: undefined,
                loading: undefined,
                loadingText: undefined,
                noMatchText: undefined,
                noDataText: undefined,
                popperClass: undefined,
                reserveKeyword: undefined,
                defaultFirstOption: undefined,
                teleported: undefined,
                persistent: undefined,
                automaticDropdown: undefined,
                clearIcon: undefined,
                fitInputWidth: undefined,
                suffixIcon: undefined,
                tagType: undefined,
            },

            // 日期选择器默认配置
            datePickerInside: {
                disabled: undefined,
                size: undefined,
                editable: undefined,
                clearable: undefined,
                placeholder: undefined,
                startPlaceholder: 'Start Date',
                endPlaceholder: 'End Date',
                type: undefined,
                format: undefined,
                popperClass: undefined,
                rangeSeparator: 'to',
                defaultValue: undefined,
                defaultTime: undefined,
                valueFormat: undefined,
                id: undefined,
                name: undefined,
                unlinkPanels: undefined,
                prefixIcon: undefined,
                clearIcon: undefined,
                validateEvent: undefined,
                disabledDate: undefined,
                shortcuts: undefined,
                cellClassName: undefined,
                teleported: undefined
            },

            // 表格默认配置
            tableInside: {
                height: undefined,
                maxHeight: undefined,
                stripe: undefined,
                border: undefined,
                size: undefined,
                fit: undefined,
                showHeader: undefined,
                highlightCurrentRow: undefined,
                currentRowKey: undefined,
                rowClassName: undefined,
                rowStyle: undefined,
                cellClassName: undefined,
                cellStyle: undefined,
                headerRowClassName: undefined,
                headerRowStyle: undefined,
                headerCellClassName: undefined,
                headerCellStyle: undefined,
                rowKey: undefined,
                emptyText: undefined,
                defaultExpandAll: undefined,
                expandRowKeys: undefined,
                tooltipEffect: undefined,
                showSummary: undefined,
                sumText: undefined,
                summaryMethod: undefined,
                spanMethod: undefined,
                selectOnIndeterminate: undefined,
                indent: undefined,
                lazy: undefined,
                load: undefined,
                treeProps: undefined,
                tableLayout: undefined,
                scrollbarAlwaysOn: undefined
            },

            // 表格列默认配置
            tableColumnInside: {
                type: undefined,
                index: undefined,
                label: undefined,
                columnKey: undefined,
                prop: undefined,
                width: undefined,
                minWidth: undefined,
                fixed: undefined,
                renderHeader: undefined,
                sortable: undefined,
                sortMethod: undefined,
                sortBy: undefined,
                sortOrders: undefined,
                resizable: undefined,
                formatter: undefined,
                showOverflowTooltip: undefined,
                align: undefined,
                headerAlign: undefined,
                className: undefined,
                labelClassName: undefined,
                selectable: undefined,
                reserveSelection: undefined,
                filters: undefined,
                filterPlacement: undefined,
                filterMultiple: undefined,
                filterMethod: undefined,
                filteredValue: undefined
            },

            // 模式选择器默认配置
            selectorInside: {
                value: '',
                size: undefined,
                disabled: undefined,
                textColor: undefined,
                fill: undefined,
                list: {
                    table: {
                        text: 'table',
                        default: undefined
                    },
                    list: {
                        text: 'list',
                        default: undefined
                    }
                }
            },

            // 分页组件配置
            paginationInside: {
                small: undefined,
                background: undefined,
                pageSize: 10,
                defaultPageSize: undefined,
                total: undefined,
                pageCount: undefined,
                pagerCount: undefined,
                currentPage: 1,
                defaultCurrentPage: undefined,
                layout: undefined,
                pageSizes: undefined,
                popperClass: undefined,
                prevText: undefined,
                nextText: undefined,
                disabled: undefined,
                hideOnSinglePage: undefined
            },

            // 加载组件配置
            loadingInside: {
                target: undefined,
                body: undefined,
                fullscreen: undefined,
                lock: undefined,
                text: undefined,
                spinner: undefined,
                background: undefined,
                customClass: undefined
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
            // 处理搜索框配置
            for (let key in this.searchInputInside) {
                this.searchInputInside[key] = this.checkConfig('searchInput', key);
            }

            // 处理搜索按钮配置
            for (let key in this.searchButtonInside) {
                this.searchButtonInside[key] = this.checkConfig('searchButton', key);
            }

            // 处理表格配置
            for (let key in this.tableInside) {
                this.tableInside[key] = this.checkConfig('table', key);
            }

            // 处理选择器配置
            if (this.tableHeader.length == 0 || this.listItem.length == 0) {
                this.selectorInside = false;
            } else {
                for (let key in this.selectorInside) {
                    this.selectorInside[key] = this.checkConfig('selector', key);
                }

                for (let key in this.selectorInside.list) {
                    for (let name in this.selectorInside.list[key]) {
                        this.selectorInside.list[key][name] = this.selector.list === undefined || this.selector.list[key] === undefined || this.selector.list[key][name] === undefined ? this.selectorInside.list[key][name] : this.selector.list[key][name];
                    }
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
            for (let key in this.paginationInside) {
                this.paginationInside[key] = this.checkConfig('pagination', key);
            }

            // 处理加载组件配置
            for (let key in this.loadingInside) {
                this.loadingInside[key] = this.checkConfig('loading', key);
            }
        },

        // 处理数据 先搜索 再筛选 再排序
        // 每次搜索或筛选执行这里 排序直接执行sortChange
        handleData(index = -1, source = '') {
            if (source === 'searchInput' && this.searchInputInside.onInput !== true) return false;
            if (source === 'searchChange' && this.searchInputInside.onChange !== true) return false;

            const loading = ElementPlus.ElLoading.service(this.loadingInside);

            setTimeout(() => {
                setTimeout(() => {
                    // 计算出每个筛选选项的数量
                    if (this.screenStatus && this.screenCountStatus) this.screenStatistics(index);
                }, 500);

                // 先处理搜索 把结果赋值给screenList
                if (this.searchStatus) this.searchList(this.assign(this.list));

                // 再从screenList里筛选 把结果赋值给screenList
                if (this.screenStatus) this.screen();

                // 再从screenList里根据currentSort排序 把结果赋值给screenList
                this.sortChange(this.currentSort);

                loading.close();
            }, 30);
        },

        // 将传进来的html中的属性转换成对应的值
        handleHtml(data) {
            var html = this.listItem;
            for (let key in data) html = html.replaceAll('{ ' + key + ' }', data[key]);
            return html;
        },

        // 根据关键词对可搜索的字段筛选查询
        searchList(list) {
            if (this.keywords.length > 0 && this.searchable.length == 0) {
                this.screenList = [];
                return false;
            }

            var list = [], keywords = this.searchCaseSensitive ? this.keywords : this.keywords.toLowerCase();
            for (var i = 0; i < this.list.length; i++) {
                for (var j = 0; j < this.searchable.length; j++) {
                    let value = this.searchCaseSensitive ? this.list[i][this.searchable[j]] : this.list[i][this.searchable[j]].toLowerCase();
                    if (value.indexOf(keywords) !== -1) {
                        list.push(this.list[i]);
                        break;
                    }
                }
            }
            this.screenList = list;
        },

        // 获取可分组的字段的选项
        group() {
            for (var i = 0; i < this.groupable.length; i++) {
                if (this.groupable[i].type == 1) {
                    this.screenRegister(this.groupable[i], this.valueListByAttr(this.groupable[i].field), 'default' in this.groupable[i] ? this.groupable[i].default : 'All', 'All');
                }
                if (this.groupable[i].type == 2) {
                    this.screenRegister(this.groupable[i], this.valueListByAttr(this.groupable[i].field), 'default' in this.groupable[i] ? this.groupable[i].default : this.assign([]), []);
                }
                if (this.groupable[i].type == 3) {
                    if (this.groupable[i].config.range === true) {
                        this.screenRegister(this.groupable[i], [], 'default' in this.groupable[i] ? this.groupable[i].default : [this.groupable[i].config.min, this.groupable[i].config.max], [this.groupable[i].config.min, this.groupable[i].config.max]);
                    } else {
                        this.screenRegister(this.groupable[i], [], 'default' in this.groupable[i] ? this.groupable[i].default : 0, 0);
                    }
                }
                if (this.groupable[i].type == 4) {
                    this.screenRegister(this.groupable[i], [], 'default' in this.groupable[i] ? this.groupable[i].default : '', '');
                }
                if (this.groupable[i].type == 5) {
                    this.screenRegister(this.groupable[i], this.valueListByAttr(this.groupable[i].field), 'default' in this.groupable[i] ? this.groupable[i].default : '', '');
                }
                if (this.groupable[i].type == 6) {
                    this.screenRegister(this.groupable[i], [], 'default' in this.groupable[i] ? this.groupable[i].default : '', '');
                }
            }

            if (this.screenType == 3) {
                for (let key in this.screenAll) {
                    if (this.screenEffect(key, this.screenAllSelected[key].value)) this.screenChange(key);
                }
            }
        },

        // 注册筛选项
        screenRegister(data, list, defaultValue, all) {
            for (var i = 0; i < list.length; i++) list[i] = { name: list[i], count: 0 };

            if (data.type == 1) list.unshift({ name: 'All', count: 0 });

            var config = {}, configGroup = {}, button = data.button === true;

            if (data.type == 1 || data.type == 2) {
                for (let key in this.checkboxInside) {
                    config[key] = this.checkConfig2(data.config, this.checkboxInside, key);
                }
                for (let key in this.checkboxGroupInside) {
                    configGroup[key] = this.checkConfig2(data.configGroup, this.checkboxGroupInside, key);
                }

                this.screenAllSelected[data.name] = { type: data.type, value: defaultValue };
                this.screenAll[data.name] = { label: data.field, list: list, type: data.type, default: defaultValue, button: button, config: config, configGroup: configGroup, all: all };
                return false;
            }

            if (data.type == 3) {
                for (let key in this.sliderInside) config[key] = this.checkConfig2(data.config, this.sliderInside, key);
            }

            if (data.type == 4) {
                for (let key in this.dateTimePickerInside) config[key] = this.checkConfig2(data.config, this.dateTimePickerInside, key);
            }

            if (data.type == 5) {
                for (let key in this.selectInside) config[key] = this.checkConfig2(data.config, this.selectInside, key);

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

            if (data.type == 6) {
                for (let key in this.datePickerInside) config[key] = this.checkConfig2(data.config, this.datePickerInside, key);
            }

            this.screenAllSelected[data.name] = { type: data.type, value: defaultValue };

            this.screenAll[data.name] = { label: data.field, list: list, type: data.type, default: defaultValue, config: config, all: all };
        },

        // 用户点击进行筛选
        screenChange(name) {
            var index = Object.keys(this.screenAll).indexOf(name);

            // 点击筛选时重置下级组
            if (this.screenType == 2) {
                var i = -2;
                for (let key in this.screenAllSelected) {
                    i++;
                    if (i < index) continue;
                    this.screenAllSelected[key] = this.screenReset(key);
                }
            }

            // 把点击的顺序存进数组
            if (this.screenType == 3) {
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
        screen() {
            var data = this.assign(this.screenAllSelected), list = this.assign(this.screenList), screenList = [], condition = {};

            for (var key in data) {
                if (this.screenEffect(key, data[key].value)) condition[key] = data[key];
            }

            if (Object.keys(condition).length == 0) {
                this.screenList = list;
                return false;
            }

            for (var i = 0; i < list.length; i++) {
                if (this.check(list[i], condition)) screenList.push(list[i]);
            }

            this.screenList = screenList;
        },

        // 根据screenType计算出每个筛选选项的数量
        screenStatistics(index) {
            if (!this.screenCountStatus) return false;

            var list = this.assign(this.list);

            if (this.screenType == 1 || this.screenType == 2) {
                // 耗时0.5 - 0.6
                for (let key in this.screenAll) {
                    this.screenGroupCount(list, key);

                    if (this.screenEffect(key, this.screenAllSelected[key].value)) {
                        list = this.checkList(list, key, this.screenAllSelected[key]);
                    }
                }
            }

            if (this.screenType == 3) {
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

            if (this.screenType == 4) {
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
            return JSON.stringify(this.screenAll[name].all) != JSON.stringify(data);
        },

        // 根据集合计算筛选组里每个筛选项对应的数据数量
        screenGroupCount(list, name) {
            if (this.screenGroupCountType.indexOf(this.screenAll[name].type) === -1) return false;

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
                    if (value < parseInt(condition[key].value[0]) || value > parseInt(condition[key].value[1])) return false;
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
                if (condition[key].type == 6) {
                    let value = typeof data[key] == 'number' ? data[key] : Date.parse(data[key]) / 1000;
                    if (value < parseInt(condition[key].value[0]) || value > parseInt(condition[key].value[1])) return false;
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
            if (this.tableHeader.length > 0) {
                for (var i = 0; i < this.tableHeader.length; i++) {
                    if (this.tableHeader[i].sortableDefaultField === true) {
                        this.currentSort.prop = this.tableHeader[i].field;
                        this.currentSort.order = this.tableHeader[i].sortableDefaultMode === 'desc' ? 'descending' : 'ascending';
                        break;
                    }
                }

                if (!this.currentSort.prop || !this.currentSort.order) {
                    for (var i = 0; i < this.tableHeader.length; i++) {
                        if (this.tableHeader[i].sortable === true) {
                            this.currentSort.prop = this.tableHeader[i].field;
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
        date(time) {
            return new Date(parseInt(time) * 1000).toLocaleString().replaceAll('/', '-');
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
        },

        // 获取配置信息
        checkConfig(config, attr) {
            return this[config][attr] === undefined ? this[config + 'Inside'][attr] : this[config][attr];
        },

        // 获取配置信息2
        checkConfig2(config, defaultConfig, attr) {
            return config === undefined || config[attr] === undefined ? defaultConfig[attr] : config[attr];
        }
    },
};