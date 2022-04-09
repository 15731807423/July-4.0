<el-form-item prop="{{ $index }}" size="small" class="{{ $data['description']?'has-helptext':'' }} {{ isset($class) ? $class : '' }}">
    <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $data['tips'] !!}" placement="right">
        <span>{{ $data['label'] }}</span>
    </el-tooltip>
    <el-select v-model="settings['{{ $index }}']">
        @foreach ($list as $key => $value)
            <el-option label="{{ $value }}" value="{{ $value }}">{{ $value }}</el-option>
        @endforeach
    </el-select>
    @if ($data['description'])
        <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $data['description'] }}</span>
    @endif
</el-form-item>