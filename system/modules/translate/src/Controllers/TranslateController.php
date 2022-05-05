<?php

namespace Translate\Controllers;

use Translate\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * 翻译功能
 */
class TranslateController extends Controller
{
    private $time = [];

    // 全部不翻译的字段
    private $notFields = [
        'url',
        'meta_canonical',
        'image_src',
        'nav_icon',
        'applications',
        'projects',
        'installation',
        'data_pdf',
        'brochure_pdf',
        'nav_img',
        'index_pro',
        'pro',
        'list_icon',
        'muses',
        'athena',
        'apollo',
        'hephaistos',
        'triton',
        'astraios'
    ];

    // 全部不翻译的内容
    private $notText = [
        'Argger',
        'ARGGER',
        'argger',
        'MUSES',
        'ATHENA',
        'APOLLO',
        'HEPHAISTOS',
        'TRITON',
        'ASTRAIOS'
    ];

    /**
     * 翻译全部字段
     */
    public function all()
    {
        if (!config('lang.multiple')) return response('');

        $front = config('lang.frontend');
        $list = [];
        $result = [];

        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        if (count($list) == 0) return response('');

        foreach ($list as $key => $value) {
            $data = $this->content($front, $value);

            if (is_string($data)) return 'content:' . $data;

            $result[$value]['content'] = $data;
        }

        foreach ($list as $key => $value) {
            $data = $this->data($front, $value);

            if (is_string($data)) return $data;

            $result[$value] = array_merge($result[$value], $data);
        }

        return $result;
    }

    /**
     * 翻译全部字段2.0
     */
    public function all2()
    {
        if (!config('lang.multiple')) return response('');

        $front = config('lang.frontend');
        $list = [];
        $result = [];

        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        if (count($list) == 0) return response('');

        // 过滤完开始翻译
        // 创建保存翻译id和状态的数组 状态默认false表示未翻译完成
        $taskid = [];
        $status = [];
        $error = [];
        foreach ($list as $key => $value) {
            $status[$value] = false;
        }

        // 同时处理数据创建任务并获取taskid
        foreach ($list as $key => $value) {
            $this->time[$value] = [time()];
            $taskid[$value] = $this->handle($front, $value);
            if (is_string($taskid[$value])) {
                return $taskid[$value];
            } elseif (is_null($taskid[$value])) {
                unset($taskid[$value]);
            }
        }

        if (count($taskid) == 0) {
            return response([]);
        }

        // 创建死循环获取翻译结果
        while (true) {
            // 3秒后开始获取结果
            sleep(3);

            // 循环每个id获取结果
            foreach ($taskid as $key => $value) {
                // 如果这个语言编码的状态为真 表示已经获取到结果并修改了数据库 跳过这个语言
                if ($status[$key]) continue;

                // 用id获取结果 结果为数组表示获取成功 结果为null表示还在翻译 结果为false表示翻译失败
                $data = $this->get($value);

                // 如果结果是字符串 修改语言的状态和数据库
                if (is_string($data)) {
                    $this->time[$key][1] = time();
                    $status[$key] = true;
                    $result[$key] = $this->update($data, $front, $key);
                } 

                // 如果是null 跳过本次循环 3秒后继续获取结果
                elseif (is_null($data)) {
                    continue;
                }

                // 如果是false 记录这个语言翻译失败
                elseif ($data === false) {
                    $error[$key] = false;
                }
            }

            // 如果全部语言的状态都为真 说明全部语言翻译完成 终止循环
            if (count($status) == count(array_filter($status))) {
                break;
            }

            // 如果翻译完成的结果数量加上翻译失败的结果数量等于全部语言的数量 表示全部语言都获取到了结果 终止循环
            elseif (count(array_filter($status)) + count($error) == count($status)) {
                break;
            }
        }

        return response($result);
    }

