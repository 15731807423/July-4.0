<?php
namespace Translate;

use Illuminate\Support\Facades\DB;

/**
 * 文档翻译
 */
class Direct
{
	// 域名
	private $domain;

	// 源语言
	private $from = 'en';

	// 翻译语言
	private $to;

	// 一键翻译翻译的页面id
	private $nodes;

	// 不翻译的字段
	private $notFields;

	// 不翻译的内容
	private $notText;

	// 指定翻译的内容
	private $appoint;

    // 模板路径
    private $tplPath;

    // 标识 第一个用于切割字段 第二个用于切割页面 第三个用于替换空格 第四个用于代替空页面的数据
    private $replace = [
        '<div class="translate-field-cutting"></div>',
        '<div class="translate-page-cutting"></div>',
        '<div class="translate-space"></div>',
        '<div class="translate-page-empty"></div>'
    ];

    // api
    private $api = 'https://api.vip/api/translate/translate';

    /**
     * 初始化一批成员属性
     */
	function __construct()
	{
		$this->domain 		= parse_url(env('APP_URL'))['host'];

        $this->notFields    = eval('return ' . str_replace("\n", '', config('translate.fields')) . ';');
        $this->notText      = eval('return ' . str_replace("\n", '', config('translate.text')) . ';');
        $this->appoint      = eval('return ' . str_replace("\n", '', config('translate.replace')) . ';');

        $this->notFields    = is_array($this->notFields) ? $this->notFields : [];
        $this->notText      = is_array($this->notText) ? $this->notText : [];
        $this->appoint      = is_array($this->appoint) ? $this->appoint : [];

        $this->tplPath      = base_path('../themes/frontend/template/');
	}

	/**
	 * 设置翻译语言
	 * 
	 * @param  string $code 翻译语言
	 * @return $this
	 */
	public function setTo(string $code)
	{
        // 源语言不能和翻译语言一致
		if ($code == $this->from) return $this;

        // 翻译语言必须在后台配置才能翻译
		if (!in_array($code, array_keys(config('lang.available')))) return $this;

		$this->to = $code;
		return $this;
	}

	/**
	 * 设置一键翻译翻译的页面id
	 * 
	 * @param  array $nodes 页面id
	 * @return $this
	 */
	public function setNodes(array $nodes)
	{
        // 只能设置数据库里存在的页面
		$nodes = Db::table('nodes')->whereIn('id', $nodes)->pluck('id')->toArray();

		$this->nodes = $nodes;
		return $this;
	}

	/**
	 * 批量翻译
     * 
     * @return array
	 */
	public function batch()
	{
		// 每个页面需要翻译的内容
		$pages = [];

		// 循环每个页面 获取每个页面需要翻译的内容
		foreach ($this->nodes as $id) {
			$data = $this->getPageContent($id);
            $pages[] = implode($this->replace[0], $data);
		}

        // 如果没有需要翻译的内容 返回null
		if (!$pages) return null;

        // 创建任务
        $result = $this->translate(implode($this->replace[1], $pages));
var_dump($result);die;
        // 返回错误信息
        if (!$result['status']) return $this->error($result['message']);

        // 切割成每个页面的翻译结果
        $pages = explode($this->replace[1], $result['data']);

        // 如果翻译后的页面数量和被翻译的页面数量不一致
        if (count($pages) != count($this->nodes)) {
            return $this->error('翻译完成，但翻译后页面数量和翻译前不一致');
        }

        // 保存每个页面被翻译的字段
        $fields = [];

        // 循环每个页面，为页面设置翻译后的内容
        foreach ($this->nodes as $key => $id) {
            $fields[$id] = $this->setPageContent($id, explode($this->replace[0], $pages[$key]));
        }

        return true;
	}

    /**
     * 翻译页面
     * 
     * @param  array $content 被翻译的内容
     * @return array
     */
    public function page(array $content)
    {
        // 过滤不翻译的内容
        foreach ($content as $key => $value) {
            if (in_array($key, $this->getNotFields())) {
                unset($content[$key]);
            }
        }

        // 创建任务
        $result = $this->translate(implode($this->replace[0], $content));

        // 返回错误信息
        if (!$result['status']) return $this->error($result['message']);

        // 翻译的字段
        $fields = array_keys($content);

        // 切割成每个页面的翻译结果
        $html = explode($this->replace[0], $result['data']);

        // 如果翻译后的页面数量和被翻译的页面数量不一致
        if (count($html) != count($fields)) {
            return ['result' => '翻译完成，但翻译后字段数量和翻译前不一致'];
        }

        $new = [];
        foreach ($html as $key => $value) {
            $new[$fields[$key]] = $value;
        }

        return $this->pageSuccess($new);
    }

