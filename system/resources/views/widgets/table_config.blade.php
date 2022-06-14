<el-form-item v-if="{{ $_model }}.table_status" label="{{ $_label ?? '表格的配置信息' }}" size="small" class="has-helptext">
    <el-input name="table_config" v-model="{{ $_model }}.table_config" type="textarea" rows="{{ $_rows ?? 5 }}" maxlength="255" show-word-limit></el-input>
</el-form-item>