    /**
     * 翻译指定内容
     */
    public function batch(Request $request)
    {
        $from   = config('lang.content');
        $to     = $request->input('to');
        $text   = json_decode($request->input('text'), true);

        if ($from == $to || count($text) == 0) {
            return $text;
        }

        foreach ($text as $key => $value) {
            if (in_array($key, $this->notFields)) {
                unset($text[$key]);
            }
        }

        $cutting = '<div class="translate-cutting"></div>';
        $html = implode($cutting, $text);

        $html = $this->html($html, $from, $to);

        if (is_array($html)) return $html[0];

        $html = explode($cutting, $html);

        if (count($html) != count($text)) {
            return '翻译后内容数量不一致';
        }

        $i = 0;
        foreach ($text as $key => $value) {
            $text[$key] = $html[$i];
            $i++;
        }

        return $text;
    }

    private function handle(string $from, string $to)
    {
        // 获取全部页面的id
        $id = Db::table('nodes')->where('langcode', $from)->pluck('id')->toArray();

        // 结果
        $list = [];
        $cutting = [
            '<div class="translate-field-cutting"></div>',
            '<div class="translate-page-cutting"></div>'
        ];

        // 循环获取每个页面的字段内容
        foreach ($id as $key => $value) {
            $data = $this->page($value, $from, $to);
            if (!$data) continue;
            $list[] = implode($cutting[0], $data);
        }

        if (count($list) == 0) {
            return null;
        }

        $html = implode($cutting[1], $list);

        $data = $this->create($html, $from, $to);

        return [
            $data['data']['body']['TaskId'],
            $data['file']
        ];
    }

