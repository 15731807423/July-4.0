<?php

namespace Installer;

use Illuminate\Support\Facades\Schema;

use July\Node\Node;
use July\Node\Catalog;
use July\Node\NodeType;
use July\Node\NodeField;
use July\Node\NodeIndex;
use July\Node\NodeTranslation;
use July\Node\NodeFieldNodeType;

use July\Message\Message;
use July\Message\MessageForm;
use July\Message\MessageField;
use July\Message\MessageFieldMessageForm;

class Import
{
    private static $data;

    private static $root;

    private static $field_types = [
        'text' => 'App\EntityField\FieldTypes\Input',
        'textarea' => 'App\EntityField\FieldTypes\Text',
        'html' => 'App\EntityField\FieldTypes\Html',
        'image' => 'App\EntityField\FieldTypes\Image',
        'file' => 'App\EntityField\FieldTypes\File',
        'attachment' => 'July\Message\FieldTypes\Attachment',
        'attachments' => 'July\Message\FieldTypes\MultipleAttachment'
    ];

    private static $defaultLanguage;

    public static function run()
    {
        self::$root = base_path('../');

        if (!is_file($file = self::$root . '/_data.json')) {
            return false;
        }

        if (is_null(self::$data = json_decode(file_get_contents($file), true))) {
            return false;
        }

        unlink($file);

        self::$defaultLanguage = collect(self::$data['languages'])->firstWhere('is_default', true);

        self::site();
        self::language();
        self::page_field();
        self::page_type();
        self::mail_field();
        self::mail_type();
        self::page();
        self::catalog();
        self::mail();
        self::translate();

        return true;
    }

    private static function site()
    {
        $data = self::$data['site'];

        app()->make('settings.site_information')->save([
            'app.url' => $data['url'],
            'site.subject' => $data['subject'],
            'site.mails' => $data['mails']
        ]);
    }

    private static function language()
    {
        $data = self::$data['languages'];

        app()->make('settings.language')->save([
            'lang.multiple' => count($data) > 1,
            'lang.available' => collect($data)->mapWithKeys(function ($language) {
                $icon = '/images/' . $language['code'] . '.svg';
                file_put_contents(self::$root . $icon, $language['icon']);
                return [$language['code'] => [
                    'accessible' => $language['status'],
                    'translatable' => $language['status'],
                    'name' => $language['name'],
                    'code' => $language['code'],
                    'icon' => $icon
                ]];
            })->toArray(),
            'lang.content' => self::$defaultLanguage['code'],
            'lang.translate' => self::$defaultLanguage['code'],
            'lang.icon' => true,
            'lang.frontend' => self::$defaultLanguage['code'],
        ]);
    }

    private static function page_field()
    {
        $data = self::$data['page_fields'];

        NodeField::get()->map(fn ($field) => Schema::dropIfExists('node__' . $field->id));
        NodeType::truncate();
        NodeField::truncate();
        NodeFieldNodeType::truncate();

        foreach ($data as $field) {
            NodeField::create([
                'field_type' => self::$field_types[$field['type']],
                'id' => $field['name'],
                'label' => $field['label'],
                'description' => $field['description'],
                'required' => $field['is_required'],
                'helptext' => $field['help'],
                'weight' => $field['weight'],
                'default' => $field['default'],
                'maxlength' => $field['length'],
                'options' => $field['options'],
                'rules' => $field['verify'],
                'langcode' => self::$defaultLanguage['code'],
                'is_global' => $field['is_global'],
                'is_reserved' => $field['is_preset'],
                'field_group' => null,
                'placeholder' => null,
                'reference_scope' => null
            ]);
        }
    }

    private static function page_type()
    {
        $data = self::$data['page_types'];

        NodeType::truncate();
        NodeFieldNodeType::truncate();

        foreach ($data as $type) {
            NodeType::create([
                'id' => $type['name'],
                'label' => $type['label'],
                'description' => $type['description'],
                'langcode' => self::$defaultLanguage['code'],
                'is_reserved' => $type['is_preset'],
                'fields' => collect($type['fields'])->map(fn ($field, $name) => [
                    'id' => $name,
                    'field_type' => NodeField::find($name)->field_type,
                    'label' => $field['label'],
                    'description' => $field['description'],
                    'is_reserved' => $field['is_preset'],
                    'is_global' => $field['is_global'],
                    'field_group' => null,
                    'weight' => $field['weight'],
                    'langcode' => self::$defaultLanguage['code'],
                    'required' => $field['is_required'],
                    'default' => $field['default'],
                    'helptext' => $field['help'],
                    'maxlength' => $field['length'],
                    'rules' => $field['verify'],
                    'options' => $field['options'],
                ])->values()->sortBy(fn ($field) => $field['is_global'])->values()->map(fn ($field, $key) => array_merge($field, ['delta' => $key]))->toArray()
            ]);
        }
    }

