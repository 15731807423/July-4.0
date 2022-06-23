<?php

namespace Specs;

class Twig
{
    private static $searchInput = '<el-input v-model="keywords" :class="searchInside.inputConfig.class" :type="searchInside.inputConfig.type" :maxlength="searchInside.inputConfig.maxlength" :minlength="searchInside.inputConfig.minlength" :show-word-limit="searchInside.inputConfig.showWordLimit" :placeholder="searchInside.inputConfig.placeholder" :clearable="searchInside.inputConfig.clearable" :show-password="searchInside.inputConfig.showPassword" :disabled="searchInside.inputConfig.disabled" :size="searchInside.inputConfig.size" :prefix-icon="searchInside.inputConfig.prefixIcon" :suffix-icon="searchInside.inputConfig.suffixIcon" :rows="searchInside.inputConfig.rows" :autosize="searchInside.inputConfig.autosize" :autocomplete="searchInside.inputConfig.autocomplete" :name="searchInside.inputConfig.name" :readonly="searchInside.inputConfig.readonly" :max="searchInside.inputConfig.max" :min="searchInside.inputConfig.min" :step="searchInside.inputConfig.step" :resize="searchInside.inputConfig.resize" :autofocus="searchInside.inputConfig.autofocus" :form="searchInside.inputConfig.form" :label="searchInside.inputConfig.label" :tabindex="searchInside.inputConfig.tabindex" :validate-event="searchInside.inputConfig.validateEvent" :input-style="searchInside.inputConfig.inputStyle" @input="handleData(-1, \'searchInput\')" @change="handleData(-1, \'searchChange\')"></el-input> ';

    private static $searchButton = '<el-button :class="searchInside.buttonConfig.class" :size="searchInside.buttonConfig.size" :type="searchInside.buttonConfig.type" :plain="searchInside.buttonConfig.plain" :round="searchInside.buttonConfig.round" :circle="searchInside.buttonConfig.circle" :loading="searchInside.buttonConfig.loading" :loading-icon="searchInside.buttonConfig.loadingIcon" :disabled="searchInside.buttonConfig.disabled" :icon="searchInside.buttonConfig.icon" :autofocus="searchInside.buttonConfig.autofocus" :native-type="searchInside.buttonConfig.nativeType" :auto-insert-space="searchInside.buttonConfig.autoInsertSpace" @click="handleData">{{ searchInside.buttonConfig.text }}</el-button> ';

    private static $screenUser = '<template v-if="userSelectedList.length > 0"><span v-for="item in userSelectedList" @click="screenClear(item.field, item.value)">{{ item.value }}</span><span @click="screenClear()">{{ screenInside.clearText }}</span></template>';

