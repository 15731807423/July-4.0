<?php

namespace Specs;

/**
 * 三种调用方式
 * 1.PHP处理后台的数据
 * 2.js处理后台的数据
 * 3.js处理传进来的数据
 * 
 * 使用步骤
 * 1.实例化							new
 * 2.设置规格						setSpec
 * 3.设置被使用配置信息的规格（可选）	setAttr
 * 4.设置配置信息（可选）				setConfig
 * 5.设置数据（可选）					setData
 * 6.处理数据						handleData
 * 7.调用一个组件或渲染全部			call or output
 * 
 * 处理流程
 * 1.实例化
 * 2.设置规格						过滤不存在的规格 获取全部规格的数据
 * 3.设置被指用配置信息的规格			获取规格的配置信息保存
 * 4.设置配置信息						把配置信息处理后保存
 * 5.设置数据						用给定的数据替换数据库查出的数据
 * 6.处理数据						3 配置信息设置默认值 处理渲染时需要提供的数据
 * 7.调用
 */

class Vue
{
	use VueApi;

	// 全部使用的规格 ['name1', 'name2']
	private $specs = [];

	// 被使用配置信息的规格 Spec
	private $attr;

	// 后台默认的配置信息 config('specList')
	private $configDefault = [];

	// 本次使用的配置信息 ['specList.model' => 'dynamic']
	private $configThis = [];

	// 最终使用的配置信息 configThis 覆盖 configDefault
	private $config = [];

	// 最终展示的数据 二维数组
	private $data = [];

	// 合法组件名称
	private $components = [
		'resetButton',
		'searchInput',
		'searchButton',
		'screenUser',
		'screenAll',
		'selector',
		'table',
		'list',
		'pagination'
	];

	// 组件在模板中的显示条件 不包括组件的容器
	private $conditionTpl = [
		'resetButton'	=> '',
		'searchInput'	=> '',
		'searchButton'	=> 'search.buttonConfig.status',
		'screenUser'	=> 'screen.userStatus && userSelectedList.length > 0',
		'screenAll'		=> '',
		'selector'		=> '',
		'table'			=> '',
		'list'			=> '',
		'pagination'	=> ''
	];

	// 组件在调用时的显示条件
	private $conditionCall = [
		'resetButton'	=> '',
		'searchInput'	=> '',
		'searchButton'	=> '',
		'screenUser'	=> '',
		'screenAll'		=> '',
		'selector'		=> '',
		'table'			=> '!selector || selector.value == \'table\'',
		'list'			=> '!selector || selector.value == \'list\'',
		'pagination'	=> ''
	];

	// 组件在模板中的容器
	private $container = [
		'resetButton'	=> ['<div v-if="reset.status" :class="reset.class">', '</div>'],
		'search'		=> ['<div v-if="search.status" :class="search.class">', '</div>'],
		'screen'		=> ['<div v-if="screen.status" :class="screen.class">', '</div>'],
		'selector'		=> ['<div v-if="table.status && listItem.length > 0" :class="selector.class">', '</div>'],
		'table'			=> ['<div v-if="table.status && (!selector || selector.value == \'table\')" class="data-table">', '</div>'],
		'list'			=> ['<div v-if="listItem.length > 0 && (!selector || selector.value == \'list\')" class="data-list">', '</div>'],
		'pagination'	=> ['<div :class="pagination.class">', '</div>'],
		'whole'			=> ['<div id="spec{{ id }}" class="data">', '</div>']
	];

	// 规格字段中允许修改的属性
	private $updateSpecFieldAttr = [
		'label',
		'is_groupable',
		'screen_type',
		'screen_default',
		'screen_config',
		'screen_config_group',
		'is_searchable',
		'is_sortable',
		'is_hiddenable'
	];

	// 规格中允许修改的属性
	private $updateSpecAttr = [
		'table_status',
		'default_sort_field',
		'default_sort_mode',
		'table_config',
		'list_status',
		'list_item'
	];

	// 自定义的规格属性和字段属性 以下属性将覆盖后台的配置
	// name 字段将不允许被搜索： ['name' => ['is_searchable' => false]]
	// 不展示表格： ['table_status' => false]
	private $customSpecAttr = [];

	// 组件内容的缓存
	private $view = [];

	// 组件内容处理在模板中的显示条件后的缓存
	private $viewTpl = [];

	// 组件内容处理在调用时的显示条件后的缓存
	private $viewCall = [];

	// 渲染时需要提供的数据
	private $renderData = [];

	// 组装时的html内容
	private $html = '';

	// 组装时的js内容
	private $js = '';

	// 容器的id 用来实例化vue
	private $id = '';

