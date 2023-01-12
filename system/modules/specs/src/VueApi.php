<?php

namespace Specs;

trait VueApi
{
	// 数据列表
	private $apiList;

	// 请求的搜索信息
	private $apiSearch;

	// 请求的筛选信息
	private $apiScreen;

	// 请求的排序信息
	private $apiSort;

	// 请求的分页信息
	private $apiPage;

	// 全部筛选信息
	private $apiScreenAll;

	// 已筛选信息
	private $apiScreenSelected;

	// 筛选顺序
	private $apiScreenSort;

	/**
	 * 接口调用查询数据
	 * 
	 * @param  array  $data 参数
	 * @return [type]	   [description]
	 */
	public function list(array $data)
	{
		// 全部数据
		$this->apiList = $this->data;

		// 成员属性
		$this->apiSearch		= $data['search'];
		$this->apiScreen		= $data['screen'];
		$this->apiSort			= $data['sort'];
		$this->apiPage			= $data['page'];
		$this->apiScreenAll		= $this->renderData['config']['screen']['group'][0];
		$this->apiScreenSort	= $data['screenSort'] ?? null;

		foreach ($this->apiScreen as $key => $value) {
			$this->apiScreenSelected[$key] = ['type' => $this->apiScreenAll[$key]['type'], 'value' => $value];
		}

		// 处理搜索
		$this->apiHandleSearch();

		// 处理筛选组
		$this->handleScreenStatistics();

		// 处理筛选
		$this->apiHandleScreen();

		// 处理排序
		$this->apiHandleSort();

		return [
			'list' => array_slice($this->apiList, ($this->apiPage[0] - 1) * $this->apiPage[1], $this->apiPage[1]),
			'screen' => $this->apiScreen,
			'screenAll' => $this->apiScreenAll,
			'count' => count($this->apiList)
		];
	}

	/**
	 * 处理搜索
	 */
	private function apiHandleSearch()
	{
		$search = $this->renderData['config']['search'];
		$this->apiList = $this->apiFilter($this->apiList, $search['fields'], $this->apiSearch, $search['caseSensitive'], false);
	}

	/**
	 * 处理筛选组
	 */
	private function handleScreenStatistics()
	{
		$screen = $this->renderData['config']['screen'];

		// 显示数值或隐藏空都需要计算 否则不计算
		if (!$screen['countStatus'] && !$screen['nullHidden']) return false;

		// 搜索后的数据
		$list = $this->apiList;

		// 逐级计算数量
		if ($screen['type'] == 1 || $screen['type'] == 2) {
			// 耗时0.5 - 0.6
			foreach ($this->apiScreenAll as $key => $value) {
				// 根据集合计算筛选组里每个筛选项对应的数据数量
				$this->apiScreenGroupCount($list, $key);

				// 如果这个筛选组生效了 获取筛选后的数据继续循环
				$this->apiScreenEffect($key) && ($list = $this->apiScreenCheckList($list, $key, $this->apiScreenSelected[$key]));
			}
		}

		// 根据用户点击顺序计算 没有点击的按默认顺序
		if ($screen['type'] == 3) {
		 	// 先处理用户筛选过的筛选组
		 	foreach ($this->apiScreenSort as $name) {
			 	// 根据集合计算筛选组里每个筛选项对应的数据数量
				$this->apiScreenGroupCount($list, $name);

				// 如果这个筛选组生效了 获取筛选后的数据继续循环
				$this->apiScreenEffect($name) && ($list = $this->apiScreenCheckList($list, $name, $this->apiScreenSelected[$name]));
			}

			// 再处理没有筛选过的筛选组
			foreach ($this->apiScreenAll as $name => $value) {
				if (in_array($name, $this->apiScreenSort)) continue;

				// 根据集合计算筛选组里每个筛选项对应的数据数量
				$this->apiScreenGroupCount($list, $name);

				 // 如果这个筛选组生效了 获取筛选后的数据继续循环
				$this->apiScreenEffect($name) && ($list = $this->apiScreenCheckList($list, $name, $this->apiScreenSelected[$name]));
			}
		}

		// 计算每个筛选组的数量时都是基于其他筛选组筛选后的结果
		if ($screen['type'] == 4) {
		 	// 处理每一组
		 	foreach ($this->apiScreenAll as $key => $value) {
				$list = $this->apiList;

				// 不处理自己 其他组如果生效了 获取筛选结果
				foreach ($this->apiScreenAll as $key2 => $value2) {
					if ($key == $key2) continue;

					$this->apiScreenEffect($key2) && ($list = $this->apiScreenCheckList($list, $key2, $this->apiScreenSelected[$key2]));
				}

				// 根据集合计算筛选组里每个筛选项对应的数据数量
				$this->apiScreenGroupCount($list, $key);
			}
		}

        // 如果已选择的选项因为数量为0被隐藏了 重置该组
        $this->screenCheckHidden();
	}

