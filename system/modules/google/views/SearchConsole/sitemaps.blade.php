@extends('layout')

@section('h1', '所有内容')

@section('main_content')

<div class="console">
    <div class="submit">
        <p>添加新的站点地图</p>
        <div>
            <span>@{{ url }}</span>
            <el-input v-model="newSiteMap" size="small" native-size="20" placeholder="输入站点地图网址"></el-input>
            <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" @click.stop="submit" :disabled="!newSiteMap.length">
                <div class="md-ripple"><div class="md-button-content">提交</div></div>
            </button>
        </div>
    </div>

    <div class="list">
        <p>已提交的站点地图</p>
        <el-table :data="list" style="width: 100%;" class="jc-table jc-dense">
            <el-table-column prop="path" label="站点地图" sortable fixed="left"></el-table-column>
            <el-table-column prop="type" label="类型" sortable></el-table-column>
            <el-table-column prop="errors" label="错误数量" sortable></el-table-column>
            <el-table-column prop="isPending" label="尚未处理" sortable></el-table-column>
            <el-table-column prop="isSitemapsIndex" label="站点地图集合" sortable></el-table-column>
            <el-table-column prop="lastDownloaded" label="最后下载时间" sortable></el-table-column>
            <el-table-column prop="lastSubmitted" label="提交时间" sortable></el-table-column>
            <el-table-column prop="warnings" label="警告数量" sortable></el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <div class="jc-operators">
                        <button type="button" title="删除" class="md-button md-fab md-dense md-accent md-theme-default" @click.stop="remove(scope.row)">
                            <div class="md-ripple"><div class="md-button-content"><i class="md-icon md-icon-font md-theme-default">remove</i></div></div>
                        </button>
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </div>
</div>

@endsection

@section('script')
<script>
    let app = new Vue({
        el: '#main_content',

        data() {
            return {
            	url: '{{ $url }}',
            	submitUrl: '{{ $submit }}',
            	listUrl: '{{ $list }}',
            	deleteUrl: '{{ $delete }}',
            	newSiteMap: '',
            	list: []
            };
        },

        mounted() {
        	this.listSiteMaps();
        },

        methods: {
        	submit() {
        		const loading = this.$loading({
                    lock: true,
                    text: '正在提交站点地图 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

        		axios.post(this.submitUrl, { path: this.newSiteMap }).then(response => {
        			loading.close();
        			response.data.status === 1 ? this.$message.success('已成功提交站点地图') : this.$message.error('提交失败');
        			this.listSiteMaps();
        		}).catch(data => {
        			this.$message.error('发生错误，可查看控制台');
        		});
        	},

        	listSiteMaps() {
				// const loading = this.$loading({
				// 	lock: true,
				// 	text: '正在获取站点地图 ...',
				// 	background: 'rgba(255, 255, 255, 0.7)',
				// });

        		axios.post(this.listUrl).then(response => {
        			// loading.close();

        			if (response.data.status) {
        				this.list = response.data.list;
        			} else {
        				this.list = [];
        				this.$message.error('获取失败');
        			}
        		}).catch(data => {
        			this.$message.error('发生错误，可查看控制台');
        		});
        	},

        	remove(data) {
        		this.$confirm(`确定要删除站点地图吗？`, '删除', {
                    confirmButtonText: '删除',
                    cancelButtonText: '取消',
                    type: 'warning',
                }).then(() => {
					const loading = this.$loading({
	                    lock: true,
	                    text: '正在删除站点地图 ...',
	                    background: 'rgba(255, 255, 255, 0.7)',
	                });

	        		axios.post(this.deleteUrl, { path: data.path }).then(response => {
	        			loading.close();
	        			response.data.status === 1 ? this.$message.success('已成功删除站点地图') : this.$message.error('删除失败');
	        			this.listSiteMaps();
	        		}).catch(data => {
	        			this.$message.error('发生错误，可查看控制台');
	        		});
                }).catch(()=>{});
        	}
        }
    });
</script>
@endsection