    /**
     * 翻译模板
     * 
     * @return array
     */
    public function tpl()
    {
        // 如果目录已经存在 不执行任何操作
        // if (is_dir($this->tplPath . $this->to)) return $this->error('目录已存在，请先删除目录');

        // 获取模板目录里的文件
        $list = scandir($this->tplPath);

        // 过滤不复制的文件 复制
        foreach ($list as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            if ($value == 'google-sitemap.twig') continue;

            if (is_dir($this->tplPath . $value)) {
                if ($value != 'message' && $value != 'specs') continue;
            }

            if (is_file($this->tplPath . $value)) {
                if (pathinfo($this->tplPath . $value)['extension'] != 'twig') continue;
            }

            $this->tplCopy($this->tplPath . $value, $this->tplPath . $this->to . '/' . $value);
        }

        // 获取目录下需要翻译的文件路径
        $file = $this->tplFile($this->tplPath . $this->to);

        $html = [];

        // 获取文件内容
        foreach ($file as $key => $value) {
            $html[] = file_get_contents($value);
        }

        // 创建任务
        // $result = $this->translate(implode($this->replace[1], $html));
        $result = $this->translate('
            {# head #}
            {% block head -%}
                <a href="{{ url }}">one</a>
                <span>two</span>
            {% endblock -%}
            {{ csrf_field()|raw }}
        ');

        var_dump($result);
    }

    public function batchSuccess(array $list)
    {
        foreach ($list as $key => $value) {
            $list[$key] = $value === true
            ? ['status' => true, 'message' => '翻译成功']
            : ['status' => false, 'message' => $value->original['message']];
        }
        return response(['status' => true, 'data' => $list]);
    }

    public function pageSuccess(array $data)
    {
        return response(['status' => true, 'data' => $data]);
    }

    public function tplSuccess(string $message)
    {
        // return response(['status' => false, 'message' => $message]);
    }

    public function error(string $message)
    {
        return response(['status' => false, 'message' => $message]);
    }

    /**
     * 翻译模板 结果
     * 
     * @param  array $data 翻译接口返回的信息，用来获取数据
     * @return array
     */
    public function resultTpl(array $data)
    {
        return $this->result($data, function (string $html) {
            // 切割成每个模板的翻译结果
            $html = explode($this->replace[1], $html);

            // 获取目录下需要翻译的文件路径
            $file = $this->tplFile($this->tplPath . $this->to);

            // 如果翻译后的页面数量和被翻译的页面数量不一致
            if (count($html) != count($file)) {
                return ['result' => '翻译完成，但翻译后模板数量和翻译前不一致'];
            }

            // 写入文件内容
            foreach ($file as $key => $value) {
                file_put_contents($value, $html[$key]);
            }
        });
    }

	/**
	 * 获取一个页面需要翻译的内容
	 * 
	 * @param  int $id 页面id
	 * @return array
	 */
	private function getPageContent(int $id)
	{
        // 结果
        $list = [];

        // 获取页面的全部字段
        $fields = Db::table('node_fields')->pluck('id')->toArray();

        // 过滤不翻译的字段
        $fields = array_diff($fields, $this->getNotFields());

        // 判断title是否存在翻译版本
        $check = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $this->to)->exists();

        // 没有翻译版本 把内容放进结果里
        if (!$check) {
            $list['title'] = Db::table('nodes')->where('id', $id)->value('title');
        }

        // 循环每个字段
        foreach ($fields as $key => $value) {
            // 判断字段是否存在翻译版本
            $check = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $this->to)->exists();

            // 没有翻译版本 把内容放进结果里
            if (!$check) {
                $list[$value] = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $this->from)->value($value);
            }
        }