	/**
	 * 构造函数 初始化一批成员属性
	 */
	function __construct()
	{
		// 后台默认的配置信息
		$this->configDefault = $this->config = config('specList');

		// 组件内容
		foreach ($this->components as $component) {
			$html						= $this->view($component);
			$this->viewTpl[$component]	= $this->handleCondition($html, $this->conditionTpl[$component]);
			$this->viewCall[$component] = $this->handleCondition($html, $this->conditionCall[$component]);
		}

		// 容器的id
		$this->id = strval(mt_rand(100000000, 999999999));
	}

	/**
	 * 设置规格 如果不设置数据 使用规格的数据 设置了数据使用设置的数据
	 * 
	 * @param  string|array $name
	 * @return $this
	 */
	public function setSpec(string|array $name)
	{
		// 规格名称处理成数组
		is_string($name) && $name = [$name];

		// 循环获取指定的规格
		foreach ($name as $spec) {
			// 获取规格的数据
			$data = Engine::make()->specs($spec)->get();

			// 如果该规格不存在 跳过
			if (!isset($data[$spec])) continue;

			// 该规格保存到成员属性
			$this->specs[] = $spec;

			// 处理该规格的数据 保存到成员属性
			$data = $data[$spec];
			foreach ($data['records'] as $key => $value) {
				$data['records'][$key]['spec'] = $data['attributes']['id'];
				$data['records'][$key]['id'] = intval($value['id']);
			}
			$this->data = array_merge($this->data, $data['records']);
		}

		return $this;
	}

	/**
	 * 获取规格
	 * 
	 * @return string|array
	 */
	public function getSpec()
	{
		return count($this->specs) == 0 ? null : (count($this->specs) == 1 ? $this->specs[0] : $this->specs);
	}

	/**
	 * 设置被使用配置信息的规格
	 * 
	 * @param  string $name
	 * @return $this
	 */
	public function setAttr(string $name)
	{
		$this->attr($name);
		return $this;
	}

	/**
	 * 获取被使用配置信息的规格
	 * 
	 * @return Spec
	 */
	public function getAttr() : Spec
	{
		return $this->attr;
	}

	/**
	 * 设置配置信息
	 * 
	 * @param  array $config 配置信息
	 * @return $this
	 */
	public function setConfig(array $config)
	{
		// 保存本次使用的配置信息到成员属性
		$this->configThis = $config;

		// 循环每个配置
		foreach ($config as $key => $value) {
			// 不是 specList. 开头的不要
			if (strpos($key, 'specList.') !== 0) continue;

			// 设 $key = 'specList.a.b.c.d'

			// 去掉前面的 specList 并用 . 切割 $key = ['a', 'b', 'c', 'd']
			$key = explode('.', str_replace('specList.', '', $key));

			// 取出 $key 的最后一位 $key = ['a', 'b', 'd']  $last = 'd'
			$last = array_splice($key, count($key) - 1, 1)[0];

			// 给 $key 的每个下标加上中括号 $key = ["['a']", "['b']", "['c']"]
			foreach ($key as $k => $val) $key[$k] = '[\'' . $val . '\']';

			// 用空字符串切成数组 $key = "['a']['b']['c']"
			$key = implode('', $key);

			// 拼接变量的字符串 $attr = "$this->config['a']['b']['c']"
			$attr = '$this->config' . $key;

			// 把值转成字符串
			if (is_int($value)) {
				$value = strval($value);
				$string = false;
			} elseif (is_bool($value)) {
				$value = $value ? 'true' : 'false';
				$string = false;
			} elseif (is_null($value)) {
				$value = 'null';
				$string = false;
			} else {
				$string = true;
			}

			// 如果这个配置信息存在 覆盖原有的值 'return isset($this->config['a']['b']['c']) && is_array($this->config['a']['b']['c']) && array_key_exists("d", $this->config['a']['b']['c']);';
			if (eval('return isset(' . $attr . ') && is_array(' . $attr . ') && array_key_exists("' . $last . '", ' . $attr . ');')) {
				// 字符串加引号 $this->config['a']['b']['c']['d'] = "value";
				// 其他不加引号 $this->config['a']['b']['c']['d'] = 1;
				eval($attr . '["' . $last . '"]' . ' = ' . ($string ? '"' . $value . '"' : $value) . ';');
			}
		}

		// 如果自定义了规格的属性 保存到成员属性
		isset($config['spec']) && $this->customSpecAttr = $config['spec'];

		// 处理合并组件的配置信息
		$this->handleComponentConfig($this->config['search']['inputConfig']);
		$this->handleComponentConfig($this->config['search']['buttonConfig']);
		$this->handleComponentConfig($this->config['reset']);
		$this->handleComponentConfig($this->config['selector']);
		$this->handleComponentConfig($this->config['pagination']);
		$this->handleComponentConfig($this->config['loading']);

		// int处理
		$this->int($this->config['screen']['type']);
		$this->arrayValueChangeType($this->config['screen']['groupCountType'], 'int');
		$this->int($this->config['pagination']['pageSize'], false);
		$this->int($this->config['pagination']['currentPage'], false);

		// 格式化筛选的默认值
		$this->config['specAll']['screenDefault'] = format_value($this->config['specAll']['screenDefault']);

		// 将配置信息转换成数组
		is_null($this->config['specAll']['screenConfig']) || $this->handleConfigValue($this->config['specAll']['screenConfig']);
		is_null($this->config['specAll']['screenGroupConfig']) || $this->handleConfigValue($this->config['specAll']['screenGroupConfig']);

		return $this;
	}

