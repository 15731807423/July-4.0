<el-form-item v-if="{{ $_model }}.table_status" label="{{ $_label ?? '默认排序字段' }}" prop="id" size="small" class="has-helptext">
    <el-select v-model="{{ $_model }}.default_sort_field" placeholder="请选择">
        <el-option v-for="field in fields" :key="field.field_id" :label="field.field_id" :value="field.field_id"></el-option>
    </el-select>
</el-form-item>
