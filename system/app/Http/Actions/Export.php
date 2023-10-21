<?php

namespace App\Http\Actions;

use Illuminate\Http\Request;

use July\Node\NodeField;
use July\Node\NodeType;
use July\Node\Catalog;
use July\Node\Node;

use July\Message\MessageField;
use July\Message\MessageForm;
use July\Message\Message;

use Illuminate\Support\Facades\Storage;

class Export extends ActionBase
{
    protected static $routeName = 'export';

    protected static $title = '导出数据';

    protected static $download = true;

    private $data = [];

    private $field_types = [
        'App\EntityField\FieldTypes\Url' => 'text',
        'App\EntityField\FieldTypes\PathAlias' => 'text',
        'App\EntityField\FieldTypes\Reference' => 'text',
        'App\EntityField\FieldTypes\MultiReference' => 'text',
        'App\EntityField\FieldTypes\Timeout' => 'text',
        'App\EntityField\FieldTypes\Input' => 'text',
        'App\EntityField\FieldTypes\Text' => 'textarea',
        'App\EntityField\FieldTypes\Html' => 'html',
        'App\EntityField\FieldTypes\Image' => 'image',
        'App\EntityField\FieldTypes\File' => 'file',
        'July\Message\FieldTypes\Attachment' => 'attachment',
        'July\Message\FieldTypes\MultipleAttachment' => 'attachments'
    ];

    public function __invoke(Request $request)
    {
        $this->site();
        $this->language();
        $this->page_field();
        $this->page_type();
        $this->mail_field();
        $this->mail_type();
        $this->page();
        $this->catalog();
        $this->mail();
        $this->translate();

        $disk = Storage::disk('public');
        $disk->deleteDirectory('_export');
        $allPages = $pages = Node::indexWith(['url'])->pluck('url')->filter()->toArray();

        foreach (config('lang.available') as $code => $language) {
            if ($code == config('lang.frontend')) {
                continue;
            }

            $allPages = array_merge($allPages, array_map(fn ($page) => '/' . $code . $page, $pages));
        }

        $allPages = array_map(fn ($page) => substr($page, 1), $allPages);

        $files = collect($disk->allFiles('/'))->filter(function ($path) {
            if (stripos($path, '.git/') === 0) return false;
            if (stripos($path, 'system/') === 0) return false;
            if (stripos($path, 'themes/') === 0) return false;
            if (stripos($path, '.editorconfig') === 0) return false;
            if (stripos($path, '.gitignore') === 0) return false;
            if (stripos($path, '.htaccess') === 0) return false;
            if (stripos($path, 'index.php') === 0) return false;
            if (stripos($path, 'README.md') === 0) return false;
            if (stripos($path, 'LICENSE') === 0) return false;
            if (stripos($path, 'time.txt') === 0) return false;

            return true;
        })->toArray();

        $files = array_filter($files, fn ($file) => !in_array($file, $allPages));
        $disk->makeDirectory('_export');

        foreach ($files as $file) {
            $disk->copy($file, '_export/file/' . $file);
        }

        foreach (config('lang.available') as $code => $language) {
            $path = 'themes/frontend/template/' . ($code == config('lang.frontend') ? '' : ($code . '/'));

            foreach ($disk->files($path) as $file) {
                $content = $disk->get($file);

                $content = str_replace([
                    '{% extends "' . $code . '/_layout.twig" %}',
                    '{% use "' . $code . '/_blocks.twig" %}'
                ], [
                    '{% extends "_layout.twig" %}',
                    '{% use "_blocks.twig" %}'
                ], $content);

                $disk->put('_export/tpl/' . $code . '/' . basename($file), $content);
            }

            foreach ($disk->files($path . 'message/content/') as $file) {
                $disk->copy($file, '_export/tpl/' . $code . '/mail_content_' . basename($file));
            }

            foreach ($disk->files($path . 'message/form/') as $file) {
                $disk->copy($file, '_export/tpl/' . $code . '/mail_form_' . basename($file));
            }
        }

        $disk->put('_export/data.json', json_encode($this->data));

        $zip = '_export/' . request()->getHost() . '.zip';

        compress($disk->path('_export'), $disk->path($zip));

        $response = response($disk->get($zip), 200)->header('Content-Type', $disk->mimeType($zip))->header('Content-Length', $disk->size($zip));

        $disk->deleteDirectory('_export');

        return $response;
    }

