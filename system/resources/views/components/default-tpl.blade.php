@props([
  'name' => 'default_tpl',
  'label' => '默认模板',
  'size' => 60,
  'readOnly' => false,
  'helpertext' => '创建内容时默认选择的模板',
  'model' => 'model',
])

<el-form-item label="{{ $label }}" prop="{{ $name }}" size="small" class="has-helptext">
  <el-input
    v-model="{{ $model }}.{{ $name }}"
    name="{{ $name }}"
    native-size="{{ $size }}"></el-input>
    <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $helpertext }}</span>
</el-form-item>