	/**
	 * 处理筛选
	 */
	private function apiHandleScreen()
	{
		$screen = $this->renderData['config']['screen'];

		// 如果没开启筛选 退出
		if (!$screen['status']) return false;

		// 条件和结果
		$condition = $list = [];

        // 循环每个筛选组 生效的筛选组放进条件里
        foreach ($this->apiScreen as $key => $value) $condition[$key] = $this->apiScreenSelected[$key];

		// 没有条件则不需要处理 退出
		if (count($condition) == 0) return false;

		// 循环每条数据 判断是否符合条件 符合条件放进数组
		foreach ($this->apiList as $data) $this->apiScreenCheckData($data, $condition) && ($list[] = $data);

		$this->apiList = $list;
	}

	/**
	 * 处理筛选 把筛选后的结果赋值给screenList
	 */
	private function apiHandleSort()
	{
		$this->apiList = $this->apiArraySortByColumn($this->apiList, $this->apiSort['prop'], $this->apiSort['order'], $this->config['sortCaseSensitive']);
	}

	/**
	 * 判断一条数据是否符合条件
	 * 
	 * @param  array  $data	   数据
	 * @param  array  $conditions 条件
	 * @return bool
	 */
	private function apiScreenCheckData(array $data, array $conditions) : bool
	{
		$screen = $this->renderData['config']['screen'];

		// 循环每个条件 判断这条数据是否符合 条件属于并列关系 只要有一个条件不符合就返回false
		foreach ($conditions as $key => $condition) {
			// 值和条件
			$value = $data[$key] ?? null;
			$cuttingSymbol = $this->renderData['config']['cuttingSymbol'];

			// 值不存在时为假
			if (is_null($value)) return false;

			// 不同的筛选类型分别处理
			switch ($this->apiScreenAll[$key]['type']) {
				case 1:
					// 切割值
					$value = is_int($value) ? [$value] : ($cuttingSymbol ? explode($cuttingSymbol, $value) : [$value]);

					// 如果条件的值不在数组里 不通过
					if ($condition['value'] != 'All' && !in_array($condition['value'], $value)) return false;
					break;

				case 2:
					// 切割值
					$value = is_int($value) ? [$value] : ($cuttingSymbol ? explode($cuttingSymbol, $value) : [$value]);

					// 如果值和条件没有交集 不通过
					if (count($condition['value']) > 0 && count(array_intersect($value, $condition['value'])) == 0) return false;
					break;

				case 3:
					// 转小数
					$value = (int) $value;

					// 如果是范围 判断在不在范围内 否则判断是否相等
					if ($this->apiScreenAll[$key]['config']['range'] === true) {
						if ($value < $condition['value'][0] || $value > $condition['value'][1]) return false;
					} else {
						if ($value != $condition['value']) return false;
					}
					break;

				case 4:
					// 转时间戳并获取条件的日期范围
					$value = is_int($value) ? $value : strtotime($value);
					$date = $this->apiDateRange($condition['value'], $condition['config']['type']);

					// 如果是天多选 循环每一天 判断有没有合法的 否则判断在不在范围内
					if ($this->apiScreenAll[$key]['config']['type'] == 'dates') {
						$status = false;
						foreach ($date as $day) $value >= $day[0] && $value <= $day[1] && ($status = true);
						return $status;
					} else {
						if ($value < $date[0] || $value > $date[1]) return false;
					}
					break;

				case 5:
					// 切割值
					$value = is_int($value) ? [$value] : ($cuttingSymbol ? explode($cuttingSymbol, $value) : [$value]);

					// 如果是天多选 循环每一天 判断有没有合法的 否则判断在不在范围内
					if ($this->apiScreenAll[$key]['config']['multiple'] === true) {
						// 如果值和条件没有交集 不通过
						if (count($condition['value']) > 0 && count(array_intersect($value, $condition['value'])) == 0) return false;
					} else {
						// 如果条件的值不在数组里 不通过
						if ($condition['value'] != 'All' && in_array($condition['value'], $value)) return false;
					}
					break;
			}
		}

		return true;
	}

