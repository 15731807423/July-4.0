<el-form-item label="{{ $_label ?? '展示列表' }}" size="small" class="has-helptext">
    <el-switch v-model="{{ $_model }}.list_status" active-text="开启" inactive-text="关闭"></el-switch>
</el-form-item>