    private function page(string $id, string $from, string $to)
    {
        $list = [];

        $fields = Db::table('node_fields')->pluck('id')->toArray();
        $fields = array_diff($fields, $this->notFields);
        $fields[] = 'title';

        foreach ($fields as $key => $value) {
            switch ($value) {
                case 'title':
                    $check = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $to)->exists();
                    if (!$check) {
                        $list['title'] = Db::table('nodes')->where('id', $id)->value('title');
                    }
                    break;

                default:
                    $check = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $to)->exists();
                    if (!$check) {
                        $list[$value] = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $from)->value($value);
                    }
                    break;
            }
        }

        return array_filter($list);
    }

    private function update(string $html, string $from, string $to)
    {
        // 获取全部页面的id
        $id = Db::table('nodes')->where('langcode', $from)->pluck('id')->toArray();

        // 结果
        $list = [];
        $cutting = [
            '<div class="translate-field-cutting"></div>',
            '<div class="translate-page-cutting"></div>'
        ];

        $html = explode($cutting[1], $html);
        foreach ($html as $key => $value) {
            $html[$key] = explode($cutting[0], $value);
        }

        // 循环获取每个页面的字段内容
        foreach ($id as $key => $value) {
            $data = $this->page($value, $from, $to);
            if (!$data) {
                unset($id[$key]);
                continue;
            }

            // if (count($data) != count($html[$key])) {
            //     $id[$key] = [$value, '翻译后内容数量发生变化'];
            //     continue;
            // }

            $html2 = array_splice($html, 0, 1);

            $id[$key] = [$value, $this->save($data, $html2[0], $value, $from, $to)];
        }

        return array_values($id);
    }

    private function save(array $old, array $new, string $id, string $from, string $to)
    {
        $i = 0;
        foreach ($old as $key => $value) {
            $old[$key] = $new[$i];
            $i++;
        }

        foreach ($old as $key => $value) {
            switch ($key) {
                case 'title':
                    $data = Db::table('nodes')->where('id', $id)->first();

                    Db::table('node_translations')->insert([
                        'entity_id'     => intval($id),
                        'mold_id'       => $data->mold_id,
                        'title'         => $value,
                        'view'          => $data->view,
                        'is_red'        => $data->is_red,
                        'is_green'      => $data->is_green,
                        'is_blue'       => $data->is_blue,
                        'langcode'      => $to,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    break;

                default:
                    Db::table('node__' . $key)->insert([
                        'entity_id'     => $id,
                        $key            => $value,
                        'langcode'      => $to,
                        'created_at'    => date('Y-m-d H:i:s', time())
                    ]);
                    break;
            }
        }

        return array_keys($old);
    }

    // private function content(string $front, string $code)
    // {
    //     $ids    = [];
    //     $html   = [];
    //     $list   = Db::table('node__content')->where('langcode', $front)->get()->toArray();

    //     foreach ($list as $key => $value) {
    //         $check = Db::table('node__content')->where('entity_id', $value->entity_id)->where('langcode', $code)->exists();
    //         if (!$check) {
    //             $ids[] = $value->entity_id;
    //             $html[] = $value->content;
    //         }
    //     }

    //     if (count($ids) == 0) return 0;

    //     $cutting = '<div class="translate-cutting"></div>';
    //     $html = implode($cutting, $html);

    //     $html = $this->html($html, $front, $code);

    //     if (is_array($html)) return $html[0];

    //     $html = explode($cutting, $html);

    //     if (count($html) != count($ids)) {
    //         return '翻译后内容数量不一致';
    //     }

    //     foreach ($ids as $key => $value) {
    //         Db::table('node__content')->insert([
    //             'entity_id'     => $value,
    //             'content'       => $html[$key],
    //             'langcode'      => $code,
    //             'created_at'    => date('Y-m-d H:i:s', time())
    //         ]);
    //     }

    //     return count($ids);
    // }

    // private function data(string $front, string $code)
    // {
    //     // 把front语言翻译成code语言 翻译全部页面的全部字段
    //     // 循环每个字段 获取每个字段需要翻译的页面

    //     $fields = [
    //         'title',
    //         'image_alt',
    //         'meta_description',
    //         'meta_keywords',
    //         'meta_title',
    //         'summary'

    //         // 添加其他自定义字段
    //     ];

    //     $count  = 0;
    //     $html   = [];

    //     foreach ($fields as $key => $value) {
    //         $data       = $this->getField($value, $front, $code);
    //         $count      += count($data);
    //         $html[]     = implode('<br />', $data);
    //     }

    //     $html = array_filter($html);

    //     if (count($html) == 0) {
    //         return [];
    //     }

    //     $html = implode('<br />', $html);
    //     $html = '<p>' . $html . '</p>';

    //     $html = $this->html($html, $front, $code);

    //     if (is_array($html)) return $html[0];

    //     $html = ltrim($html, '<p>');
    //     $html = rtrim($html, '</p>');
    //     $html = explode('<br />', $html);

    //     if (count($html) != $count) {
    //         return '翻译后内容数量不一致';
    //     }

    //     $result = [];
    //     foreach ($fields as $key => $value) {
    //         $count      = count($html);
    //         $html       = $this->setField($value, $front, $code, $html);
    //         $result[$value] = $count - count($html);
    //     }

    //     return $result;
    // }

    // private function getField(string $data, string $front, string $code)
    // {
    //     switch ($data) {
    //         case 'title':
    //             $list = Db::table('nodes')->where('langcode', $front)->get()->toArray();
    //             $text = [];
    //             foreach ($list as $key => $value) {
    //                 $check = Db::table('node_translations')->where('entity_id', $value->id)->where('langcode', $code)->exists();
    //                 if (!$check) $text[] = $value->title;
    //             }
    //             return $text;
    //             break;
            
    //         default:
    //             $list = Db::table('node__' . $data)->where('langcode', $front)->get()->toArray();
    //             $text = [];
    //             foreach ($list as $key => $value) {
    //                 $check = Db::table('node__' . $data)->where('entity_id', $value->entity_id)->where('langcode', $code)->exists();
    //                 if (!$check) $text[] = $value->{$data};
    //             }
    //             return $text;
    //             break;
    //     }
    // }

    // private function setField(string $data, string $front, string $code, array $all)
    // {
    //     switch ($data) {
    //         case 'title':
    //             $list = Db::table('nodes')->where('langcode', $front)->get()->toArray();
    //             foreach ($list as $key => $value) {
    //                 $check = Db::table('node_translations')->where('entity_id', $value->id)->where('langcode', $code)->exists();
    //                 if (!$check) {
    //                     $html = array_splice($all, 0, 1);
    //                     Db::table('node_translations')->insert([
    //                         'entity_id'     => intval($value->id),
    //                         'mold_id'       => $value->mold_id,
    //                         'title'         => $html[0],
    //                         'view'          => $value->view,
    //                         'is_red'        => $value->is_red,
    //                         'is_green'      => $value->is_green,
    //                         'is_blue'       => $value->is_blue,
    //                         'langcode'      => $code,
    //                         'created_at'    => date('Y-m-d H:i:s')
    //                     ]);
    //                 }
    //             }
    //             return $all;
    //             break;
            
    //         default:
    //             $list = Db::table('node__' . $data)->where('langcode', $front)->get()->toArray();
    //             foreach ($list as $key => $value) {
    //                 $check = Db::table('node__' . $data)->where('entity_id', $value->entity_id)->where('langcode', $code)->exists();
    //                 if (!$check) {
    //                     $html = array_splice($all, 0, 1);
    //                     Db::table('node__' . $data)->insert([
    //                         'entity_id'     => intval($value->entity_id),
    //                         $data           => $html[0],
    //                         'langcode'      => $code,
    //                         'created_at'    => date('Y-m-d H:i:s')
    //                     ]);
    //                 }
    //             }
    //             return $all;
    //             break;
    //     }
    // }

    private function html(string $html, string $from, string $to)
    {
        $data = $this->create($html, $from, $to);

        $id = $data['data']['body']['TaskId'];

        $file = $data['file'];

        while (true) {
            sleep(3);

            $data = $this->get([$id, $file]);

            if (is_string($data)) {
                return $data;
            } elseif (is_null($data)) {
                continue;
            } elseif ($data === false) {
                return ['翻译失败'];
            }
        }

        $except = $this->except($data);

        foreach ($except as $key => $value) {
            $data = str_replace('<!-- ' . $value . ' -->', $value, $data);
        }

        return $data;
    }

    private function create(string $html, string $from, string $to)
    {
        $except = $this->except($html);

        foreach ($except as $key => $value) {
            $html = str_replace($value, '<!-- ' . $value . ' -->', $html);
        }

        $url    = env('APP_URL');
        $html   = html_entity_decode($html);
        $to     = $to == 'zh-Hans' ? 'zh' : $to;
        $path   = str_replace('system', '', base_path());
        $file   = md5(strval(time()) . strval(mt_rand(10000, 99999))) . '.html';

        $except = $this->except($html);

        $result = touch($path . $file);
        if (!$result) return 'html缓存文件生成失败';

        $handle = fopen($path . $file, 'w');
        fwrite($handle, $html);
        fclose($handle);

        $url .= '/' . $file;
        $data = Translate::create($url, $from, $to);
        return [
            'file'  => $path . $file,
            'data'  => $data
        ];
    }

    private function get(array $data)
    {
        $file = $data[1];
        $data = Translate::get($data[0]);

        if ($data['body']['Status'] == 'ready' || $data['body']['Status'] == 'translating') {
            return null;
        } elseif ($data['body']['Status'] == 'error') {
            return false;
        }

        if (file_exists($file)) unlink($file);

        $html = file_get_contents($data['body']['TranslateFileUrl']);
        $html = html_entity_decode($html);

        $except = $this->except($html);

        foreach ($except as $key => $value) {
            $html = str_replace('<!-- ' . $value . ' -->', $value, $html);
        }

        return $html;
    }

    /**
     * 获取一段html里不需要翻译的内容
     */
    private function except($html)
    {
        $list = $this->notText;

        $wrap = [
            ['{{', '}}'],
            ['{%', '%}']
        ];

        foreach ($wrap as $key => $value) {
            $pattern = '/' . $value[0] . '([\w\W]*?)' . $value[1] . '/';
            $matches = [];
            preg_match_all($pattern, $html, $matches);
            $list = array_merge($list, $matches[0]);
        }

        return array_unique($list);
    }
}
