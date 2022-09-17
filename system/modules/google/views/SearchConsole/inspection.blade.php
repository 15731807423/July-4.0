@extends('layout')

@section('h1', '所有内容')

@section('main_content')

<div class="console">
    <div class="submit">
        <p>检查网址</p>
        <div>
            <span>@{{ url }}</span>
            <el-input v-model="website" size="small" native-size="20" placeholder="输入网址" width="300"></el-input>
            <button type="button" class="md-button md-dense md-raised md-primary md-theme-default" @click.stop="submit" :disabled="!website.length">
                <div class="md-ripple"><div class="md-button-content">检查</div></div>
            </button>
        </div>
    </div>

    <div v-if="data" class="result">
        <p><a :href="data.inspectionResultLink" target="_black">链接到 Search Console 网址检查。</a></p>
        <ul>
            <li>
                <span>站点地图</span>
                <div>
                    <span v-if="data.indexStatusResult.sitemap == null">不适用</span>
                    <template v-else>
                        <span v-for="item in data.indexStatusResult.sitemap">@{{ item }}</span>
                    </template>
                </div>
            </li>
            <li>
                <span>引荐来源网页</span>
                <div>
                    <span v-if="data.indexStatusResult.referringUrls == null">无</span>
                    <template v-else>
                        <span v-for="item in data.indexStatusResult.referringUrls">@{{ item }}</span>
                    </template>
                </div>
            </li>
            <li>
                <span>上次抓取时间</span>
                <div>
                    <span>@{{ data.indexStatusResult.lastCrawlTime }}</span>
                </div>
            </li>
            <li>
                <span>当时所用的用户代理</span>
                <div>
                    <span>@{{ data.indexStatusResult.crawledAs }}</span>
                </div>
            </li>
            <li>
                <span>是否允许抓取</span>
                <div>
                    <span>@{{ data.indexStatusResult.robotsTxtState }}</span>
                </div>
            </li>
            <li>
                <span>网页抓取</span>
                <div>
                    <span>@{{ data.indexStatusResult.pageFetchState }}</span>
                </div>
            </li>
            <li>
                <span>是否允许编入索引</span>
                <div>
                    <span>@{{ data.indexStatusResult.verdict }}</span>
                </div>
            </li>
            <li>
                <span>用户声明的规范网址</span>
                <div>
                    <span>@{{ data.indexStatusResult.userCanonical ? data.indexStatusResult.userCanonical : '无' }}</span>
                </div>
            </li>
            <li>
                <span>Google 选择的规范网址</span>
                <div>
                    <span>@{{ data.indexStatusResult.googleCanonical ? data.indexStatusResult.googleCanonical : '无' }}</span>
                </div>
            </li>
            <li>
                <span>移动设备易用性</span>
                <div>
                    <span>@{{ data.mobileUsabilityResult.verdict }}</span>
                </div>
            </li>
            <li v-if="data.ampResult">
                <span>移动设备易用性</span>
                <div>
                    <span>@{{ data.mobileUsabilityResult.verdict }}</span>
                </div>
            </li>
        </ul>
    </div>
</div>

@endsection

@section('script')
<script src="/themes/backend/js/echarts.js"></script>
<script src="/themes/backend/js/c-pagination.js"></script>
<link rel="stylesheet" type="text/css" href="/themes/backend/css/c-pagination.css">

<script>
    Vue.component('c-pagination', cPagination)

    let app = new Vue({
        el: '#main_content',

        data() {
            return {
                queryUrl: '{{ $query }}',
                url: '{{ $url }}',
                website: '',
                data: null
            };
        },

        methods: {
            submit() {
                if (this.website.length == 0) return false;

                const loading = this.$loading({
                    lock: true,
                    text: '正在获取信息 ...',
                    background: 'rgba(255, 255, 255, 0.7)',
                });

                axios.post(this.queryUrl, {
                    website: this.website,
                }).then(response => {
                    loading.close();
                    if (response.data.status == 1) {
                        this.setData(response.data.data);
                    }
                }).catch(data => {
                    console.log(data)
                    this.$message.error('发生错误，可查看控制台');
                });
            },

            setData(data) {
                this.data = data;
            }
        }
    });
</script>
@endsection