	/**
	 * 获取配置信息
	 * 
	 * @return array
	 */
	public function getConfig() : array
	{
		return $this->config;
	}

	/**
	 * 设置数据
	 * 
	 * @param  array $data 数据
	 * @return $this
	 */
	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * 获取数据
	 * 
	 * @return array
	 */
	public function getData() : array
	{
		return $this->data;
	}

	/**
	 * 处理数据 设置完成后调用
	 * 
	 * @return $this
	 */
	public function handleData()
	{
		// 设置被使用配置信息的规格
		$this->attr();

		// 配置信息设置默认值
		$this->handleConfigDefault();

		// 数据中的数字解析
		$this->handleDataNumber();

		// 处理渲染时需要提供的数据
		$this->handleRenderData();

		return $this;
	}

	/**
	 * 根据组件名称调用组件 用于自定义布局
	 * 
	 * @param  string $name 组件名称
	 * @return string
	 */
	public function call(string $name) : string
	{
		// 调用js
		if ($name == 'js') return $this->output(null);

		// 调用不存在的组件
		if (!in_array($name, $this->components)) return '';

		// 获取组件的内容并处理条件
		return $this->setComponentConfig($name, $this->viewCall[$name]);
	}

	/**
	 * 输出整体内容
	 * 
	 * @return string
	 */
	public function output(?string $html = '', ?string $js = '') : string
	{
		$this->html = is_null($html) ? '' : $this->html();
		$this->js = is_null($js) ? '' : $this->js();

		return str_replace(['{{ html }}', '{{ js }}'], [$this->html, $this->js], $this->view('tpl'));
	}

	/**
	 * 给组件设置配置信息
	 * 
	 * @param  string $name 组件名字
	 * @param  string $html 组件内容
	 * @return string
	 */
	private function setComponentConfig(string $name, string $html) : string
	{
		// 获取配置信息
		switch ($name) {
			case 'resetButton':
				$config = $this->config['reset'];
				unset($config['status'], $config['text']);
				$name = 'reset';
				break;

			case 'searchInput':
				$config = $this->config['search']['inputConfig'];
				unset($config['onInput'], $config['onChange']);
				$name = 'search.inputConfig';
				break;

			case 'searchButton':
				$config = $this->config['search']['buttonConfig'];
				unset($config['status'], $config['text']);
				$name = 'search.buttonConfig';
				break;

			case 'selector':
				$config = $this->config['selector'];
				unset($config['class'], $config['list']);
				$name = 'selector.config';
				break;

			case 'table':
				$config = $this->renderData['table']['config'];
				$name = 'table.config';
				break;

			case 'pagination':
				$config = $this->config['pagination'];
				break;
			
			default:
				return $html;
				break;
		}

		// 过滤配置信息 已经存在的不要
		foreach ($config as $key => $value) {
			// 大驼峰转中划线
			$attr = $this->snake($key, '-');
			// 判断是否存在
			if (strstr($html, $attr) || strstr($html, 'v-model:' . $attr) || strstr($html, '@' . $attr)) {
				unset($config[$key]);
				continue;
			}
			$config[$key] = ':' . $attr . '="' . $name . '.' . $key . '"';
		}

		return str_replace('{{ config }}', implode(' ', $config), $html);
	}

	/**
	 * 设置被使用配置信息的规格
	 */
	private function attr(?string $spec = null) : void
	{
		// 如果传参了 直接设置
		if (is_string($spec)) goto set;

		// 如果没传参并且已经设置过了 退出
		if (is_null($spec) && $this->attr)  goto end;

		// 获取配置信息里设置的规格
		$spec = count($this->specs) == 1 ? $this->specs[0] : $this->config['specAll']['specConfig'];

		// 如果配置信息里没有设置 当前实例也没有设置规格 退出
		if (!$spec && count($this->specs) == 0) goto end;

		set:

		// 设置被使用配置信息的规格
		$this->attr = Spec::find($spec ?: $this->specs[0]);

		end:
	}

