<el-form-item prop="{{ $index }}" size="small" class="{{ $data['description']?'has-helptext':'' }} {{ isset($class) ? $class : '' }}">
    <el-tooltip slot="label" popper-class="jc-twig-output" effect="dark" content="{!! $data['tips'] !!}" placement="right">
        <span>{{ $data['label'] }}</span>
    </el-tooltip>
    <el-switch v-model="settings['{{ $index }}']" active-text="开启" inactive-text="关闭"></el-switch>
    @if ($data['description'])
    <span class="jc-form-item-help"><i class="el-icon-info"></i> {{ $data['description'] }}</span>
    @endif
</el-form-item>