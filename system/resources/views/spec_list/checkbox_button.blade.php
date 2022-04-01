<el-form-item prop="{{ $index }}" size="small" class="{{ $data['description']?'has-helptext':'' }} {{ isset($class) ? $class : '' }}">
    <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $data['tips'] !!}" placement="right">
        <span>{{ $data['label'] }}</span>
    </el-tooltip>
    <el-checkbox-group v-model="settings['{{ $index }}']">
        @foreach ($list as $key => $value)
            @if (isset($value['disabled']) && $value['disabled'])
                <el-checkbox-button label="{{ $value['label'] }}" :disabled="true">{{ $value['text'] }}</el-checkbox-button>
            @else
                <el-checkbox-button label="{{ $value['label'] }}" :disabled="false">{{ $value['text'] }}</el-checkbox-button>
            @endif
        @endforeach
    </el-checkbox-group>
    @if ($data['description'])
        <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $data['description'] }}</span>
    @endif
</el-form-item>