	/**
	 * 组装时的html内容
	 * 
	 * @return string
	 */
	private function html() : string
	{
		$whole = [
			[
				$this->container['resetButton'][0],
				$this->setComponentConfig('resetButton', str_replace(':class="reset.class"', '', $this->viewTpl['resetButton'])),
				$this->container['resetButton'][1]
			],
			[
				$this->container['search'][0],
				$this->setComponentConfig('searchInput', $this->viewTpl['searchInput']),
				$this->setComponentConfig('searchButton', $this->viewTpl['searchButton']),
				$this->container['search'][1]
			],
			[
				$this->container['screen'][0],
				$this->setComponentConfig('screenUser', $this->viewTpl['screenUser']),
				$this->setComponentConfig('screenAll', $this->viewTpl['screenAll']),
				$this->container['screen'][1]
			],
			[
				$this->container['selector'][0],
				$this->setComponentConfig('selector', str_replace(':class="selector.class"', '', $this->viewTpl['selector'])),
				$this->container['selector'][1]
			],
			[
				$this->container['table'][0],
				$this->setComponentConfig('table', $this->viewTpl['table']),
				$this->container['table'][1]
			],
			[
				$this->container['list'][0],
				$this->setComponentConfig('list', $this->viewTpl['list']),
				$this->container['list'][1]
			],
			[
				$this->container['pagination'][0],
				$this->setComponentConfig('pagination', str_replace(':class="pagination.class"', '', $this->viewTpl['pagination'])),
				$this->container['pagination'][1]
			]
		];

		foreach ($whole as $key => $value) $whole[$key] = implode('', $value);

		return str_replace('{{ id }}', $this->id, $this->container['whole'][0]) . implode('', $whole) . $this->container['whole'][1];
	}