    private static $screen = '<div v-for="(item, key) in screenAll"><span>{{ item.name }}</span><el-radio-group v-if="item.type == 1" v-model="screenAllSelected[key].value" :size="item.configGroup.size" :disabled="item.configGroup.disabled" :text-color="item.configGroup.textColor" :fill="item.configGroup.fill" @change="screenChange(key)"><template v-for="it in item.list"><el-radio-button v-if="(it.count > 0 || !screenInside.nullHidden) && item.config.button" :label="it.name" :border="item.config.border" :size="item.config.size" :name="item.config.name">{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(1) != -1 ? \'(\' + it.count + \')\' : \'\' }}</el-radio-button><el-radio v-if="(it.count > 0 || !screenInside.nullHidden) && !item.config.button" :label="it.name" :border="item.config.border" :size="item.config.size" :name="item.config.name">{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(1) != -1 ? \'(\' + it.count + \')\' : \'\' }}</el-radio></template></el-radio-group><el-checkbox-group v-if="item.type == 2" v-model="screenAllSelected[key].value" :size="item.configGroup.size" :disabled="item.configGroup.disabled" :min="item.configGroup.min" :max="item.configGroup.max" :text-color="item.configGroup.textColor" :fill="item.configGroup.fill" @change="screenChange(key)"><template v-for="it in item.list"><el-checkbox-button v-if="(it.count > 0 || !screenInside.nullHidden) && item.config.button" :label="it.name" :border="item.config.border" :size="item.config.size" :name="item.config.name" :indeterminate="item.config.indeterminate">{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(2) != -1 ? \'(\' + it.count + \')\' : \'\' }}</el-checkbox-button><el-checkbox v-if="(it.count > 0 || !screenInside.nullHidden) && !item.config.button" :label="it.name" :border="item.config.border" :size="item.config.size" :name="item.config.name" :indeterminate="item.config.indeterminate">{{ it.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(2) != -1 ? \'(\' + it.count + \')\' : \'\' }}</el-checkbox></template></el-checkbox-group><el-slider v-if="item.type == 3" v-model="screenAllSelected[key].value" :min="item.config.min" :max="item.config.max" :disabled="item.config.disabled" :step="item.config.step" :show-input="item.config.showInput" :show-input-controls="item.config.showInputControls" :size="item.config.size" :input-size="item.config.inputSize" :show-stops="item.config.showStops" :show-tooltip="item.config.showTooltip" :format-tooltip="item.config.formatTooltip" :range="item.config.range" :vertical="item.config.vertical" :height="item.config.height" :label="item.config.label" :debounce="item.config.debounce" :tooltip-class="item.config.tooltipClass" :marks="item.config.marks" @change="screenChange(key)"></el-slider><el-date-picker v-if="item.type == 4" v-model="screenAllSelected[key].value" value-format="X" :type="item.config.type" :disabled="item.config.disabled" :editable="item.config.editable" :clearable="item.config.clearable" :size="item.config.size" :placeholder="item.config.placeholder" :start-placeholder="item.config.startPlaceholder" :end-placeholder="item.config.endPlaceholder" :time-arrow-control="item.config.timeArrowControl" :format="item.config.format" :popper-class="item.config.popperClass" :range-separator="item.config.rangeSeparator" :default-value="item.config.defaultValue" :default-time="item.config.defaultTime" :id="item.config.id" :name="item.config.name" :unlink-panels="item.config.unlinkPanels" :prefix-icon="item.config.prefixIcon" :clear-icon="item.config.clearIcon" :shortcuts="item.config.shortcuts" :disabledDate="item.config.disabledDate" :cellClassName="item.config.cellClassName" :teleported="item.config.teleported" @change="screenChange(key)"></el-date-picker><el-select v-if="item.type == 5" v-model="screenAllSelected[key].value" class="m-2" :multiple="item.config.multiple" :disabled="item.config.disabled" :value-key="item.config.valueKey" :size="item.config.size" :clearable="item.config.clearable" :collapse-tags="item.config.collapseTags" :collapse-tags-tooltip="item.config.collapseTagsTooltip" :multiple-limit="item.config.multipleLimit" :name="item.config.name" :effect="item.config.effect" :autocomplete="item.config.autocomplete" :placeholder="item.config.placeholder" :filterable="item.config.filterable" :allow-create="item.config.allowCreate" :filter-method="item.config.filterMethod" :remote="item.config.remote" :remote-method="item.config.remoteMethod" :loading="item.config.loading" :loading-text="item.config.loadingText" :no-match-text="item.config.noMatchText" :no-data-text="item.config.noDataText" :popper-class="item.config.popperClass" :reserve-keyword="item.config.reserveKeyword" :default-first-option="item.config.defaultFirstOption" :teleported="item.config.teleported" :persistent="item.config.persistent" :automatic-dropdown="item.config.automaticDropdown" :clear-icon="item.config.clearIcon" :fit-input-width="item.config.fitInputWidth" :suffix-icon="item.config.suffixIcon" :tag-type="item.config.tagType" @change="screenChange(key)"><el-option v-for="item in screenAll[key].list" :key="item.name" :label="item.name" :value="item.name">{{ item.name }}{{ screenInside.countStatus && screenInside.groupCountType.indexOf(5) != -1 ? \'(\' + item.count + \')\' : \'\' }}</el-option></el-select></div>';

    private static $selector = '<el-radio-group v-model="selectorInside.value" :size="selectorInside.config.size" :disabled="selectorInside.config.disabled" :text-color="selectorInside.config.textColor" :fill="selectorInside.config.fill"><el-radio-button v-for="(item, key) in selectorInside.list" :label="key" :disabled="item.disabled" :border="item.border" :size="item.size" :name="item.name">{{ item.text }}</el-radio-button></el-radio-group>';

    private static $table = '<el-table v-if="selectorInside.value == \'table\'" :data="currentList" :height="tableInside.config.height" :max-height="tableInside.config.maxHeight" :stripe="tableInside.config.stripe" :border="tableInside.config.border" :size="tableInside.config.size" :fit="tableInside.config.fit" :show-header="tableInside.config.showHeader" :highlight-current-row="tableInside.config.highlightCurrentRow" :current-row-key="tableInside.config.currentRowKey" :row-class-name="tableInside.config.rowClassName" :row-style="tableInside.config.rowStyle" :cell-class-name="tableInside.config.cellClassName" :cell-style="tableInside.config.cellStyle" :header-row-class-name="tableInside.config.headerRowClassName" :header-row-style="tableInside.config.headerRowStyle" :header-cell-class-name="tableInside.config.headerCellClassName" :header-cell-style="tableInside.config.headerCellStyle" :row-key="tableInside.config.rowKey" :empty-text="tableInside.config.emptyText" :default-expand-all="tableInside.config.defaultExpandAll" :expand-row-keys="tableInside.config.expandRowKeys" :tooltip-effect="tableInside.config.tooltipEffect" :show-summary="tableInside.config.showSummary" :sum-text="tableInside.config.sumText" :summary-method="tableInside.config.summaryMethod" :span-method="tableInside.config.spanMethod" :select-on-indeterminate="tableInside.config.selectOnIndeterminate" :indent="tableInside.config.indent" :lazy="tableInside.config.lazy" :load="tableInside.config.load" :tree-props="tableInside.config.treeProps" :table-layout="tableInside.config.tableLayout" :scrollbar-always-on="tableInside.config.scrollbarAlwaysOn" @sort-change="sortChange" style="width: 100%"><el-table-column v-for="item in tableInside.column" :prop="item.field" :label="item.title || item.field" :sortable="item.sortable === true ? \'custom\' : false" :formatter="item.formatter" :type="item.type" :index="item.index" :column-key="item.columnKey" :width="item.width" :min-width="item.minWidth" :fixed="item.fixed" :render-header="item.renderHeader" :sort-method="item.sortMethod" :sort-by="item.sortBy" :sort-orders="item.sortOrders" :resizable="item.resizable" :show-overflow-tooltip="item.showOverflowTooltip" :align="item.align" :header-align="item.headerAlign" :class-name="item.className" :label-class-name="item.labelClassName" :selectable="item.selectable" :reserve-selection="item.reserveSelection" :filters="item.filters" :filter-placement="item.filterPlacement" :filter-multiple="item.filterMultiple" :filter-method="item.filterMethod" :filtered-value="item.filteredValue"></el-table-column></el-table>';