    private function site()
    {
        $this->data['site'] = [
            'url' => config('app.url'),
            'subject' => config('site.subject'),
            'mail' => config('mail.to.address'),
        ];
    }

    private function language()
    {
        $this->data['languages'] = collect(config('lang.available'))->values()->map(fn ($language, $key) => [
            'name' => $language['name'],
            'code' => $language['code'],
            'status' => $language['accessible'],
            'icon' => $language['code'],
            'is_default' => $language['code'] == config('lang.frontend'),
            'order' => $key
        ])->toArray();
    }

    private function page_field()
    {
        $this->data['page_fields'] = NodeField::index()->values()->map(fn ($field) => [
            'name' => $field->id,
            'label' => $field->label,
            'type' => $this->field_types[$field->field_type],
            'description' => $field->description,
            'is_required' => $field->field_meta['required'] ?? false,
            'help' => $field->field_meta['helptext'] ?? '',
            'weight' => $field->weight,
            'default' => $field->field_meta['default'] ?? '',
            'length' => $field->field_meta['maxlength'] ?? 0,
            'options' => $field->field_meta['options'] ?? '',
            'verify' => $field->field_meta['rules'] ?? '',
            'pattern' => '',
            'is_preset' => $field->is_reserved,
            'is_global' => $field->is_global
        ])->toArray();
    }

    private function page_type()
    {
        $this->data['page_types'] = NodeType::index()->map(function ($type) {
            return [
                'name' => $type->id,
                'label' => $type->label,
                'description' => $type->description,
                'is_preset' => $type->is_reserved,
                'fields' => $type->fields->map(function ($field) {
                    $config = $field->pivot->field_meta ? unserialize($field->pivot->field_meta) : [];
                    return [
                        'name' => $field->id,
                        'label' => $config['label'] ?? $field->pivot->label,
                        'description' => $config['description'] ?? '',
                        'is_required' => $config['required'] ?? false,
                        'help' => $config['helptext'] ?? '',
                        'weight' => $field->weight,
                        'default' => $config['default'] ?? '',
                        'length' => $config['maxlength'] ?? 0,
                        'options' => $config['options'] ?? '',
                        'verify' => $config['rules'] ?? '',
                        'pattern' => '',
                        'order' => $field->pivot->delta,
                        'is_preset' => $field->is_reserved,
                        'is_global' => $field->is_global,
                    ];
                })->toArray()
            ];
        })->toArray();
    }

    private function mail_field()
    {
        $this->data['mail_fields'] = MessageField::index()->values()->map(fn ($field) => [
            'name' => $field->id,
            'label' => $field->label,
            'type' => $this->field_types[$field->field_type],
            'description' => $field->description,
            'is_required' => $field->field_meta['required'] ?? false,
            'help' => $field->field_meta['helptext'] ?? '',
            'weight' => $field->weight,
            'default' => $field->field_meta['default'] ?? '',
            'length' => $field->field_meta['maxlength'] ?? 0,
            'options' => $field->field_meta['options'] ?? '',
            'verify' => $field->field_meta['rules'] ?? '',
            'pattern' => '',
            'is_preset' => $field->is_reserved,
            'is_global' => $field->is_global
        ])->toArray();
    }

    private function mail_type()
    {
        $this->data['mail_types'] = MessageForm::index()->map(function ($type) {
            return [
                'name' => $type->id,
                'label' => $type->label,
                'description' => $type->description,
                'is_preset' => $type->is_reserved,
                'fields' => $type->fields->map(function ($field) {
                    $config = $field->pivot->field_meta ? unserialize($field->pivot->field_meta) : [];
                    return [
                        'name' => $field->id,
                        'label' => $config['label'] ?? $field->pivot->label,
                        'description' => $config['description'] ?? '',
                        'is_required' => $config['required'] ?? false,
                        'help' => $config['helptext'] ?? '',
                        'weight' => $field->weight,
                        'default' => $config['default'] ?? '',
                        'length' => $config['maxlength'] ?? 0,
                        'options' => $config['options'] ?? '',
                        'verify' => $config['rules'] ?? '',
                        'pattern' => '',
                        'order' => $field->pivot->delta,
                        'is_preset' => $field->is_reserved,
                        'is_global' => $field->is_global,
                    ];
                })->toArray()
            ];
        })->toArray();
    }