	/**
	 * 组装时的js内容
	 * 
	 * @return string
	 */
	private function js() : string
	{
		// 获取js内容
		$js = $this->view($this->config['model']);

		// 渲染数据
		$old = ['{{ config }}', '{{ table }}', '{{ listItem }}', '{{ id }}'];
		$new = [
			json_encode($this->renderData['config'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
			json_encode($this->renderData['table'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
			json_encode($this->renderData['listItem'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
			$this->configThis['id'] ?? 'spec' . $this->id
		];

		// 静态处理渲染数据列表
		$this->config['model'] == 'static'
		&& ($old[] = '{{ list }}')
		&& ($new[] = json_encode($this->renderData['list'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

		// 动态处理渲染规格名称和配置信息
		$this->config['model'] == 'dynamic'
		&& ($old = array_merge($old, ['{{ specs }}', '{{ configThis }}']))
		&& ($new = array_merge($new, [$this->renderData['specs'], $this->renderData['configThis']]));

		return str_replace($old, $new, $js);
	}

	/**
	 * 根据组件名称获取内容
	 */
	private function view(string $name)
	{
		// 如果存在缓存 直接返回
		if (isset($this->view[$name])) return $this->view[$name];

		// 缓存并返回
		return $this->view[$name] = view('specs::vue.' . $name)->render();
	}

	/**
	 * 处理组件里的条件
	 * @param  string $html 组件内容
	 * @param  string $condition 条件
	 * @return string
	 */
	private function handleCondition(string $html, string $condition) : string
	{
		// 有条件就替换 没有条件就清空
		return str_replace('v-if="{{ condition }}"', $condition ? 'v-if="' . $condition . '"' : '', $html);
	}

	/**
	 * 将配置信息转换成数组
	 * 
	 * @param  string $data 后台对组件的配置信息 attr:value|attr2:value2|attr3:value3 => ['attr' => value, 'attr2' => value2, 'attr3' => value3]
	 */
	private function handleConfigValue(string &$data)
	{
		// 没有内容直接为空数组
		if (strlen($data) == 0) {
			$data = [];
			goto end;
		}

		// 设 $data = 'attr:value|attr2:value2|attr3:value3'

		// 切割字符串 分出每个属性 $data = ['attr:value', 'attr2:value2', 'attr3:value3']
		$data = explode('|', $data);

		// 存放结果的数组
		$data2 = [];

		// 循环每个属性
		foreach ($data as $key => $value) {
			// 用冒号切成长度为2的数组
			$value = explode(':', $value, 2);

			// 长度不对直接跳出
			if (count($value) != 2) continue;

			// 存进结果数组
			$data2[$value[0]] = format_value($value[1]);
		}

		$data = $data2;

		end:
	}

	/**
	 * 处理合并组件的配置信息
	 * 
	 * @param  array $data 需要处理的组件的配置信息
	 */
	private function handleComponentConfig(array &$data)
	{
		// 如果组件的配置信息为空
		// $data = ['onInput' => true, 'onChange' => true, 'class' => null, 'componentConfig' => 'placeholder:search']
		if (is_null($data['componentConfig'])) {
			// 移除下标 $data = ['onInput' => true, 'onChange' => true, 'class' => null]
			unset($data['componentConfig']);
		} else {
			// 把配置信息转成数组 ['placeholder' => 'search']
			$this->handleConfigValue($data['componentConfig']);

			// 配置信息和搜索框组件的其他配置合并数组
			// $data = ['onInput' => true, 'onChange' => true, 'class' => null, 'placeholder' => 'search']
			$data = array_merge($data, $data['componentConfig']);

			// 移除原有数组下标 $data = ['onInput' => true, 'onChange' => true, 'class' => null]
			unset($data['componentConfig']);
		}
	}

	/**
	 * 处理渲染时需要提供的数据
	 */
	private function handleRenderData()
	{
		// 使用配置信息的规格的字段
		$fields = $this->attr->getFields()->values()->all();

		// 循环每个字段 如果存在自定义属性 覆盖原本属性
		foreach ($fields as $key => $value) {
			// 如果没有配置该字段 跳过当前循环
			if (!array_key_exists($value['field_id'], $this->customSpecAttr)) continue;

			// 如果配置的值不是数组 跳过当前循环
			if (!is_array($this->customSpecAttr[$value['field_id']])) continue;

			// 循环自定义信息
			foreach ($this->customSpecAttr[$value['field_id']] as $name => $data) {
				// 如果设置的属性不允许修改 跳过当前循环
				if (!in_array($name, $this->updateSpecFieldAttr)) continue;

				// 覆盖配置信息
				$fields[$key][$name] = $data;
			}
		}

		// 使用配置信息的规格的属性
		$attr = $this->attr->attributesToArray();

		// 循环每个自定义属性 如果是规格的属性 覆盖原本属性
		foreach ($this->customSpecAttr as $key => $value) {
			$key != 'table_config' && in_array($key, $this->updateSpecAttr) && $attr[$key] = $value;
		}

		// 收集隐藏字段 搜索字段 筛选字段 的字段名
		$hidden = $search = $screen = [];
		foreach ($fields as $key => $value) {
			$value['is_hiddenable'] && $hidden[] = $value['field_id'];
			$value['is_searchable'] && !$value['is_hiddenable'] && $search[] = $value['field_id'];
			$value['is_groupable'] && !$value['is_hiddenable'] && $screen[] = $value['field_id'];
		}

		// 筛选的字段名转换成属性 ['name'] 转换成 [['field_id' => 'name', ...]] $fields里的值
		foreach ($screen as $key => $name) foreach ($fields as $data) $name == $data['field_id'] && $screen[$key] = $data;

		// 循环每个字段处理字段内容
		foreach ($screen as $key => $value) {
			// 通用属性
			$item = [
				'name'	=> $value['label'],
				'field'	=> $value['field_id'],
				'type'	=> intval($value['screen_type'])
			];

			// 不为空的属性处理后加进来
			is_null($value['screen_default']) || $item['default'] = format_value($value['screen_default']);
			is_null($value['screen_item_order']) || $item['itemOrder'] = array_values(array_unique(explode('|', format_value($value['screen_item_order']))));
			is_null($value['screen_config']) || (($item['config'] = $value['screen_config']) && $this->handleConfigValue($item['config']));
			is_null($value['screen_config_group']) || (($item['configGroup'] = $value['screen_config_group']) && $this->handleConfigValue($item['configGroup']));

			$screen[$key] = $item;
		}

		// 如果展示表格
		if ($attr['table_status']) {
			$table = ['status' => true,'column' => [], 'config' => []];

			foreach ($fields as $key => $value) {
				if ($value['is_hiddenable']) continue;

				// 通用属性
				$item = [
					'field'		=> $value['field_id'],
					'title'		=> $value['label'],
					'sortable'	=> $value['is_sortable']
				];

				// 排序配置
				if ($attr['default_sort_field'] == $value['field_id']) {
					$item['sortableDefaultField'] = true;
					$item['sortableDefaultMode'] = $attr['default_sort_mode'] ?: 'asc';
				}

				// 字段组件配置
				$config = is_null($value['config']) ? '' : $value['config'];
				$this->handleConfigValue($config);

				// 加进数组里
				$table['column'][] = array_merge($item, $config);
			}

			// 表格配置信息
			$table['config'] = $attr['table_config'] ?? '';
			$this->handleConfigValue($table['config']);
			$table['config']['emptyText'] = $this->config['dataEmptyText'];

			$custom = $this->customSpecAttr['table_config'] ?? '';
			$this->handleConfigValue($custom);
			$table['config'] = $this->array_multiple_merge($table['config'], $custom);
		} else {
			$table = ['status' => false];
		}

		// 如果展示列表
		if ($attr['list_status']) {
			// 获取列表布局
			$listItem = $attr['list_item'] ?: '';

			// 移除隐藏字段
			foreach ($hidden as $key => $value) $listItem = str_replace('{ ' . $value . ' }', '', $listItem);
		} else {
			$listItem = '';
		}

		// 渲染时需要提供的数据
		$data = [
			'table'		=> $table,
			'listItem'	=> $listItem,
			'config'	=> $this->config
		];

		// 如果是静态处理 需要传数据
		$this->config['model'] == 'static' && $data['list'] = $this->data;

		// 如果使用了多个规格且配置中开启了查看全部规格时显示‘规格’信息
		if (count($this->specs) > 1 && $data['config']['specAll']['status'] && $table['status']) {
			// 配置信息
			$spec = $this->config['specAll'];

			// 字段的信息
			$item = ['field' => 'spec', 'title' => $spec['title']];

			// 字段的排序信息
			$spec['sortable'] && $item['sortable'] = true;

			// 字段的位置
			$order = intval($spec['order']) ?: 1;

			// 规格字段放到表格字段的指定位置
			array_splice($data['table']['column'], $order - 1, 0, [$item]);

			// 如果能搜索 放到搜索的字段里
			$spec['searchable'] && $search[] = 'spec';

			// 如果能筛选
			if ($spec['screenable']) {
				// 通用属性
				$item = [
					'name'	  => $spec['title'],
					'field'	 => 'spec',
					'type'	  => intval($spec['screenType'])
				];

				// 不为空的属性加进来
				$spec['screenDefault'] && $item['default'] = $spec['screenDefault'];
				$spec['screenItemOrder'] && $item['itemOrder'] = array_values(array_unique(explode('|', format_value($spec['screenItemOrder']))));
				$spec['screenConfig'] && $item['config'] = $spec['screenConfig'];
				$spec['screenGroupConfig'] && $item['configGroup'] = $spec['screenGroupConfig'];

				// 筛选项的位置
				$order = intval($spec['screenOrder']) ?: 1;

				// 规格筛选放到全部筛选的指定位置
				array_splice($screen, $order - 1, 0, [$item]);
			}
		} else {
			// 移除列表里的规格字段
			$data['listItem'] = str_replace('{ spec }', '', $listItem);
		}

		// 移除配置信息
		unset($data['config']['specAll']);

		// 搜索和筛选放进来
		$data['config']['search']['fields'] = $search;
		$data['config']['screen']['list'] = $screen;

		// 处理每组筛选项
		$data['config']['screen']['group'] = $this->handleScreenGroup($screen);

		$this->config['model'] == 'dynamic'
		&& ($data['specs'] = json_encode($this->specs))
		&& ($data['configThis'] = json_encode($this->configThis));

		// 保存成员属性
		$this->renderData = $data;
	}

	/**
	 * 配置信息设置默认值
	 */
	private function handleConfigDefault()
	{
		$default = [
			'model'				=> 'static',
			'cuttingSymbol'		=> null,
			'dataEmptyText'		=> null,
			'sortCaseSensitive'	=> false,
			'search'			=> [
				'status'			=> false,
				'default'			=> null,
				'caseSensitive'		=> false,
				'class'				=> 'data-search',
				'inputConfig'		=> [
					'onInput'			=> false,
					'onChange'			=> false,
					'class'				=> 'data-search-input'
				],
				'buttonConfig'		=> [
					'status'			=> false,
					'text'				=> 'search',
					'class'				=> 'data-search-button'
				],
			],
			'screen'			=> [
				'status'			=> false,
				'userStatus'		=> false,
				'clearText'			=> 'reset',
				'selectedClass'		=> 'data-screen-selected',
				'countStatus'		=> false,
				'groupCountType'	=> [1, 2, 5],
				'type'				=> 1,
				'nullHidden'		=> false,
				'class'				=> 'data-screen',
				'allClass'			=> 'data-screen-all'
			],
			'reset'				=> [
				'status'			=> false,
				'text'				=> 'reset',
				'class'				=> 'data-reset'
			],
			'selector'			=> [
				'class'				=> 'data-selector',
				'list'				=> [
					'table'				=> [
						'text'				=> 'table',
						'default'			=> true
					],
					'list'				=> [
						'text'				=> 'list',
						'default'			=> false
					],
				]
			],
			'pagination'		=> [
				'class'				=> 'pagination',
				'pageSize'			=> 10,
				'currentPage'		=> 1
			],
			'loading'			=> [
				'status'			=> false
			],
			'specAll'			=> [
				'specConfig'		=> null,
				'status'			=> false,
				'title'				=> 'Category',
				'order'				=> 1,
				'sortable'			=> false,
				'searchable'		=> false,
				'screenable'		=> false,
				'screenType'		=> 1,
				'screenOrder'		=> 1,
				'screenDefault'		=> null,
				'screenItemOrder'	=> null,
				'screenConfig'		=> null,
				'screenGroupConfig'	=> null
			]
		];

		$this->config = $this->array_multiple_merge($default, $this->config);
	}

	/**
	 * 处理数据中的非int类型的数字数据
	 */
	private function handleDataNumber()
	{
		foreach ($this->data as $key => $data) foreach ($data as $attr => $value) !is_int($value) && $this->isInt($value) && ($this->data[$key][$attr] = intval($value));
	}

	/**
	 * 处理每组筛选项
	 *
	 * @param  array $list 全部筛选的字段的数据
	 * @return array
	 */
	private function handleScreenGroup(array $list) : array
	{
		// 全部筛选信息和当前筛选信息
		$screenAll = $screenAllSelected = [];

		foreach ($list as $item) {
			// 获取这一列出现过的数据并正序排序
			$data = array_unique(array_column($this->data, $item['field']));
			foreach ($data as $key => $value) if (is_string($value) && strlen($value) == 0) unset($data[$key]);
			$data = array_values($data);

			// 如果存在切割符号 循环判断每个值 如果包含符号 用符号切割并合并到数组中
			if ($this->config['cuttingSymbol']) {
				$data2 = [];
				foreach ($data as $key => $value) {
					strstr($value, $this->config['cuttingSymbol'])
					? $data2 = array_merge($data2, explode($this->config['cuttingSymbol'], $value))
					: $data2[] = $value;
				}
				$data = array_values(array_unique($data2));
			}

			sort($data);

			// 如果存在自定义排序
			if (isset($item['itemOrder'])) $data = array_merge($order = array_intersect($item['itemOrder'], $data), array_diff($data, $order));

			// 每种筛选显示全部数据的情况
			$item['type'] == 1 && $all = 'All';
			$item['type'] == 2 && $all = [];
			$item['type'] == 3 && $all = ($item['config']['range'] ?? null) === true ? [$item['config']['min'] ?? 0, $item['config']['max'] ?? 100] : 0;
			$item['type'] == 4 && $all = '';
			$item['type'] == 5 && $all = '';

			// 处理一组筛选项
			$result = $this->handleScreenItem($item, $data, $item['default'] ?? $all, $all);
			$screenAll[$item['field']] = $result[0];
			$screenAllSelected[$item['field']] = $result[1];
		}

		return [$screenAll, $screenAllSelected];
	}

	/**
	 * 处理一组筛选项
	 * @param  array  $data    筛选项信息
	 * @param  array  $list    筛选项
	 * @param  mixed  $default 默认值
	 * @param  mixed  $all     每种筛选显示全部数据的情况
	 * @return array
	 */
	private function handleScreenItem(array $data, array $list, $default, $all)
	{
		// 如果默认值不合法
		$data['type'] == 1 && !in_array($default, $list) && $default = $all;
		$data['type'] == 2 && !array_intersect($default, $list) && $default = $all;

		// 处理筛选项 加上数量
		foreach ($list as $key => $value) $list[$key] = ['name' => $value, 'count' => 0];

		// 如果是单选 前面加上全部
		$data['type'] == 1 && array_unshift($list, ['name' => 'All', 'count' => 0]);

		// 配置信息
		$config = $data['config'] ?? [];
		$configGroup = $data['configGroup'] ?? [];

		// 如果是日期时间选择器
		if ($data['type'] == 4) {
			// 默认是日期时间范围选择
			$config['type'] = $config['type'] ?? 'datetimerange';

			// 如果是选择范围 应该用数组 否则是字符串
			$all = in_array($config['type'], ['dates', 'datetimerange', 'daterange']) ? [] : '';
		}

		// 如果是下拉菜单
		if ($data['type'] == 5) {
			// 多选用数组
			if (($config['multiple'] ?? null) === true) {
				$all = [];
			}

			// 单选且带清除按钮 用空字符串
			elseif (($config['clearable'] ?? null) === true) {
				$all = '';
			}

			// 单选 不带清除按钮 添加全部选项
			else {
				$all = 'All';
				$default = $default ?: 'All';
				array_unshift($list, ['name' => 'All', 'count' => 0]);
			}
		}

		// 返回值
		$result = [
			[
				'name'			=> $data['name'],
				'label'			=> $data['field'],
				'list'			=> $list,
				'type'			=> $data['type'],
				'default'		=> $default,
				'config'		=> $config,
				'configGroup'	=> $configGroup,
				'all'			=> $all
			],
			[
				'type'			=> $data['type'],
				'value'			=> $default,
				'config'		=> $config
			]
		];

		($data['type'] == 1 || $data['type'] == 2) && $result[1]['configGroup'] = $configGroup;

		return $result;
	}

	/**
	 * 合并多维数组 后面的覆盖前面的
	 * 
	 * @param  array 数组1
	 * @param  array 数组2
	 * @return array
	 */
	private function array_multiple_merge(array $array1, array $array2) : array
	{
		// 如果都是一维数组
		if (!is_multiple_array($array1) && !is_multiple_array($array2)) {
			// 如果都是索引数组
			if (check_array_type($array1) == 'index' && check_array_type($array2) == 'index') {
				// 如果后者有内容 用后者覆盖 否则用前者
				return $array2 ?: $array1;
			}

			// 如果都是关联数组
			if (check_array_type($array1) == 'associative' && check_array_type($array2) == 'associative') {
				// 循环后者每个下标 只要不是null 覆盖到前者上
				foreach ($array2 as $key => $value) is_null($value) || $array1[$key] = $value;
			}

			// 前者为空数组 后者有内容 用后者
			if (count($array1) == 0 && count($array2) != 0) {
				return $array2;
			}
		} else {
			// 循环后者每个下标
			foreach ($array2 as $key => $value2) {
				// 前者的当前下标
				$value1 = $array1[$key] ?? null;

				// 如果后者是数组
				if (is_array($value2)) {
					// 如果前者也是数组 合并数组 否则 覆盖
					$array1[$key] = is_array($value1) ? $this->array_multiple_merge($value1 ?: [], $value2) : $value2;
				}

				// 如果后者是null 使用默认值 跳出即可
				elseif (is_null($value2)) {
					continue;
				}

				// 否则表示设置了值 覆盖前者默认值
				else {
					$array1[$key] = $value2;
				}
			}
		}

		return $array1;
	}

	/**
	 * 数组的所有下标转数据类型
	 * 
	 * @param  array  $data 需要处理的数组
	 * @param  string $type 需要转换的类型
	 */
	private function arrayValueChangeType(array &$data, string $type)
	{
		// 转换的类型存在
		if (method_exists($this, $type)) {
			// 循环处理每个下标
			foreach ($data as $key => $value) {
				$this->{$type}($data[$key]);
			}
		}
	}

	/**
	 * 判断一个值是不是数字 包括字符串类型的数字
	 * @param  Mixed   $data 值
	 * @return boolean
	 */
	private function isInt($value) : bool
	{
		try {
			return eval('return is_int(' . $value . ');');
		} catch (\Throwable $e) {
			return false;
		}
	}

	/**
	 * 同 intval 不需要赋值
	 * 
	 * @param  $var  处理的值
	 * @param  $null 是否处理null
	 */
	private function int(&$var, $null = true)
	{
		(!is_null($var) || (is_null($var) && $null)) && $var = intval($var);
	}

	/**
	 * 同 strval 不需要赋值
	 * 
	 * @param  $var  处理的值
	 * @param  $null 是否处理null
	 */
	private function str(&$var, $null = true)
	{
		(!is_null($var) || (is_null($var) && $null)) && $var = strval($var);
	}

	/**
	 * 同 boolval 不需要赋值
	 * 
	 * @param  $var  处理的值
	 * @param  $null 是否处理null
	 */
	private function bool(&$var, $null = true)
	{
		(!is_null($var) || (is_null($var) && $null)) && $var = boolval($var);
	}

	/**
	 * 同 floatval 不需要赋值
	 * 
	 * @param  $var  处理的值
	 * @param  $null 是否处理null
	 */
	private function float(&$var, $null = true)
	{
		(!is_null($var) || (is_null($var) && $null)) && $var = floatval($var);
	}

	/**
	 * 驼峰转下划线
	 *
	 * @param  string $value
	 * @param  string $delimiter
	 * @return string
	 */
	private function snake(string $value, string $delimiter = '_') : string
	{
		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, preg_replace('/\s+/u', '', ucwords($value))));
	}

	/**
	 * 下划线转驼峰(首字母小写)
	 *
	 * @param  string $value
	 * @return string
	 */
	private function camel(string $value) : string
	{
		return lcfirst($this->studly($value));
	}

	/**
	 * 下划线转驼峰(首字母大写)
	 *
	 * @param  string $value
	 * @return string
	 */
	private function studly(string $value) : string
	{
		return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
	}
}
