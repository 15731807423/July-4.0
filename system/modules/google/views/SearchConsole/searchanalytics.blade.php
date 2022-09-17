@extends('layout')

@section('h1', '所有内容')

@section('main_content')

<div class="console">
    <div class="screen">
        <div>
            <label>类型：</label>

            <el-select v-model="type" size="small" class="jc-filterby" @change="query">
                <el-option label="网络" value="web"></el-option>
                <el-option label="图片" value="image"></el-option>
                <el-option label="视频" value="video"></el-option>
                <el-option label="新闻" value="news"></el-option>
            </el-select>
        </div>

        <div>
            <label>关键词：</label>

            <el-input v-model="queryScreen" size="small" native-size="20" placeholder="关键词" @change="query"></el-input>
        </div>

        <div>
            <label>网址：</label>

            <el-input v-model="pageScreen" size="small" native-size="20" placeholder="/index.html" @change="query"></el-input>
        </div>

        <div>
            <label>国家/地区：</label>

            <el-select v-model="countryScreen" size="small" :clearable="true" class="jc-filterby" @change="query">
                <el-option v-for="(item, key) in countryAll" :label="item" :value="key"></el-option>
            </el-select>
        </div>

        <div>
            <label>设备：</label>

            <el-select v-model="deviceScreen" size="small" :clearable="true" class="jc-filterby" @change="query">
                <el-option v-for="(item, key) in deviceAll" :label="item" :value="key"></el-option>
            </el-select>
        </div>

        <div>
            <label>搜索结果呈现：</label>

            <el-select v-model="searchAppearanceScreen" size="small" :clearable="true" class="jc-filterby" @change="query">
                <el-option v-for="(item, key) in searchAppearanceAll" :label="item" :value="key"></el-option>
            </el-select>
        </div>

        <div>
            <label>日期：</label>

            <el-select v-model="date" size="small" class="jc-filterby" @change="query" style="margin-right: 10px;">
                <el-option label="过去7天" value="7"></el-option>
                <el-option label="过去30天" value="30"></el-option>
                <el-option label="过去3个月" value="3"></el-option>
                <el-option label="过去6个月" value="6"></el-option>
                <el-option label="自定义" value="0"></el-option>
            </el-select>

            <el-date-picker v-if="date == 0" size="small" v-model="customize" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" @change="query"></el-date-picker>
        </div>
    </div>

    <div class="chart">
        <p>
            <span>总点击次数：@{{ count.clicks }}</span>
            <span>总展示次数：@{{ count.impressions }}</span>
            <span>平均点击率：@{{ count.ctr }}</span>
            <span>平均排名：@{{ count.position }}</span>
        </p>
        <div id="base"></div>
    </div>

    <div class="list">
        <el-tabs v-model="tabs">
            <el-tab-pane label="查询数" name="query">
                <el-table :data="queryCurrentList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'query')" @sort-change="setSort(arguments, 'query')">
                    <el-table-column prop="keys" label="热门查询" sortable="custom" fixed="left" min-width="600"></el-table-column>
                    <el-table-column v-if="show['点击次数']" prop="clicks" label="点击次数" sortable="custom"></el-table-column>
                    <el-table-column v-if="show['点击率']" prop="ctr" label="点击率" sortable="custom">
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column v-if="show['展示次数']" prop="impressions" label="展示次数" sortable="custom"></el-table-column>
                    <el-table-column v-if="show['平均排名']" prop="position" label="平均排名" sortable="custom"></el-table-column>
                </el-table>
                <el-pagination :total="queryList.length" :page-size="queryPerPage" @size-change="queryPerPage = $event" @current-change="queryCurrentPage = $event" layout="prev, pager, next, sizes, jumper, ->, total"></el-pagination>
            </el-tab-pane>
            <el-tab-pane label="网页" name="page">
                <el-table :data="pageCurrentList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'page')" @sort-change="setSort(arguments, 'page')">
                    <el-table-column prop="keys" label="网页" sortable="custom" fixed="left" min-width="600"></el-table-column>
                    <el-table-column prop="clicks" label="点击次数" sortable="custom"></el-table-column>
                    <el-table-column prop="ctr" label="点击率" sortable="custom">
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column prop="impressions" label="展示次数" sortable="custom"></el-table-column>
                    <el-table-column prop="position" label="平均排名" sortable="custom"></el-table-column>
                </el-table>
                <el-pagination :total="pageList.length" :page-size="pagePerPage" @size-change="pagePerPage = $event" @current-change="pageCurrentPage = $event" layout="prev, pager, next, sizes, jumper, ->, total"></el-pagination>
            </el-tab-pane>
            <el-tab-pane label="国家/地区" name="country">
                <el-table :data="countryCurrentList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'country')" @sort-change="setSort(arguments, 'country')">
                    <el-table-column prop="keys" label="国家/地区" sortable="custom" fixed="left" min-width="600"></el-table-column>
                    <el-table-column prop="clicks" label="点击次数" sortable="custom"></el-table-column>
                    <el-table-column prop="ctr" label="点击率" sortable="custom">
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column prop="impressions" label="展示次数" sortable="custom"></el-table-column>
                    <el-table-column prop="position" label="平均排名" sortable="custom"></el-table-column>
                </el-table>
                <el-pagination :total="countryList.length" :page-size="countryPerPage" @size-change="countryPerPage = $event" @current-change="countryCurrentPage = $event" layout="prev, pager, next, sizes, jumper, ->, total"></el-pagination>
            </el-tab-pane>
            <el-tab-pane label="设备" name="device">
                <el-table :data="deviceList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'device')">
                    <el-table-column prop="keys" label="国家/地区" sortable fixed="left" min-width="600"></el-table-column>
                    <el-table-column prop="clicks" label="点击次数" sortable></el-table-column>
                    <el-table-column prop="ctr" label="点击率" sortable>
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column prop="impressions" label="展示次数" sortable></el-table-column>
                    <el-table-column prop="position" label="平均排名" sortable></el-table-column>
                </el-table>
            </el-tab-pane>
            <el-tab-pane label="搜索结果呈现" name="searchAppearance">
                <el-table :data="searchAppearanceList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'searchAppearance')">
                    <el-table-column prop="keys" label="搜索结果呈现" sortable fixed="left" min-width="600"></el-table-column>
                    <el-table-column prop="clicks" label="点击次数" sortable></el-table-column>
                    <el-table-column prop="ctr" label="点击率" sortable>
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column prop="impressions" label="展示次数" sortable></el-table-column>
                    <el-table-column prop="position" label="平均排名" sortable></el-table-column>
                </el-table>
            </el-tab-pane>
            <el-tab-pane label="日期" name="date">
                <el-table :data="dateCurrentList" style="width: 100%;" class="jc-table jc-dense" @row-click="tableScreen(arguments, 'date')" @sort-change="setSort(arguments, 'date')">
                    <el-table-column prop="keys" label="日期" sortable="custom" fixed="left" min-width="600"></el-table-column>
                    <el-table-column prop="clicks" label="点击次数" sortable="custom"></el-table-column>
                    <el-table-column prop="ctr" label="点击率" sortable="custom">
                        <template slot-scope="scope">@{{ scope.row.ctr }}%</template>
                    </el-table-column>
                    <el-table-column prop="impressions" label="展示次数" sortable="custom"></el-table-column>
                    <el-table-column prop="position" label="平均排名" sortable="custom"></el-table-column>
                </el-table>
                <el-pagination :total="dateList.length" :page-size="datePerPage" @size-change="datePerPage = $event" @current-change="dateCurrentPage = $event" layout="prev, pager, next, sizes, jumper, ->, total"></el-pagination>
            </el-tab-pane>
        </el-tabs>
    </div>
