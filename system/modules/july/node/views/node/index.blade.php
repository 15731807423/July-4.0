 @extends('layout')

@section('h1', '所有内容')

@section('main_content')
<div id="main_tools">
    <div class="jc-btn-group">
        <a href="{{ short_url('nodes.choose_mold') }}" class="md-button md-dense md-raised md-primary md-theme-default">
            <div class="md-ripple"><div class="md-button-content">新建内容</div></div>
        </a>
        <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" :disabled="!selected.length" @click.stop="render()">
            <div class="md-ripple"><div class="md-button-content">生成 HTML</div></div>
        </button>
        <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" @click.stop="render('all')">
            <div class="md-ripple"><div class="md-button-content">全部生成 HTML</div></div>
        </button>
        @if (config('lang.multiple'))
            <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" :disabled="!selected.length" @click.stop="translate">
                <div class="md-ripple"><div class="md-button-content">一键翻译</div></div>
            </button>
        @endif
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
                @if (config('lang.multiple'))
                <el-option label="按语言" value="langcode"></el-option>
                @endif
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
            @if (config('lang.multiple'))
            <el-select size="small" v-if="filterBy=='langcode'" v-model="filterValues.langcode" @change="filterNodes">
                <el-option v-for="(langname, langcode) in languages" :key="langcode" :value="langcode">@{{ langname }}</el-option>
            </el-select>
            @endif
        </div>
        <div class="jc-option">
            <label>显示『建议模板』：</label>
            <el-switch v-model="showSuggestedTemplates"></el-switch>
        </div>
        <div class="jc-option">
            <label for="nodes_view">呈现方式：</label>
            <select id="nodes_view" class="jc-select">
                <option value="" selected>列表</option>
                <optgroup label="------- 目录 -------">
                    @foreach ($context['catalogs'] as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>
    {{-- <div class="jc-translate"></div> --}}
</div>
<div id="main_list">
    <div class="jc-table-wrapper">
        <el-table class="jc-table with-operators" :data="nodes" @row-contextmenu="handleContextmenu" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="50"></el-table-column>
            <el-table-column label="ID" prop="id" width="100" sortable></el-table-column>
            <el-table-column label="标题" prop="title" width="auto" sortable>
                <template slot-scope="scope">
                    <a :href="getUrl('edit', scope.row.id)" target="_blank">@{{ scope.row.title }}</a>
                </template>
            </el-table-column>
            <el-table-column label="颜色" prop="color" width="240">
                <template slot-scope="scope">
                    <el-switch style="margin-right: 1em" @change="changeColor($event, scope.row.id, 'is_red')" v-model="scope.row.is_red" active-color="#F44336" inactive-color="#FFCDD2"></el-switch>
                    <el-switch style="margin-right: 1em" @change="changeColor($event, scope.row.id, 'is_green')" v-model="scope.row.is_green" active-color="#4caf50" inactive-color="#C8E6C9"></el-switch>
                    <el-switch style="margin-right: 1em" @change="changeColor($event, scope.row.id, 'is_blue')" v-model="scope.row.is_blue" active-color="#2196F3" inactive-color="#BBDEFB"></el-switch>
                </template>
            </el-table-column>
            <el-table-column label="建议模板" prop="suggested_templates" width="auto" v-if="showSuggestedTemplates">
                <template slot-scope="scope">
                    <span class="jc-suggested-template" v-for="template in scope.row.suggested_templates" :key="template">@{{ template }}</span>
                </template>
            </el-table-column>
            <el-table-column label="类型" prop="mold_id" width="120" sortable>
                <template slot-scope="scope">
                    <span>@{{ molds[scope.row.mold_id] }}</span>
                </template>
            </el-table-column>
            <el-table-column label="上次修改" prop="updated_at" width="240" sortable>
                <template slot-scope="scope">
                    <span>@{{ diffForHumans(scope.row.updated_at) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="200">
                <template slot-scope="scope">
                    <div class="jc-operators">
                        <a :href="getUrl('edit', scope.row.id)" title="编辑" class="md-button md-fab md-dense md-primary md-theme-default">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">edit</i></div></div>
                        </a>
                        @if (config('lang.multiple'))
                        <a :href="getUrl('translate', scope.row.id)" title="翻译" class="md-button md-fab md-dense md-primary md-theme-default">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">translate</i></div></div>
                        </a>
                        @endif
                        <button type="button" title="删除" class="md-button md-fab md-dense md-accent md-theme-default" @click.stop="deleteNode(scope.row)">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">remove</i></div></div>
                        </button>
                        <a :href="scope.row.url" target="_blank" title="预览页面" class="md-button md-fab md-dense md-primary md-theme-default">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">visibility</i></div></div>
                        </a>
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </div>
    <jc-contextmenu ref="contextmenu">
        <x-menu-item title="编辑" icon="edit" href="contextmenu.editUrl" />
        {{-- @if (config('lang.multiple'))
        <x-menu-item title="翻译" icon="translate" href="contextmenu.translateUrl" />
        @endif --}}
        <x-menu-item title="删除" icon="remove_circle" theme="md-accent" click="deleteNode(contextmenu.target)" />
        <x-menu-item title="生成 HTML" icon="description" click="render(contextmenu.target)" />
        <x-menu-item title="查看页面" icon="visibility" href="contextmenu.url" target="_blank" />
    </jc-contextmenu>
</div>

<c-pagination ref="pagination" :concise="false" :total="nodesAll.length" :per-page="perPage" @per-page-change="perPage = $event" @current-change="currentPage = $event"></c-pagination>

@endsection

@section('script')
<script src="/themes/backend/js/c-pagination.js"></script>
<link rel="stylesheet" type="text/css" href="/themes/backend/css/c-pagination.css">
<script type="text/javascript" src="/themes/backend/js/translate-{{ config('translate.mode') }}.js"></script>
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

                // {{-- tags: @json($tags, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), --}}
                languages: @jjson($context['languages'], JSON_PRETTY_PRINT),

                editUrl: "{{ short_url('nodes.edit', '_ID_') }}",
                deleteUrl: "{{ short_url('nodes.destroy', '_ID_') }}",
                translateUrl: "{{ short_url('nodes.choose_language', '_ID_') }}",
                currentPage: 1,
                perPage: 200,
                langcodes: @jjson(array_keys($context['languages']), JSON_PRETTY_PRINT)
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

            diffForHumans(time) {
                return moment(time).fromNow();
            },

            changeColor(status, id, color) {
                var data = {
                    id: id,
                    [color]: status,
                    langcode: 'en',
                    _changed: [color]
                };
                axios.put("{{ short_url('nodes.update', 'node_id') }}".replace('node_id', id), data).then((response) => {
                    axios.post("{{ short_url('nodes.render') }}", {nodes: [id]}).then((response) => {

                    }).catch(err => {
                        loading.close();
                        this.$message.error('发生错误');
                    });
                }).catch((error) => {
                    loading.close();
                    this.$message.error(error);
                });
            },

            getUrl(route, id) {
                switch (route) {
                    case 'edit':
                    return this.editUrl.replace('_ID_', id);
                    case 'translate':
                    return this.translateUrl.replace('_ID_', id);
                }
            },

            deleteNode(node) {
                if (! node) return;

                this.$confirm(`确定要删除内容？`, '删除内容', {
                    confirmButtonText: '删除',
                    cancelButtonText: '取消',
                    type: 'warning',
                }).then(() => {
                    const loading = app.$loading({
                        lock: true,
                        text: '正在删除 ...',
                        background: 'rgba(255, 255, 255, 0.7)',
                    }), _this = this;
                    axios.delete(this.deleteUrl.replace('_ID_', node.id)).then(function(response) {
                        var data = response.data;
                        if (Array.isArray(data)) {
                            loading.close();
                            _this.$message.error('该内容在' + data.join('、') + '目录下存在子集，无法删除');
                            return false;
                        }

                        loading.spinner = 'el-icon-success';
                        loading.text = '已删除';
                        window.location.reload();
                    }).catch(function(error) {
                        console.error(error);
                    });
                }).catch(()=>{});
            },

            handleContextmenu(row, column, event) {
                if (event.target.tagName==='A' || column.label==='操作') {
                    return;
                }

                const menu = this.contextmenu;
                menu.target = row;
                menu.url = row.url;
                menu.editUrl = this.editUrl.replace('_ID_', row.id);
                menu.translateUrl = this.translateUrl.replace('_ID_', row.id);

                this.$refs.contextmenu.show(event, this.$refs.contextmenu.$el);
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
            },

            translate() {
                var nodes = [];

                this.selected.forEach(element => {
                    nodes.push(element.id);
                });

                translate.frame(this.$loading, this.$message).batch(nodes);
            },

            render(node) {
                const nodes = [];
                if (node === 'all') {
                    this.original_models.forEach(element => {
                        nodes.push(element.id);
                    });
                } else if (node) {
                    nodes.push(node.id);
                } else {
                    this.selected.forEach(element => {
                        nodes.push(element.id);
                    });
                }

                if (! nodes.length) {
                    setTimeout(() => {
                        this.$message.info('未选中任何内容');
                    }, 10)
                    return;
                }

                this.renderRequest(nodes, 0);
            },

            renderRequest(nodes, i) {
                const count = 50, current = nodes.slice(i, i + count);

                if (current.length == 0) {
                    setTimeout(() => {
                        this.$message.success('生成完成');
                    }, 10)
                    return false;
                }

                const loading = this.$loading({
                    lock: true,
                    text: '正在生成HTML ' + current[0] + ' - ' + current[current.length - 1] + ' ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                axios.post("{{ short_url('nodes.render') }}", { nodes: current }).then((response) => {
                    loading.close();
                    this.showRenderError(response.data)
                    this.renderRequest(nodes, i + count);

                }).catch(err => {
                    loading.close();
                    console.error(err);
                    setTimeout(() => {
                        this.$message.error('发生错误');
                    }, 10)
                });
            },

            showRenderError(data) {
                const list = [];

                for (let page in data) {
                    for (let language in data[page]) {
                        if (data[page][language] !== true) {
                            list.push({
                                page: page,
                                language: (language == '_default' ? '默认' : language),
                                file: data[page][language].file,
                                line: data[page][language].line,
                                message: data[page][language].message
                            })
                        }
                    }
                }

                if (list.length) {
                    setTimeout(() => {
                        this.$message.error('发生错误，控制台显示详细信息');
                    }, 10)
                }

                for (var i = 0; i < list.length; i++) {
                    console.log('页面：', list[i].page)
                    console.log('语言：', list[i].language)
                    console.log('文件：', list[i].file)
                    console.log('行号：', list[i].line)
                    console.log('信息：', list[i].message)
                    console.log('------------------------')
                }
            }
        },
    });
</script>
@endsection
