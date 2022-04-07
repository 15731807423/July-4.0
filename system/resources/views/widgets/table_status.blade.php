<el-form-item label="{{ $_label ?? '展示表格' }}" prop="id" size="small" class="has-helptext">
    <el-switch v-model="{{ $_model }}.table_status" active-text="开启" inactive-text="关闭"></el-switch>
</el-form-item>