    /**
     * 根据集合计算筛选组里每个筛选项对应的数据数量
     * 
     * @param  array  $list 数据
     * @param  string $name 筛选项名称
     */
    private function apiScreenGroupCount(array $list, string $name)
    {
		$screen = $this->renderData['config']['screen'];

        if (!in_array($this->apiScreenAll[$name]['type'], $screen['groupCountType'])) return false;

        foreach ($this->apiScreenAll[$name]['list'] as $key => $value) {
        	$value = ['type' => $this->apiScreenAll[$name]['type'], 'value' => $value['name']];
        	($value['type'] == 2 || ($this->apiScreenAll[$name]['config']['multiple'] ?? null) === true) && ($value['value'] = [$value['value']]);
        	$this->apiScreenAll[$name]['list'][$key]['count'] = count($this->apiScreenCheckList($list, $name, $value));
        }
    }

	/**
	 * 判断一个筛选组是否进行了筛选
	 * 
	 * @param  string $name 筛选组名字
	 * @return bool
	 */
	private function apiScreenEffect(string $name) {
		return array_key_exists($name, $this->apiScreen);
	}

    /**
     * 获取列表里某个属性符合某个值的集合
     * 
     * @param  array  $list  数据列表
     * @param  string $name  属性
     * @param  mixed  $value 值
     * @return array
     */
    private function apiScreenCheckList(array $list, string $name, $value) : array
    {
    	$list2 = [];
    	foreach ($list as $key => $data) $this->apiScreenCheckData($data, [$name => $value]) && ($list2[] = $data);
    	return $list2;
    }

    /**
     * 判断筛选组的已选择项是否被隐藏 如果隐藏重置该组
     */
    private function screenCheckHidden()
    {
		$screen = $this->renderData['config']['screen'];

        // 如果不隐藏 不执行
        if (!$screen['nullHidden']) return false;

        // 是否需要重新加载
        $reload = false;

        // 循环全部筛选组
        foreach ($this->apiScreenAll as $key => $data) {
            // 获取这一组当前的筛选值
            if (isset($this->apiScreen[$key])) {
            	$value = $this->apiScreen[$key];
            } else {
            	continue;
            }

            // 判断筛选类型
            switch ($data['type']) {
            	case 1:
                    // 全选不处理
                    if ($value == 'All') continue 2;

                    // 单选 获取这个值的数量 如果为0 重置 重载
                    if ($this->apiScreenGroupItemCount($key, $value) == 0) {
                    	unset($this->apiScreen[$key]);
                    	$reload = true;
                    }
            		break;

            	case 2:
                    // 不选不处理
                    if (count($value) == 0) continue 2;

                    // 循环每个值 把有数据的值放进新数组里
                    $value2 = [];
                    foreach ($value as $item) $this->apiScreenGroupItemCount($key, $item) > 0 && ($value2[] = $item);

                    // 数量相同说明没有隐藏的项 不需要处理
                    if (count($value) == count($value2)) continue 2;

                    // 重置成新数组 重载
                    if (count($value2) == 0) {
                    	unset($this->apiScreen[$key]);
                    } else {
                    	$this->apiScreen[$key] = $value2;
                    }
                    $reload = true;
            		break;

                case 5:
                    // 如果是多选
                    if (($this->screenAll[$key]['config']['multiple'] ?? null) === true) {
	                    // 不选不处理
	                    if (count($value) == 0) continue 2;

	                    // 循环每个值 把有数据的值放进新数组里
	                    $value2 = [];
	                    foreach ($value as $item) $this->apiScreenGroupItemCount($key, $item) > 0 && ($value2[] = $item);

	                    // 数量相同说明没有隐藏的项 不需要处理
	                    if (count($value) == count($value2)) continue 2;

	                    // 重置成新数组 重载
	                    if (count($value2) == 0) {
	                    	unset($this->apiScreen[$key]);
	                    } else {
	                    	$this->apiScreen[$key] = $value2;
	                    }
	                    $reload = true;
                    } else {
	                    // 全选不处理
	                    if ($value == 'All') continue 2;

	                    // 单选 获取这个值的数量 如果为0 重置 重载
	                    if ($this->apiScreenGroupItemCount($key, $value) == 0) {
	                    	unset($this->apiScreen[$key]);
	                    	$reload = true;
	                    }
                    }
                    break;
            }
        }

        $reload && $this->handleScreenStatistics();
    }

