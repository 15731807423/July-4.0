<?php

namespace App\Support\Settings;

use Illuminate\Validation\ValidationException;
use JsonException;
use stdClass;

class Translate extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'translate';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '翻译设置';

    /**
     * 配置项
     *
     * @var array
     */
    protected $items = [
        // 页面不再展示翻译平台，但必须保留旧配置，避免保存时丢失该字段。
        'translate.tool' => [
            'key' => 'translate.tool',
            'label' => '翻译平台',
            'description' => '',
        ],

        'translate.mode' => [
            'key' => 'translate.mode',
            'label' => '翻译模式',
            'description' => '任务模式会创建任务并轮询结果，适合批量内容；直接模式会在单次请求中等待并返回结果，内容较多时可能超时。',
        ],

        'translate.code' => [
            'key' => 'translate.code',
            'label' => '代码转换',
            'description' => '网站语言代码与阿里云语言代码不一致时，可在此配置映射关系。',
        ],

        'translate.fields' => [
            'key' => 'translate.fields',
            'label' => '全部不翻译的字段',
            'description' => '',
            'placeholder' => "[\"url\",\"image_src\"]"
        ],

        'translate.text' => [
            'key' => 'translate.text',
            'label' => '全部不翻译的内容',
            'description' => '',
            'placeholder' => "[\"text\",\"name\"]"
        ],

        'translate.replace' => [
            'key' => 'translate.replace',
            'label' => '指定翻译结果',
            'description' => '',
            'placeholder' => '{"de":{"Company":"Unternehmen"}}'
        ],
    ];

    public function save(array $settings)
    {
        $errors = self::jsonValidationErrors($settings);
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        // 页面不再展示平台选项；旧请求未提交该字段时继续保留现有值。
        if (!array_key_exists('translate.tool', $settings)) {
            $settings['translate.tool'] = config('translate.tool', 'alibabacloud');
        }

        parent::save($settings);
    }

    public static function jsonValidationErrors(array $settings): array
    {
        $definitions = [
            'translate.code' => [
                'label' => '代码转换',
                'validator' => 'isCodeMapping',
                'shape' => '应为语言代码映射对象',
            ],
            'translate.fields' => [
                'label' => '全部不翻译的字段',
                'validator' => 'isStringListSetting',
                'shape' => '应为字符串数组，或按语言分组的字符串数组对象',
            ],
            'translate.text' => [
                'label' => '全部不翻译的内容',
                'validator' => 'isStringListSetting',
                'shape' => '应为字符串数组，或按语言分组的字符串数组对象',
            ],
            'translate.replace' => [
                'label' => '指定翻译结果',
                'validator' => 'isReplacementSetting',
                'shape' => '应为原文与译文的映射对象，或按语言分组的映射对象',
            ],
        ];

        $errors = [];
        foreach ($definitions as $key => $definition) {
            $raw = $settings[$key] ?? null;
            if ($raw === null || $raw === '') {
                continue;
            }
            if (!is_string($raw)) {
                $errors[$key] = $definition['label'] . '必须是 JSON 文本';
                continue;
            }
            if (trim($raw) === '') {
                continue;
            }

            try {
                $value = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $errors[$key] = $definition['label'] . '不是合法的 JSON，请检查引号、逗号和括号';
                continue;
            }

            if (!self::{$definition['validator']}($value)) {
                $errors[$key] = $definition['label'] . $definition['shape'];
            }
        }

        return $errors;
    }

    public static function normalizeImportedReplacementJson(string $json, string $source): string
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return $json;
        }

        $prefix = $source . '_to_';
        $normalized = [];
        // 先保留新格式；新旧键同时存在时，以明确的目标语言键为准。
        foreach ($decoded as $key => $value) {
            if (
                !is_string($key)
                || (is_array($value) && str_starts_with($key, $prefix))
            ) {
                continue;
            }
            $normalized[$key] = $value;
        }

        foreach ($decoded as $key => $value) {
            if (
                !is_string($key)
                || !is_array($value)
                || !str_starts_with($key, $prefix)
            ) {
                continue;
            }

            $target = substr($key, strlen($prefix));
            if ($target !== '' && !array_key_exists($target, $normalized)) {
                $normalized[$target] = $value;
            }
        }

        return json_encode(
            $normalized,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: $json;
    }

    public static function replacementMapForCode(
        $setting,
        string $code,
        ?string $source = null,
        ?string $frontend = null
    ): array {
        $decoded = is_string($setting) ? json_decode($setting, true) : $setting;
        if (!is_array($decoded)) {
            return [];
        }

        $containsGroups = count(array_filter($decoded, 'is_array')) > 0;
        if (!$containsGroups) {
            return array_filter($decoded, 'is_string');
        }

        $keys = [$code];
        $sources = array_filter([$source, $frontend], function ($from): bool {
            return is_string($from) && $from !== '';
        });
        foreach (array_unique($sources) as $from) {
            $keys[] = $from . '_to_' . $code;
        }

        foreach ($keys as $key) {
            $map = $decoded[$key] ?? null;
            if (is_array($map)) {
                return array_filter($map, 'is_string');
            }
        }

        return [];
    }

    private static function isStringList($value): bool
    {
        return is_array($value)
            && count($value) === count(array_filter($value, 'is_string'));
    }

    private static function isStringMap($value): bool
    {
        if (!$value instanceof stdClass) {
            return false;
        }

        $values = array_values(get_object_vars($value));

        return count($values) === count(array_filter($values, 'is_string'));
    }

    private static function isCodeMapping($value): bool
    {
        if (is_array($value)) {
            return $value === [];
        }
        if (!$value instanceof stdClass) {
            return false;
        }

        $values = array_values(get_object_vars($value));

        return count($values) === count(array_filter($values, 'is_string'))
            || count($values) === count(array_filter($values, [self::class, 'isStringMap']));
    }

    private static function isStringListSetting($value): bool
    {
        if (self::isStringList($value)) {
            return true;
        }
        if (!$value instanceof stdClass) {
            return false;
        }

        $values = array_values(get_object_vars($value));

        return count($values) === count(array_filter($values, [self::class, 'isStringList']));
    }

    private static function isReplacementSetting($value): bool
    {
        if (is_array($value)) {
            return $value === [];
        }
        if (!$value instanceof stdClass) {
            return false;
        }

        $values = array_values(get_object_vars($value));
        $isFlatMap = count($values) === count(array_filter($values, 'is_string'));
        $isGroupedMap = count($values) === count(array_filter($values, function ($item): bool {
            return self::isStringMap($item) || $item === [];
        }));

        return $isFlatMap || $isGroupedMap;
    }
}
