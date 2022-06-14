<el-form-item v-if="{{ $_model }}.list_status" label="{{ $_label ?? '列表的布局' }}" size="small" class="has-helptext">
    <el-input name="list_item" v-model="{{ $_model }}.list_item" type="textarea" rows="{{ $_rows ?? 5 }}" maxlength="255" show-word-limit></el-input>
</el-form-item>