    /**
     * 获取一个筛选组中某一项的数据数量
     * 
     * @param  string $group 组
     * @param  string $item  项
     * @return int
     */
    private function apiScreenGroupItemCount(string $group, string $item) : int
    {
        // 循环这一组里全部项
        foreach ($this->apiScreenAll[$group]['list'] as $key => $value) {
            // 如果找到这个名字 返回数量
            if ($value['name'] == $item) return $value['count'];
        }
    }

	/**
	 * 过滤一个列表 判断指定属性是否包含指定值
	 * 
	 * @param  array   $list	 被过滤的列表
	 * @param  array   $keys	 属性列表
	 * @param  string  $value	判断的值
	 * @param  boolean $isCase   大小写敏感
	 * @param  boolean $isStrict 严格模式 开启后值必须相等 不开启时包含即可
	 * @return array			过滤后的值
	 */
	private function apiFilter(array $list, array $keys, $value, bool $isCase = false, bool $isStrict = false) : array
	{
		// 结果
		$result = [];

		// 循环每条数据
		foreach ($list as $item) {
			// 循环每个字段
			foreach ($keys as $key) {
				// 列表中的值
				$data = strval($item[$key]);

				// 如果大小写不敏感 全部转小写
				$isCase || (($data = strtolower($data)) && ($value = strtolower($value)));

				if ($isStrict) {
					// 严格模式下值相等 放进结果里 终止循环
					if ($data === $value) {
						$result[] = $item;
						break;
					}
				} else {
					// 非严格模式下值包含 放进结果里 终止循环
					if (strstr($data, $value)) {
						$result[] = $item;
						break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * 获取时间戳对应时间类型的时间戳范围
	 * @param  mined  $data 时间
	 * @param  string $type 时间类型
	 * @return mined
	 */
	private function apiDateRange($data, string $type)
	{
		// 如果值是数组且类型不是这两种 一般为开始时间和结束时间 转数字返回即可
		if (is_array($data) && $type != 'dates' && $type != 'monthrange') {
			$data[0] = (int) $data[0];
			$data[1] = (int) $data[1];
			return $data;
		}

		// 天多选选择器
		if ($type == 'dates') {
			// 循环每个时间戳 获取当天 00:00:00 和 23:59:59 的时间戳
			foreach ($data as $key => $value) {
				$data[$key] = [strtotime(date('Y-m-d 00:00:00', $value)), strtotime(date('Y-m-d 23:59:59', $value))];
			}

			return $data;
		}

		// 月范围选择器
		if ($type == 'monthrange') {
			// 开始时间当月的第一秒
			$data[0] = strtotime(date('Y-m-01 00:00:00', $data[0]));

			// 结束时间当月的最后一秒
			$data[1] = strtotime(date('Y-m-t 23:59:59', $data[1]));

			return $data;
		}

		// 剩下的情况$data为时间戳

		// 获取时间信息
		switch ($type) {
			case 'year':
				// 当年的第一秒和最后一秒
				return [strtotime(date('Y-01-01 00:00:00', $data)), strtotime(date('Y-12-31 23:59:59', $data))];

			case 'month':
				// 当月的第一秒和最后一秒
				return [strtotime(date('Y-m-01 00:00:00', $data)), strtotime(date('Y-m-t 23:59:59', $data))];

			case 'date':
				// 当日的第一秒和最后一秒
				return [strtotime(date('Y-m-d 00:00:00', $data)), strtotime(date('Y-m-d 23:59:59', $data))];

			case 'datetime':
				// 这个是选择了一个时间点 直接返回
				return [$data, $data];

			case 'week':
				// 开始时间往后数一星期
				return [$data, $data + 86400 * 7 - 1];
		}
	}

	/**
	 * 二维数组根据某一列排序
	 * @param  array  $array  二维数组
	 * @param  string $column 列
	 * @param  string $sort   顺序
	 * @param  bool   $case   大小写敏感
	 * @return array
	 */
	private function apiArraySortByColumn(array $array, string $column, string $sort, bool $case) : array
	{
		$attr = $column;

		if (!$case) {
			foreach ($array as $key => $value) {
				$array[$key]['old_value'] = $value[$attr];
				$array[$key][$attr] = strtolower($value[$attr]);
			}
		}

		$column = array_column($array, $column);
		array_multisort($column, $sort == 'desc' ? SORT_DESC : SORT_ASC, $array);

		if (!$case) {
			foreach ($array as $key => $value) {
				$array[$key][$attr] = $value['old_value'];
				unset($array[$key]['old_value']);
			}
		}

		return $array;
	}
}