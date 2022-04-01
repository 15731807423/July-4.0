<el-form-item prop="{{ $index }}" size="small" class="{{ $data['description']?'has-helptext':'' }} {{ isset($class) ? $class : '' }}">
    <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $data['tips'] !!}" placement="right">
        <span>{{ $data['label'] }}</span>
    </el-tooltip>
    <el-radio-group v-model="settings['{{ $index }}']">
        @foreach ($list as $key => $value)
            @if (isset($value['disabled']) && $value['disabled'])
                <el-radio-button label="{{ $value['label'] }}" :disabled="true">{{ $value['text'] }}</el-radio-button>
            @else
                <el-radio-button label="{{ $value['label'] }}" :disabled="false">{{ $value['text'] }}</el-radio-button>
            @endif
        @endforeach
    </el-radio-group>
    @if ($data['description'])
        <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $data['description'] }}</span>
    @endif
</el-form-item>