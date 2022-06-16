@extends('layout')

@section('h1', '内容回收站')

@section('main_content')
<div id="main_tools">
    <div class="jc-btn-group">
        <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" :disabled="!selected.length" @click.stop="recovery">
            <div class="md-ripple"><div class="md-button-content">恢复数据</div></div>
        </button>
        <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" :disabled="!selected.length" @click.stop="destroy">
            <div class="md-ripple"><div class="md-button-content">删除数据</div></div>
        </button>
    </div>
    <div class="jc-options">
        <div class="jc-option" id="contents_filter">
            <label>过滤：</label>
            <el-select v-model="filterBy" size="small" class="jc-filterby" @change="handleFilterByChange">
                <el-option label="-- 显示全部 --" value=""></el-option>
                <el-option label="按标题" value="title"></el-option>
                <el-option label="按类型" value="mold"></el-option>
                <el-option label="按网址" value="url"></el-option>
                <el-option label="按颜色" value="color"></el-option>
            </el-select>
            <el-input v-if="filterBy=='title'" v-model="filterValues.title" size="small" native-size="20" placeholder="内容标题" @input="filterNodes"></el-input>
            <el-select v-if="filterBy=='mold'" v-model="filterValues.mold" size="small" placeholder="选择内容类型" @change="filterNodes">
                <el-option v-for="(label, id) in molds" :key="id" :label="label" :value="id">
                </el-option>
            </el-select>
            <el-select v-if="filterBy=='url'" v-model="filterValues.url" size="small" @change="filterNodes">
                <el-option label="有网址" :value="true"></el-option>
                <el-option label="没有网址" :value="false"></el-option>
            </el-select>
            <el-select size="small" v-if="filterBy=='color'" v-model="filterValues.color" @change="filterNodes">
                <el-option value="is_red">红</el-option>
                <el-option value="is_green">绿</el-option>
                <el-option value="is_blue">蓝</el-option>
            </el-select>
        </div>
    </div>
