// 全部数据
const list = {{ list }};

// 组件的配置信息
const config = {{ config }};

// 表格的配置信息
const table = {{ table }};

// 列表的html
const listItem = {{ listItem }};

var dataPanel = Vue.createApp({
    data() {
        return {
            // 全部数据的集合 对象数组
            list: [],

            // 搜索的配置信息 默认searchInside
            search: {},

            // 筛选的配置信息 默认screenInside
            screen: {},

            // 模式选择器的配置信息 默认selectorInside
            selector: {},

            // 表格的配置信息
            table: {},

            // 列表的布局html
            listItem: '',

            // 分页的配置信息
            pagination: {},

            // 加载的配置信息
            loading: {},

            // 数据里多个数据放在一个属性里时的切割符号 空字符串表示不存在多个数据 暂不支持多个切割符号
            cuttingSymbol: '',

            // 数据为空时的提示
            dataEmptyText: '',

            // 排序大小写敏感
            sortCaseSensitive: false,

            // 一些元素的class
            className: 'data',
            contentClass: 'data-content',
            dataClass: 'data-data',
            listClass: 'data-list',
            listItemClass: 'data-list-item',

            // 搜索和筛选后的数据的集合
            screenList: [],

            // 搜索的关键词
            keywords: '',

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
                    emptyText: ''
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

    mounted() {
        this.init();
    },

    computed: {
        // 获取当前页数据
        currentList() {
            return this.assign(this.screenList.slice((this.paginationInside.currentPage - 1) * this.paginationInside.pageSize, this.paginationInside.currentPage * this.paginationInside.pageSize));
        },

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
        init() {
            this.list = list;
            this.search = config.search;
            this.screen = config.screen;
            this.selector = config.selector;
            this.table = table;
            this.listItem = listItem;
            this.pagination = config.pagination;
            this.loading = config.loading;
            this.cuttingSymbol = config.cuttingSymbol || '';
            this.dataEmptyText = config.dataEmptyText || 'No Data';
            this.sortCaseSensitive = config.sortCaseSensitive;

            this.keywords = this.search.default ? this.search.default : '';
            this.tableInside.config.emptyText = this.dataEmptyText;

            // 处理组件配置
            this.handleComponentConfig();

            // 处理传进来的默认排序
            this.handleDefaultSort();

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

            this.defaultSort = this.currentSort;
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
                        let max = this.screenInside.list[i].config.max === undefined ? 100 : this.screenInside.list[i].config.max,
                            min = this.screenInside.list[i].config.min === undefined ? 0 : this.screenInside.list[i].config.min;
                        this.screenRegister(this.screenInside.list[i], [], this.screenInside.list[i].default !== null ? this.screenInside.list[i].default : [min, max], [min, max]);
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

            list = this.sort(list, 'name', 'asc', 2);

            if (data.type == 1) list.unshift({ name: 'All', count: 0 });

            var config = data.config, configGroup = data.configGroup;

            if (data.type == 1 || data.type == 2) {
                this.screenAllSelected[data.field] = { type: data.type, value: defaultValue, config: config };
                this.screenAll[data.field] = { name: data.name, label: data.field, list: list, type: data.type, default: defaultValue, config: config, configGroup: configGroup, all: all };
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
                    list.unshift({ name: 'All', count: 0 });
                }
            }

            this.screenAllSelected[data.field] = { type: data.type, value: defaultValue, config: config };
            this.screenAll[data.field] = { name: data.name, label: data.field, list: list, type: data.type, default: defaultValue, config: config, all: all };
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

            if (!this.searchInside.status || this.keywords.length == 0) {
                this.screenList = list;
                return false;
            }

            var screenList = [], keywords = this.searchInside.caseSensitive ? this.keywords : this.keywords.toLowerCase();
            for (var i = 0; i < list.length; i++) {
                let searchValue = [];

                for (var j = 0; j < this.searchInside.field.length; j++) {
                    if (list[i][this.searchInside.field[j]] === undefined) continue;

                    let value = this.searchInside.caseSensitive
                    ? list[i][this.searchInside.field[j]].toString()
                    : list[i][this.searchInside.field[j]].toString().toLowerCase();

                    if (this.cuttingSymbol.length == 0) {
                        searchValue.push(value);
                    } else {
                        value = value.split(this.cuttingSymbol);
                        searchValue = this.merge(searchValue, value);
                    }
                }

                for (var j = 0; j < searchValue.length; j++) {
                    if (searchValue[j].indexOf(keywords) !== -1) {
                        screenList.push(list[i]);
                        break;
                    }
                }
            }

            this.screenList = screenList;
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
            // 显示数值或隐藏空都需要计算 否则不计算
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
                if (this.list[j][attr] === undefined) continue;

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
                if (data[key] === undefined) continue;

                if (condition[key].type == 1) {
                    let value = this.cuttingSymbol.length == 0 ? [data[key]] : data[key].split(this.cuttingSymbol);
                    if (condition[key].value !== 'All' && value.indexOf(condition[key].value) === -1) return false;
                }
                if (condition[key].type == 2) {
                    let value = this.cuttingSymbol.length == 0 ? [data[key]] : data[key].split(this.cuttingSymbol);
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
                    if (condition[key].config.type == 'dates') {
                        var status = false;
                        for (var i = 0; i < date.length; i++) {
                            if (value >= date[i][0] && value <= date[i][1]) status = true;
                        }
                        return status;
                    } else {
                        if (value < date[0] || value > date[1]) return false;
                    }
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

        // 根据筛选结果的类型返回重置结果
        screenReset(name) {
            return { type: this.screenAll[name].type, value: this.screenAll[name].all, config: this.screenAll[name].config };
        },

        // 排序时对整个筛选后的数组排序，默认某个字段正序
        sortChange(data) {
            if (data.order === null) data = this.defaultSort;

            this.currentSort = data;

            var number = true;
            for (var i = 0; i < this.list.length; i++) {
                if (isNaN(Number(this.list[i][data.prop]))) {
                    number = false;
                    break;
                }
            }

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
                    if (a[field] === undefined && b[field] === undefined) {
                        return 0;
                    } else if (a[field] === undefined) {
                        return 1;
                    } else if (b[field] === undefined) {
                        return -1;
                    }

                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();

                    return _this.sortFunc(a, b, 2);
                }
            }
            if (type == 2 && order == 'desc') {
                var func = function (a, b) {
                    if (a[field] === undefined && b[field] === undefined) {
                        return 0;
                    } else if (a[field] === undefined) {
                        return -1;
                    } else if (b[field] === undefined) {
                        return 1;
                    }

                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();

                    return _this.sortFunc(b, a, 2);
                }
            }
            if (type == 3 && order == 'asc') {
                var func = function (a, b) {
                    if (a[field] === undefined && b[field] === undefined) {
                        return 0;
                    } else if (a[field] === undefined) {
                        return 1;
                    } else if (b[field] === undefined) {
                        return -1;
                    }

                    a = _this.sortCaseSensitive ? a[field] : a[field].toLowerCase();
                    b = _this.sortCaseSensitive ? b[field] : b[field].toLowerCase();

                    return a.localeCompare(b, 'zh');
                }
            }
            if (type == 3 && order == 'desc') {
                var func = function (a, b) {
                    if (a[field] === undefined && b[field] === undefined) {
                        return 0;
                    } else if (a[field] === undefined) {
                        return -1;
                    } else if (b[field] === undefined) {
                        return 1;
                    }

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

        // 筛选函数
        sortFunc(a, b, n, i = 0) {
            if (n == 1) return a - b;

            if (n == 2) {
                if (isNaN(a.charCodeAt(i)) && isNaN(b.charCodeAt(i))) return 0;
                if (isNaN(a.charCodeAt(i))) return -1;
                if (isNaN(b.charCodeAt(i))) return 1;

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

        // 合并数组
        merge(a, b) {
            a.push.apply(a, b);
            return a;
        },

        // 数组去重
        unique(a) {
            return [...new Set(a)];
        }
    }
})
.use(ElementPlus.ElInput)
.use(ElementPlus.ElButton)
.use(ElementPlus.ElRadio)
.use(ElementPlus.ElCheckbox)
.use(ElementPlus.ElSlider)
.use(ElementPlus.ElDatePicker)
.use(ElementPlus.ElSelect)
.use(ElementPlus.ElTable)
.use(ElementPlus.ElLoading)
.use(ElementPlus.ElPagination)
.mount('#{{ id }}');