    private static function mail_field()
    {
        $data = self::$data['mail_fields'];

        MessageField::get()->map(fn ($field) => Schema::dropIfExists('message__' . $field->id));
        MessageForm::truncate();
        MessageField::truncate();
        MessageFieldMessageForm::truncate();

        foreach ($data as $field) {
            MessageField::create([
                'field_type' => self::$field_types[$field['type']],
                'id' => $field['name'],
                'label' => $field['label'],
                'description' => $field['description'],
                'required' => $field['is_required'],
                'helptext' => $field['help'],
                'weight' => $field['weight'],
                'default' => $field['default'],
                'maxlength' => $field['length'],
                'options' => $field['options'],
                'rules' => $field['verify'],
                'langcode' => self::$defaultLanguage['code'],
                'is_global' => $field['is_global'],
                'is_reserved' => $field['is_preset'],
                'field_group' => null,
                'placeholder' => null,
                'reference_scope' => null
            ]);
        }
    }

    private static function mail_type()
    {
        $data = self::$data['mail_types'];

        MessageForm::truncate();
        MessageFieldMessageForm::truncate();

        foreach ($data as $type) {
            MessageForm::create([
                'id' => $type['name'],
                'label' => $type['label'],
                'description' => $type['description'],
                'langcode' => self::$defaultLanguage['code'],
                'is_reserved' => $type['is_preset'],
                'fields' => collect($type['fields'])->map(fn ($field, $name) => [
                    'id' => $name,
                    'field_type' => MessageField::find($name)->field_type,
                    'label' => $field['label'],
                    'description' => $field['description'],
                    'is_reserved' => $field['is_preset'],
                    'is_global' => $field['is_global'],
                    'field_group' => null,
                    'weight' => $field['weight'],
                    'langcode' => self::$defaultLanguage['code'],
                    'required' => $field['is_required'],
                    'default' => $field['default'],
                    'helptext' => $field['help'],
                    'maxlength' => $field['length'],
                    'rules' => $field['verify'],
                    'options' => $field['options'],
                ])->values()->sortBy(fn ($field) => $field['is_global'])->values()->map(fn ($field, $key) => array_merge($field, ['delta' => $key]))->toArray()
            ]);
        }
    }

    private static function page()
    {
        $data = self::$data['pages'];

        Node::truncate();
        NodeIndex::truncate();
        NodeTranslation::truncate();

        foreach ($data as $page) {
            $info = [
                'id' => $page['id'],
                'mold_id' => $page['type']['name'],
                'langcode' => self::$defaultLanguage['code']
            ];

            $info = array_merge($info, array_diff_key($page, array_flip(['id', 'type_id', 'created_at', 'updated_at', 'deleted_at', 'exists', 'type', 'languages'])));
            $info['_changed'] = array_keys($info);

            $node = Node::create($info);

            foreach ($page['languages'] as $code => $language) {
                if (is_null($language)) {
                    continue;
                }

                $node->translateTo($code);

                $info = [
                    'mold_id' => $page['type']['name'],
                    'langcode' => $code
                ];

                $info = array_merge($info, array_diff_key($language, array_flip(['id', 'type_id', 'created_at', 'updated_at', 'deleted_at', 'exists'])));
                $info['_changed'] = array_keys($info);
                $node->update($info);
            }

            if ($page['deleted_at']) {
                $node->delete();
            }
        }
    }

    private static function catalog()
    {
        $data = self::$data['catalogs'];

        Catalog::truncate();

        foreach ($data as $catalog) {
            $model = Catalog::create([
                'description' => $catalog['description'],
                'id' => $catalog['name'],
                'is_reserved' => $catalog['is_preset'],
                'label' => $catalog['label']
            ]);

            $model->updatePositions(self::treeConvert($catalog['tree']));
        }
    }

    private static function mail()
    {
        $data = self::$data['mails'];

        foreach ($data as $mail) {
            $fields = MessageForm::find($mail['type'])->fields->pluck('id')->toArray();

            $info = array_intersect_key($mail, array_flip($fields));
            $info = array_merge($info, [
                'mold_id' => $mail['type'],
                'langcode' => $mail['language'],
                'ip' => $mail['ip'],
                'user_agent' => $mail['user_agent'],
                'trails' => $mail['trails'],
                '_server' => $mail['server'],
                'is_sent' => $mail['status']
            ]);

            Message::create($info);
        }
    }

    private static function translate()
    {
        $data = self::$data['translate'];

        app()->make('settings.translate')->save([
            'translate.tool' => 'alibabacloud',
            'translate.mode' => 'task',
            'translate.code' => $data['code'],
            'translate.fields' => $data['fields'],
            'translate.text' => $data['text'],
            'translate.replace' => $data['replace'],
        ]);
    }

    private static function treeConvert(array $list, ?int $parent_id = null)
    {
        $positions = [];

        foreach ($list as $key => $data) {
            $positions[] = [
                'id' => $data['id'],
                'parent_id' => $parent_id,
                'prev_id' => $list[$key - 1]['id'] ?? null
            ];

            if (isset($data['children'])) {
                $positions = array_merge($positions, self::treeConvert($data['children'], $data['id']));
            }
        }

        return $positions;
    }
}