    private function page()
    {
        $this->data['pages'] = Node::withTrashed()->get()->map(function ($node) {
            $data = $node->gather();

            $data['languages'] = $node->translations->mapWithKeys(function ($translate) use ($node) {
                return [$translate->langcode => $node->translateTo($translate->langcode)->gather()];
            })->toArray();

            $info = [
                'id' => $data['id'],
                'type' => $data['mold_id'],
                'title' => $data['title'],
                'view' => $data['view'],
                'is_red' => $data['is_red'],
                'is_green' => $data['is_green'],
                'is_blue' => $data['is_blue'],
                'delete' => $node->trashed()
            ];

            $fields = $node->getFields()->keys()->flip()->toArray();

            $info = array_merge($info, array_intersect_key($data, $fields));

            foreach (config('lang.available') as $code => $language) {
                if ($code == config('lang.frontend')) {
                    continue;
                }

                if (isset($data['languages'][$code])) {
                    $info['languages'][$code] = array_intersect_key($data['languages'][$code], array_merge($fields, array_flip(['title', 'view', 'is_red', 'is_green', 'is_blue'])));
                } else {
                    $info['languages'][$code] = null;
                }                
            }

            return $info;
        })->toArray();
    }

    private function catalog()
    {
        $this->data['catalogs'] = Catalog::all()->map(function ($catalog) {
            $data = [
                'name' => $catalog->id,
                'label' => $catalog->label,
                'description' => $catalog->description,
                'is_preset' => $catalog->is_reserved
            ];

            $tree = $catalog->nodes->map(fn ($node) => $node->pivot->toArray());

            $data['tree'] = $tree->filter(fn ($node) => $node['parent_id'] == 0)->map(function ($node) use ($tree) {
                $data = ['id' => $node['node_id']];
                if ($children = $this->treeConvert($node['node_id'], $tree)) {
                    $data['children'] = $children;
                }

                return $data;
            })->toArray();

            return $data;
        })->toArray();
    }

    private function mail()
    {
        $this->data['mails'] = Message::all()->map(function ($message) {
            $data = [
                'type' => $message->mold_id,
                'language' => $message->langcode,
                'user_agent' => $message->user_agent,
                'ip' => $message->ip,
                'trails' => json_encode($message->trails),
                'server' => json_encode($message->_server),
                'status' => $message->is_sent
            ];

            return array_merge($data, $message->fields->keyBy('id')->map(fn (MessageField $field) => $field->bindEntity($message)->getValue())->all());
        })->filter()->toArray();
    }

    private function translate()
    {
        $this->data['translate'] = [
            'code' => config('translate.code'),
            'fields' => config('translate.fields'),
            'text' => config('translate.text'),
            'replace' => collect(json_decode(config('translate.replace'), true))->mapWithKeys(fn ($list, $code) => [config('lang.frontend') . '_to_' . $code => $list])->toJson(),
        ];
    }

    private function treeConvert(int $id, $list)
    {
        $list = $list->filter(fn ($node) => $node['parent_id'] == $id);

        if ($list->isEmpty()) {
            return null;
        }

        $sort = [];

        $sort[] = ['id' => $list->filter(fn ($node) => is_null($node['prev_id']))->first()['node_id']];

        while (true) {
            $end = end($sort);
            $node = $list->filter(fn ($node) => $node['prev_id'] == $end['id']);
            if ($node->isEmpty()) break;
            $sort[] = ['id' => $node->first()['node_id']];
        }

        foreach ($sort as $key => $value) {
            if ($children = $this->treeConvert($value['id'], $list)) {
                $sort[$key]['children'] = $children;
            }
        }

        return $sort;
    }
}
