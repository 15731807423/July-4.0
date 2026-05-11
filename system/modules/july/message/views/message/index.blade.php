@extends('layout')

@section('h1', '所有内容')

@section('main_content')
  <div id="main_tools">
    <div class="jc-options">
      <div class="jc-option" id="contents_filter">
        <label>筛选：</label>
        <el-select v-model="filterBy" size="small" class="jc-filterby" @change="handleFilterByChange">
          <el-option label="-- 显示全部 --" value=""></el-option>
          <el-option label="按主题" value="subject"></el-option>
          <el-option label="按类型" value="mold"></el-option>
          @if (config('lang.multiple'))
          <el-option label="按语言" value="langcode"></el-option>
          @endif
        </el-select>
        <el-input
          v-if="filterBy=='subject'"
          v-model="filterValues.subject"
          size="small"
          native-size="20"
          placeholder="消息主题"
          @change="filterModels"
          @keyup.enter.native="filterModels"></el-input>
        <el-select v-if="filterBy=='mold'" v-model="filterValues.mold" size="small" placeholder="选择内容类型" @change="filterModels">
          <el-option
            v-for="(label, id) in molds"
            :key="id"
            :label="label"
            :value="id">
          </el-option>
        </el-select>
        @if (config('lang.multiple'))
        <el-select size="small"
          v-if="filterBy=='langcode'"
          v-model="filterValues.langcode"
          @change="filterModels">
          <el-option v-for="(langname, langcode) in languages" :key="langcode" :value="langcode">@{{ langname }}</el-option>
        </el-select>
        @endif
      </div>
    </div>
    {{-- <div class="jc-translate"></div> --}}
  </div>
  <div id="main_list">
    <div class="jc-table-wrapper">
      <el-table class="jc-table with-operators" :data="models" @row-contextmenu="handleContextmenu">
        <el-table-column label="ID" prop="id" width="100" sortable></el-table-column>
        <el-table-column label="主题" prop="subject" width="auto" sortable>
          <template slot-scope="scope">
            <a :href="getUrl('show', scope.row.id)" target="_blank">@{{ scope.row.subject }}</a>
          </template>
        </el-table-column>
        <el-table-column label="类型" prop="mold_id" width="120" sortable>
          <template slot-scope="scope">
            <span>@{{ molds[scope.row.mold_id] }}</span>
          </template>
        </el-table-column>
        <el-table-column label="发送状态" prop="mold_id" width="200" sortable>
          <template slot-scope="scope">
            <span>@{{ scope.row.is_sent ? '已发送' : '未发送' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="IP地址" prop="mold_id" width="200" sortable>
          <template slot-scope="scope">
            <span>@{{ scope.row.ip }}</span>
          </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="created_at" width="240" sortable>
          <template slot-scope="scope">
            <span>@{{ diffForHumans(scope.row.created_at) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200">
          <template slot-scope="scope">
            <div class="jc-operators">
              <a :href="getUrl('show', scope.row.id)" title="查看" class="md-button md-fab md-dense md-primary md-theme-default">
                <div class="md-ripple">
                  <div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">visibility</i></div>
                </div>
              </a>
              <button type="button" title="删除" class="md-button md-fab md-dense md-accent md-theme-default"
                @click.stop="deleteModel(scope.row)">
                <div class="md-ripple">
                  <div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">remove</i></div>
                </div>
              </button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <jc-contextmenu ref="contextmenu">
      <x-menu-item title="查看" icon="visibility" target="_blank" href="contextmenu.url" />
      <x-menu-item title="删除" icon="remove_circle" theme="md-accent" click="deleteModel(contextmenu.target)" />
    </jc-contextmenu>
  </div>

  <div class="c-pagination">
    @if ($models->onFirstPage())
      <button class="c-pagination__prev" disabled>&lsaquo;</button>
    @else
      <a class="c-pagination__prev" href="{{ $models->previousPageUrl() }}">&lsaquo;</a>
    @endif

    <div class="c-pagination__pagers">
      @foreach (\Illuminate\Pagination\UrlWindow::make($models) as $element)
        @if (is_string($element))
          <div class="c-pagination__pager">{{ $element }}</div>
        @endif
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $models->currentPage())
              <div class="c-pagination__pager is-active">{{ $page }}</div>
            @else
              <a class="c-pagination__pager" href="{{ $url }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach
    </div>

    @if ($models->hasMorePages())
      <a class="c-pagination__next" href="{{ $models->nextPageUrl() }}">&rsaquo;</a>
    @else
      <button class="c-pagination__next" disabled>&rsaquo;</button>
    @endif

    <select class="el-input__inner limit" :value="perPage" @change="changePerPage($event.target.value)">
      <option value="15">15条/页</option>
      <option value="30">30条/页</option>
      <option value="50">50条/页</option>
      <option value="100">100条/页</option>
      <option value="200">200条/页</option>
      <option value="300">300条/页</option>
      <option value="500">500条/页</option>
      <option value="1000">1000条/页</option>
    </select>
    <input class="el-input__inner jump" @keyup.enter="jumpPage($event.target.value)" placeholder="输入页码并回车">
    <span style="color: #666;">共{{ $models->total() }}条，每页{{ $models->perPage() }}条，共{{ $models->lastPage() }}页</span>
  </div>
@endsection

@section('script')
<link rel="stylesheet" type="text/css" href="/themes/backend/css/c-pagination.css">
<script>
  let app = new Vue({
    el: '#main_content',

    data() {
      return {
        models: @jjson($models->getCollection()->values()->all()),
        molds: @jjson($context['molds']),
        contextmenu: {
          target: null,
          showUrl: null,
        },

        filterBy: @jjson($context['filters']['filter_by']),
        filterValues: {
          subject: @jjson($context['filters']['subject']),
          mold: @jjson($context['filters']['mold']),
          langcode: @jjson($context['filters']['langcode']),
        },

        // {{-- tags: @json($tags, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), --}}
        languages: @jjson($context['languages']),

        showUrl: "{{ short_url('messages.show', '_ID_') }}",
        deleteUrl: "{{ short_url('messages.destroy', '_ID_') }}",
        currentPage: {{ $models->currentPage() }},
        perPage: {{ $models->perPage() }},
      };
    },

    methods: {
      diffForHumans(time) {
        return moment(time).fromNow();
      },

      getUrl(route, id) {
        switch (route) {
          case 'show':
            return this.showUrl.replace('_ID_', id);
        }
      },

      deleteModel(model) {
        if (! model) return;

        // console.log(this.deleteUrl.replace('_ID_', model.id));
        this.$confirm(`确定要删除内容？`, '删除内容', {
          confirmButtonText: '删除',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          const loading = app.$loading({
            lock: true,
            text: '正在删除 ...',
            background: 'rgba(255, 255, 255, 0.7)',
          });
          axios.delete(this.deleteUrl.replace('_ID_', model.id)).then(function(response) {
            // console.log(response)
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

        // this.targetNode = row;
        const menu = this.contextmenu;
        menu.target = row;
        menu.url = row.url;
        menu.showUrl = this.showUrl.replace('_ID_', row.id);

        this.$refs.contextmenu.show(event, this.$refs.contextmenu.$el);
      },

      handleFilterByChange() {
        this.filterModels();
      },

      filterModels() {
        const url = new URL(window.location.href);

        ['subject', 'mold', 'langcode', 'filter_by', 'page'].forEach(key => url.searchParams.delete(key));
        url.searchParams.set('per_page', this.perPage);

        if (this.filterBy) {
          const value = this.filterValues[this.filterBy];
          url.searchParams.set('filter_by', this.filterBy);
          if (value !== null && value !== '') {
            url.searchParams.set(this.filterBy, value);
          }
        }

        window.location.href = url.toString();
      },

      changePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page');
        window.location.href = url.toString();
      },

      jumpPage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page || 1);
        window.location.href = url.toString();
      },
    },
  });
</script>
@endsection