</div>

@endsection

@section('script')
<script src="/themes/backend/js/echarts.js"></script>

<script>
    let app = new Vue({
        el: '#main_content',

        data() {
            return {
                url: '{{ $url }}',
                queryUrl: '{{ $query }}',
                countryAll: @json($countryList, JSON_UNESCAPED_UNICODE),
                deviceAll: @json($deviceList, JSON_UNESCAPED_UNICODE),
                searchAppearanceAll: @json($searchAppearanceList, JSON_UNESCAPED_UNICODE),
                startDate: '',
                endDate: '',
                type: 'web',
                date: '7',
                customize: '',
                queryScreen: '',
                pageScreen: '',
                countryScreen: '',
                deviceScreen: '',
                searchAppearanceScreen: '',
                chart: '',
                count: {
                    clicks: 0,
                    ctr: 0,
                    impressions: 0,
                    position: 0
                },
                tabs: 'query',
                queryList: [],
                queryPerPage: 10,
                queryCurrentPage: 1,
                querySort: {
                    prop: '',
                    order: ''
                },
                pageList: [],
                pagePerPage: 10,
                pageCurrentPage: 1,
                pageSort: {
                    prop: '',
                    order: ''
                },
                countryList: [],
                countryPerPage: 10,
                countryCurrentPage: 1,
                countrySort: {
                    prop: '',
                    order: ''
                },
                deviceList: [],
                searchAppearanceList: [],
                dateList: [],
                datePerPage: 10,
                dateCurrentPage: 1,
                dateSort: {
                    prop: '',
                    order: ''
                },
                show: {
                    '点击次数': true,
                    '点击率': true,
                    '展示次数': true,
                    '平均排名': true,
                },
                loading: null,
                loadCount: 0
            };
        },

        mounted() {
            this.query();
        },

        computed: {
            queryCurrentList() {
                var list = this.assign(this.queryList);

                if (this.querySort.order) list = list.sort(this.compare(this.querySort.prop, this.querySort.order));

                return list.slice((this.queryCurrentPage - 1) * this.queryPerPage, this.queryCurrentPage * this.queryPerPage);
            },

            pageCurrentList() {
                var list = this.assign(this.pageList);

                if (this.pageSort.order) list = list.sort(this.compare(this.pageSort.prop, this.pageSort.order));

                return list.slice((this.pageCurrentPage - 1) * this.pagePerPage, this.pageCurrentPage * this.pagePerPage);
            },

            countryCurrentList() {
                var list = this.assign(this.countryList);

                if (this.countrySort.order) list = list.sort(this.compare(this.countrySort.prop, this.countrySort.order));

                return list.slice((this.countryCurrentPage - 1) * this.countryPerPage, this.countryCurrentPage * this.countryPerPage);
            },

            dateCurrentList() {
                var list = this.assign(this.dateList);

                if (this.dateSort.order) list = list.sort(this.compare(this.dateSort.prop, this.dateSort.order));

                return list.slice((this.dateCurrentPage - 1) * this.datePerPage, this.dateCurrentPage * this.datePerPage);
            },
        },

        methods: {
            query() {
                if (this.date == '0' && this.customize == '') return false;

                // const date = this.handleDate(), loading = this.$loading({
                //     lock: true,
                //     text: '正在获取信息 ...',
                //     background: 'rgba(255, 255, 255, 0.7)',
                // });

                const date = this.handleDate();

                var param = {
                    startDate: date[0],
                    endDate: date[1],
                    type: this.type,
                    query: this.queryScreen,
                    page: this.pageScreen,
                    country: this.countryScreen,
                    device: this.deviceScreen,
                    searchAppearance: this.searchAppearanceScreen
                };

                this.send(Object.assign(this.assign(param), {dimensions: 'date'}), 'setBase');
                this.send(this.assign(param), 'setCount');
                this.send(Object.assign(this.assign(param), {dimensions: 'query'}), 'setQuery');
                this.send(Object.assign(this.assign(param), {dimensions: 'page'}), 'setPage');
                this.send(Object.assign(this.assign(param), {dimensions: 'country'}), 'setCountry');
                this.send(Object.assign(this.assign(param), {dimensions: 'device'}), 'setDevice');
                this.send(Object.assign(this.assign(param), {dimensions: 'searchAppearance'}), 'setSearchAppearance');
            },

            send(data, func) {
                if (this.loading === null) {
                    this.loading = this.$loading({
                        lock: true,
                        text: '正在获取信息 ...',
                        background: 'rgba(255, 255, 255, 0.7)',
                    });
                }

                axios.post(this.queryUrl, data).then(response => {
                    if (response.data.status == 1) this[func](response.data.data);
                    this.loadCount++;
                    if (this.loadCount == 7) {
                        this.loadCount = 0;
                        this.loading.close();
                        this.loading = null;
                    }
                }).catch(data => {
                    console.log(data)
                    this.$message.error('发生错误，可查看控制台');
                });
            },

            setSort(data, table) {
                this[table + 'Sort'] = {
                    order: data[0].order,
                    prop: data[0].prop
                };
            },

            tableScreen(data, table) {
                var keys = data[0].keys.replace(this.url, '/');

                if (table == 'date') {
                    var date = this.handleDate();
                    if (this.date != '0' || date[0] != keys && date[1] != keys) {
                        this.date = '0';
                        this.customize = [new Date(keys + ' 00:00:00'), new Date(keys + ' 00:00:00')];
                        this.query();
                    }
                } else {
                    if (this[table + 'Screen'] != keys) {
                        this[table + 'Screen'] = keys;
                        this.query();
                    }
                }
            },

            handleDate() {
                const time = (new Date()).getTime();

                switch (this.date) {
                    case '7':
                        return [this.getDateByTime(time - 86400 * 1000 * 7), this.getDateByTime()];
                        break;

                    case '30':
                        return [this.getDateByTime(time - 86400 * 1000 * 30), this.getDateByTime()];
                        break;

                    case '3':
                        return [this.getDateByTime(time - 86400 * 1000 * 30 * 3), this.getDateByTime()];
                        break;

                    case '6':
                        return [this.getDateByTime(time - 86400 * 1000 * 30 * 6), this.getDateByTime()];
                        break;

                    case '0':
                        console.log(this.customize)
                        var customize = this.assign(this.customize);
                        customize[0] = (new Date(customize[0])).getTime();
                        customize[1] = (new Date(customize[1])).getTime();
                        return [this.getDateByTime(customize[0]), this.getDateByTime(customize[1])];
                        break;
                }
            },

            setBase(data) {
                this.dateList = data.rows || [];

                this.chart = this.chart || echarts.init(document.getElementById('base'));

                var date = [], clicks = [], ctr = [], impressions = [], position = [];
                for (let i = 0; i < data.rows.length; i++) {
                    date.push(data.rows[i].keys)
                    clicks.push(data.rows[i].clicks)
                    ctr.push(data.rows[i].ctr)
                    impressions.push(data.rows[i].impressions)
                    position.push(data.rows[i].position)
                }

                this.chart.setOption({
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        selected: this.show
                    },
                    xAxis: {
                        data: date
                    },
                    yAxis: [
                        {
                            type: 'value',
                            name: '点击次数',
                            min: 0,
                            max: Math.ceil(parseFloat((this.max(clicks) * 1.05).toFixed(1))),
                            show: false
                        },
                        {
                            type: 'value',
                            name: '点击率',
                            min: 0,
                            max: Math.ceil(parseFloat((this.max(ctr) * 1.05).toFixed(1))),
                            show: false
                        },
                        {
                            type: 'value',
                            name: '展示次数',
                            min: 0,
                            max: Math.ceil(parseFloat((this.max(impressions) * 1.05).toFixed(1))),
                            show: false
                        },
                        {
                            type: 'value',
                            name: '平均排名',
                            min: 0,
                            max: Math.ceil(parseFloat((this.max(position) * 1.05).toFixed(1))),
                            show: false
                        }
                    ],
                    series: [
                        {
                            name: '点击次数',
                            type: 'line',
                            data: clicks,
                            yAxisIndex: 0
                        },
                        {
                            name: '点击率',
                            type: 'line',
                            data: ctr,
                            yAxisIndex: 1,
                            tooltip: {
                                valueFormatter: value => value + '%'
                            },
                        },
                        {
                            name: '展示次数',
                            type: 'line',
                            data: impressions,
                            yAxisIndex: 2
                        },
                        {
                            name: '平均排名',
                            type: 'line',
                            data: position,
                            yAxisIndex: 3
                        }
                    ]
                });

                this.chart.on('legendselectchanged', params => {
                    this.show = params.selected;
                    // return false;
                    // this.chart.dispatchAction({
                    //     type: 'legendSelect',
                    //     name: params.name
                    // })
                });
            },

            setCount(data) {
                this.count = {
                    clicks: data.rows[0].clicks,
                    ctr: parseFloat(data.rows[0].ctr.toFixed(1)) + '%',
                    impressions: data.rows[0].impressions,
                    position: parseFloat(data.rows[0].position.toFixed(1))
                };
            },

            setQuery(data) {
                this.queryList = data.rows || [];
            },

            setPage(data) {
                this.pageList = data.rows || [];
            },

            setCountry(data) {
                this.countryList = data.rows || [];
            },

            setDevice(data) {
                this.deviceList = data.rows || [];
            },

            setSearchAppearance(data) {
                this.searchAppearanceList = data.rows || [];
            },

            getDateByTime(time = null) {
                const date = time ? new Date(time) : new Date();
                return date.getFullYear() + '-' + this.fill0(date.getMonth() + 1) + '-' + this.fill0(date.getDate());
            },

            fill0(number) {
                number = parseInt(number);
                return number > 9 ? number : '0' + number;
            },

            max(array) {
                var max = null;
                for (var i = 0; i < array.length; i++) {
                    max = max > parseFloat(array[i]) ? max : parseFloat(array[i]);
                }
                return max;
            },

            assign(a) {
                return JSON.parse(JSON.stringify(a));
            },

            compare(attr, sort) {
                return function (list1, list2) {
                    var value1 = list1[attr], value2 = list2[attr];

                    if (typeof value1 === 'string' && typeof value2 === 'string') {
                        var res = value1.localeCompare(value2, 'zh');
                        return sort === 'ascending' ? res : -res;
                    } else {
                        if (value1 <= value2) {
                            return sort === 'ascending' ? -1 : 1;
                        } else {
                            return sort === 'ascending' ? 1 : -1;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
