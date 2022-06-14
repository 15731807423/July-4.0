<el-form-item v-if="{{ $_model }}.table_status" label="{{ $_label ?? '默认排序方式' }}" size="small" class="has-helptext">
    <el-radio-group v-model="{{ $_model }}.default_sort_mode">
        <el-radio label="asc">正序</el-radio>
        <el-radio label="desc">倒序</el-radio>
    </el-radio-group>
</el-form-item>