</div>
<div id="main_list">
    <div class="jc-table-wrapper">
        <el-table class="jc-table with-operators" :data="nodes" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="50"></el-table-column>
            <el-table-column label="ID" prop="id" width="100" sortable></el-table-column>
            <el-table-column label="标题" prop="title" width="auto" sortable>
                <template slot-scope="scope">
                    <span>@{{ scope.row.title }}</span>
                </template>
            </el-table-column>
            <el-table-column label="类型" prop="mold_id" width="120" sortable>
                <template slot-scope="scope">
                    <span>@{{ molds[scope.row.mold_id] }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="200">
                <template slot-scope="scope">
                    <div class="jc-operators">
                        <button type="button" title="恢复" class="md-button md-fab md-dense md-primary md-theme-default" @click.stop="recovery(scope.row)">
                            <div class="md-ripple" style="background: #448aff;"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">check</i></div></div>
                        </button>
                        <button type="button" title="删除" class="md-button md-fab md-dense md-accent md-theme-default" @click.stop="destroy(scope.row)">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">remove</i></div></div>
                        </button>
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </div>
</div>

<c-pagination ref="pagination" :concise="false" :total="nodesAll.length" :per-page="perPage" @per-page-change="perPage = $event" @current-change="currentPage = $event"></c-pagination>

@endsection

@section('script')
<script src="/themes/backend/vue/js/c-pagination.js"></script>
<link rel="stylesheet" type="text/css" href="/themes/backend/vue/css/c-pagination.css">
<script>
    Vue.component('c-pagination', cPagination)

    let app = new Vue({
        el: '#main_content',

        data() {
            return {
                nodes: [],
                nodesAll: @jjson(array_values($models), JSON_PRETTY_PRINT),
                molds: @jjson($context['molds'], JSON_PRETTY_PRINT),
                selected: [],
                showSuggestedTemplates: false,
                contextmenu: {
                    target: null,
                    url: null,
                    editUrl: null,
                    translateUrl: null,
                },

                filterBy: '',
                filterValues: {
                    title: null,
                    mold: null,
                    url: true,
                    langcode: "{{ langcode('content') }}",
                    color: '',
                },

                currentPage: 1,
                perPage: 50
            };
        },

        created() {
            this.original_models = _.cloneDeep(this.nodesAll);
        },

        mounted() {
            this.to();
        },

        watch: {
            currentPage: function(newVal, oldVal) {
                this.to();
            },
            perPage: function(newVal, oldVal) {
                this.to();
            },
        },

        methods: {
            to() {
                // $('#main_content').scrollTop(0);
                this.nodes = JSON.parse(JSON.stringify(this.nodesAll)).splice((this.currentPage-1)*this.perPage, this.perPage);
            },

            recovery(data) {
                var nodes = [];
                if (data.id) {
                    nodes.push(data.id);
                } else {
                    this.selected.forEach(element => {
                        nodes.push(element.id);
                    });
                }

                if (nodes.length == 0) return false;

                const loading = app.$loading({
                    lock: true,
                    text: '正在恢复 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });
                axios.post("{{ short_url('nodes.recovery_recovery_data',) }}", { nodes: nodes }).then(function(response) {
                    loading.spinner = 'el-icon-success';
                    loading.text = '已恢复';
                    window.location.reload();
                }).catch(function(error) {
                    console.error(error);
                });
            },

            destroy(data) {
                var nodes = [];
                if (data.id) {
                    nodes.push(data.id);
                } else {
                    this.selected.forEach(element => {
                        nodes.push(element.id);
                    });
                }

                if (nodes.length == 0) return false;

                this.$confirm(`确定要永久删除内容？`, '删除内容', {
                    confirmButtonText: '删除',
                    cancelButtonText: '取消',
                    type: 'warning',
                }).then(() => {
                    const loading = app.$loading({
                        lock: true,
                        text: '正在删除 ...',
                        background: 'rgba(255, 255, 255, 0.7)',
                    });
                    axios.post("{{ short_url('nodes.recovery_delete_data',) }}", { nodes: nodes }).then(function(response) {
                        loading.spinner = 'el-icon-success';
                        loading.text = '已删除';
                        window.location.reload();
                    }).catch(function(error) {
                        console.error(error);
                    });
                }).catch(()=>{});
            },

            handleSelectionChange(selected) {
                this.$set(this.$data, 'selected', selected);
            },

            handleFilterByChange(value) {
                if (value === 'url') {
                    this.filterValues.url = true;
                    this.$set(this.$data, 'nodesAll', this.filterByUrl(true));
                    this.currentPage = 1;
                    this.$refs.pagination.reset();
                    this.to();
                } else {
                    if (value) {
                        this.filterValues[value] = null;
                    }
                    this.$set(this.$data, 'nodesAll', _.cloneDeep(this.original_models));
                    this.currentPage = 1;
                    this.$refs.pagination.reset();
                    this.to();
                }
            },

            filterNodes(value) {
                let nodes = null;
                switch (this.filterBy) {
                    case 'title':
                    nodes = this.filterByTitle(value);
                    break;
                    case 'mold':
                    nodes = this.filterByMold(value);
                    break;
                    case 'url':
                    nodes = this.filterByUrl(value);
                    break;
                    case 'color':
                    nodes = this.filterByColor(value);
                    break;
                    case 'langcode':
                    nodes = this.filterByLangcode(value);
                    break;
                }
                this.$set(this.$data, 'nodesAll', nodes || _.cloneDeep(this.original_models));
                this.currentPage = 1;
                this.$refs.pagination.reset();
                this.to();
            },

            filterByTitle(value) {
                if (!value || !value.trim()) {
                    return _.cloneDeep(this.original_models);
                }

                const nodes = [];
                value = value.trim().toLowerCase();
                this.original_models.forEach(node => {
                    if (node.title.toLowerCase().indexOf(value) >= 0) {
                        nodes.push(clone(node));
                    }
                });

                return nodes;
            },

            filterByMold(value) {
                if (!value) {
                    return _.cloneDeep(this.original_models);
                }

                const nodes = [];
                this.original_models.forEach(node => {
                    if (node.mold_id === value) {
                        nodes.push(clone(node));
                    }
                });

                return nodes;
            },

            filterByUrl(value) {
                const nodes = [];
                this.original_models.forEach(node => {
                    if ((value && node.url) || (!value && !node.url)) {
                        nodes.push(clone(node));
                    }
                });

                return nodes;
            },

            filterByColor(value) {
                const nodes = [];
                this.original_models.forEach(node => {
                    if (node[value]) {
                        nodes.push(clone(node));
                    }
                });

                return nodes;
            },

            filterByLangcode(langcode) {
                if (!value) {
                    return _.cloneDeep(this.original_models);
                }

                const nodes = [];
                this.original_models.forEach(node => {
                    if (node.langcode === langcode) {
                        nodes.push(clone(node));
                    }
                });

                return nodes;
            }
        },
    });
</script>
@endsection