    private static $list = '<template v-if="listItem.length > 0 && selectorInside.value == \'list\'"><p class="empty" v-if="currentList.length == 0">{{ dataEmptyText }}</p><div v-else v-for="item in currentList" :class="listItemClass" v-html="handleHtml(item)"></div></template>';

    private static $page = '<el-pagination v-model:current-page="paginationInside.currentPage" v-model:page-size="paginationInside.pageSize" :small="paginationInside.small" :background="paginationInside.background" :default-page-size="paginationInside.defaultPageSize" :total="paginationInside.total ? paginationInside.total : screenList.length" :page-count="paginationInside.pageCount" :pager-count="paginationInside.pagerCount" :default-current-page="paginationInside.defaultCurrentPage" :layout="paginationInside.layout" :page-sizes="paginationInside.pageSizes" :popper-class="paginationInside.popperClass" :prev-text="paginationInside.prevText" :next-text="paginationInside.nextText" :disabled="paginationInside.disabled" :hide-on-single-page="paginationInside.hideOnSinglePage"></el-pagination>';

    private static $js = '<link rel="stylesheet" type="text/css" href="/themes/frontend/vue/node_modules/element-plus/dist/index.css"><script src="/themes/frontend/vue/node_modules/vue/dist/vue.global.prod.js"></script><script src="/themes/frontend/vue/node_modules/element-plus/dist/index.full.js"></script>';

    public static function searchInput()
    {
        return self::$searchInput;
    }

    public static function searchButton()
    {
        return self::$searchButton;
    }

    public static function screenUser()
    {
        return self::$screenUser;
    }

    public static function screen()
    {
        return self::$screen;
    }

    public static function selector()
    {
        return self::$selector;
    }

    public static function table()
    {
        return self::$table;
    }

    public static function list()
    {
        return self::$list;
    }

    public static function page()
    {
        return self::$page;
    }

    public static function js($specs, array $config = [])
    {
        $js = self::$js . '<script type="text/javascript">' . file_get_contents(frontend_path() . '/js/list-static2.js') . '</script>';
        if (isset($config['id'])) $js = str_replace('{{ id }}', $config['id'], $js);

        $model = new SpecList();

        // 设置后台的配置信息 用传进来的配置信息覆盖后台的配置信息
        $model->setConfig($config);

        // 设置要显示的规格
        $model->specs = Spec::all()->map(function(Spec $spec) {
            return $spec->getKey();
        })->all();

        // 显示多个规格时使用的配置
        $model->attrSpec = Spec::find($model->config['specAll']['specConfig'] ?: $model->specs[0]);

        if (is_string($specs) && in_array($specs, $model->specs)) {
            $model->specs = [$specs];
        }

        // 如果是数组 获取数组里合法的规格名字（取交集）
        elseif (is_array($specs)) {
            $model->specs = array_intersect($specs, $model->specs) ?: $model->specs;
        }

        if (count($model->specs) == 1) {
            $model->attrSpec = Spec::find($model->specs[0]);
        }

        switch ($model->config['model']) {
            case 'static':
                $data = $model->staticSpec(true);
                break;

            case 'dynamic':
                $data = $model->dynamicSpec(true);
                break;
            
            default:
                return '';
                break;
        }

        $js = str_replace('{{ list }}', json_encode($data['list'], JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE), $js);
        $js = str_replace('{{ config }}', json_encode($data['config'], JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE), $js);
        $js = str_replace('{{ table }}', json_encode($data['table'], JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE), $js);
        $js = str_replace('{{ listItem }}', json_encode($data['listItem'], JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE), $js);

        return $js;
    }
}