        // 过滤空字符串 返回结果
        return array_filter($list) ?: [$this->replace[3]];
	}

    /**
     * 为页面设置翻译后的内容
     * 
     * @param  int   $id   页面id
     * @param  array $html 页面每个字段的翻译结果
     * @return array
     */
    private function setPageContent(int $id, array $html)
    {
        // 获取翻译前的页面内容
        $new = $old = $this->getPageContent($id);

        // 判断字段数量是否一致
        if ($html == $this->replace[3] && $old == [$this->replace[3]]) return null;

        // 把翻以前的数据用翻译后的数据替换
        foreach ($new as $key => $value) {
            $new[$key] = array_splice($html, 0, 1)[0];
        }

        // 设置页面字段并返回翻译了的字段名称
        return $this->setPageFieldContent($id, $new);
    }

    /**
     * 设置页面字段
     * 
     * @param  int   $id   页面id
     * @param  array $list 字段数据列表
     * @return array
     */
    private function setPageFieldContent(int $id, array $list)
    {
        // 循环每个字段并设置内容
        foreach ($list as $key => $value) {
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
                        'langcode'      => $this->to,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    break;

                default:
                    Db::table('node__' . $key)->insert([
                        'entity_id'     => $id,
                        $key            => $value,
                        'langcode'      => $this->to,
                        'created_at'    => date('Y-m-d H:i:s', time())
                    ]);
                    break;
            }
        }

        // 返回修改的字段
        return array_keys($list);
    }

    /**
     * 复制模板文件
     * 
     * @param  string $old 被复制的文件路径
     * @param  string $new 复制后的文件路径
     */
    private function tplCopy(string $old, string $new)
    {
        // 复制的要求 old是一个文件 new不是文件也不是文件夹 防止覆盖
        if (is_file($old) && !is_file($new) && !is_dir($new)) {
            // new所在的目录不存在则创建
            if (!is_dir(dirname($new))) mkdir(dirname($new));
            copy($old, $new);
        }

        // 如果复制的是文件夹 则需要递归复制
        if (is_dir($old) && !is_file($new)) {
            if (!is_dir($new)) mkdir($new);
            $list = scandir($old);
            unset($list[0]);
            unset($list[1]);
            foreach ($list as $key => $value) {
                $this->tplCopy($old . '/' . $value, $new . '/' . $value);
            }
        }
    }

    /**
     * 获取模板里需要翻译的文件的路径
     * 
     * @param  string $path 模板路径
     * @return array  文件路径数组
     */
    private function tplFile(string $path)
    {
        $file = [];

        // 获取路径下的文件
        $list = scandir($path);
        foreach ($list as $key => $value) {
            if ($value == '.' || $value == '..') continue;

            if (is_dir($path . '/' . $value)) continue;
            $file[] = $path . '/' . $value;
        }

        // 获取路径下的表单文件
        $list2 = scandir($path . '/message/form');
        foreach ($list2 as $key => $value) {
            if ($value == '.' || $value == '..') continue;

            if (is_dir($path . '/message/form/' . $value)) continue;
            $file[] = $path . '/message/form/' . $value;
        }

        return $file;
    }

    /**
     * 处理结果
     * 
     * @param  array  $data 结果
     * @param  object $callback 成功的回调
     * @return array
     */
    private function result(array $data, object $callback)
    {
        // 如果事件已经完成，直接返回
        if (isset($data['complete']) && $data['complete']) return $data;

        // 可能创建任务失败了
        if ($data['status'] == 0) {
            $data['result'] = $data['msg'];
            $data['complete'] = true;
            return $data;
        }

        // 获取翻译结果
        $result = $this->get(json_encode($data['data']));

        // 根据状态码区分翻译结果情况
        switch ($result['status']) {
            // 接口处理失败，报错
            case -2:
                $data['result'] = $result['msg'];
                $data['complete'] = true;
                break;

            // 翻译失败，提示错误信息，事件完成
            case -1:
                $data['state'] = $result['data']['state'];
                $data['result'] = $result['data']['msg'];
                $data['complete'] = true;
                break;

            // 翻译正在准备或正在翻译，提示信息，事件未完成
            case 0:
                $data['state'] = $result['data']['state'];
                $data['result'] = '正在翻译';
                $data['complete'] = false;
                break;

            // 翻译完成，处理翻译结果，事件完成
            case 1:
                $data['state'] = $result['data']['state'];
                $data['result'] = '翻译完成';
                $data['complete'] = true;

                // 取出翻译结果的html
                $html = $result['data']['content'];

                $return = $callback($html);

                if (is_array($return)) $data = array_merge($data, $return);

                break;
        }

        return $data;
    }

    /**
     * 创建翻译任务
     * 
     * @param  string $html 被翻译的html
     * @return array
     */
    private function translate(string $html)
    {
        $result = post($this->api, [
            'html'          => $html,
            'from'          => $this->from,
            'to'            => $this->to,
            'not'           => $this->getNotText(),
            'appoint'       => $this->getAppoint(),
            'domain'        => $this->domain,
            'tool'          => config('translate.tool'),
            'replace'       => $this->replace
        ]);exit($result);die;

        return json_decode($result, true);
    }

    /**
     * 获取不翻译的字段
     * 
     * @return array
     */
    private function getNotFields()
    {
        return count($this->notFields) == count($this->notFields, 1) ? $this->notFields : ($this->notFields[$this->to] ?? []);
    }

    /**
     * 获取不翻译的内容
     * 
     * @return array
     */
    private function getNotText()
    {
        return count($this->notText) == count($this->notText, 1) ? $this->notText : ($this->notText[$this->to] ?? []);
    }

    /**
     * 获取指定的翻译结果
     * 
     * @return array
     */
    private function getAppoint()
    {
        return count($this->appoint) == count($this->appoint, 1) ? $this->appoint : ($this->appoint[$this->to] ?? []);
    }
}