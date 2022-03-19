
{{-- timeout 类型字段 --}}
<el-form-item prop="{{ $id }}" size="small" class="{{ isset($helptext) ? 'has-helptext' : '' }}" :rules="[{!! implode(',', $rules) !!}]">
    <el-tooltip slot="label" content="{{ $id }}" placement="right" effect="dark" popper-class="jc-twig-output">
        <span>{{ $label }}</span>
    </el-tooltip>
    <el-date-picker v-model="model.{{ $id }}" type="datetime" placeholder="选择日期时间" editable="false" value-format="timestamp" style="width: 100%;"></el-date-picker>
    @if ($helptext ?? false)
    <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $helptext }}</span>
    @endif
</el-form-item>