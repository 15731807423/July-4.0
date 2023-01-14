LazyScript.load('jquery', function (global) {
	function ScrollPopup(data) {
		// 解析参数
		data.form 				= parseValue(data.form, 'form');
		data.once 				= parseValue(data.once, 'once');
		data.click 				= parseValue(data.click, 'click');
		data.cycle 				= parseValue(data.cycle, 'cycle');
		data.slideDown			= parseValue(data.slideDown, 'slideDown');
		data.slideUp 			= parseValue(data.slideUp, 'slideUp');
		data.timeout 			= parseValue(data.timeout, 'timeout');
		data.stopClose 			= parseValue(data.stopClose, 'stopClose');
		data.stopSubmit 		= parseValue(data.stopSubmit, 'stopSubmit');
		data.frequency			= parseValue(data.frequency, 'frequency');
		data.closeButton		= parseValue(data.closeButton, 'closeButton');
		data.switchType 		= parseValue(data.switchType, 'switchType');
		data.switchShow 		= parseValue(data.switchShow, 'switchShow');
		data.switchHide 		= parseValue(data.switchHide, 'switchHide');
		data.animationDuration	= parseValue(data.animationDuration, 'animationDuration');
		data.mask 				= parseValue(data.mask, 'mask');
		data.before				= parseValue(data.before, 'before');

		// 确定选择器
		if (!data.selector) throw '必须指定选择器';

		// 弹出窗口元素 是否弹出过 是否下滑弹出过 是否上滑弹出过 是否延迟弹出过 上滑的距离
		// 该页面是否由于关闭过或提交过而不显示弹窗 显示时间存cookie的名字 提交事件存cookie的名字 冻结次数存cookie的名字 周期内访问量存cookie的名字
		// 概率的结果 显示的时间 提交的时间 冻结的次数 本周期内信息 允许下滑的高度
		var popup = $(data.selector),
			onceShowed = false,
			downShowed = false,
			upShowed = false,
			timeoutShowed = false,
			upSlidingDistance = 0,
			frozen = false,
			cookiePopupShowTime = 'popupShowTime',
			cookiePopupSubmitTime = 'popupSubmitTime',
			cookiePopupFrozenTimes = 'popupFrozenTimes',
			cookiePopupCycleVisits = 'popupCycleVisits',
			frequency = random(0, 100) < data.frequency,
			showTime = getShowTime(),
			submitTime = getSubmitTime(),
			frozenTimes = getFrozenTimes(),
			cycleVisits = getCycleVisits();

		// 如果没有弹窗元素 说明这个页面没有该功能
		if (popup.length == 0) return false;

		// 设置周期内访问量
		setCycleVisits();

		// 如果指定了表单 监听提交事件
		if (data.form !== null) $(data.form, popup).submit(e => this.submit());

		// 如果配置了遮罩层 处理数据
		if (data.mask !== null) {
			if (data.mask.selector) {
				// 选择现有的遮罩层
				var mask = $(data.mask.selector);
			} else {
				// 创建新的遮罩层
				var mask = $('<div style="width: 100vw; height: 100vh; display: none; position: fixed; top: 0; left: 0; z-index: ' + data.mask.zIndex + '; background: ' + data.mask.background + '; opacity: ' + data.mask.opacity + '"></div>');
				$('body').append(mask);
			}

			// 点击遮罩层隐藏弹窗
			if (data.mask.close) mask.click(e => this.hide(popup, mask));
		}

		// 关闭事件
		if (data.closeButton !== null) $(data.closeButton).click(e => this.hide());

		// 点击事件
		if (data.click !== null) $(data.click).click(e => this.show());

		// 提交过并且设置了提交后的限制
		if (data.stopSubmit !== null && submitTime !== null) {
			// 永久禁用
			data.stopSubmit === true && (frozen = true);

			// 设置时间并且还没到过期时间
			data.stopSubmit[1] == 's' && submitTime + data.stopSubmit[0] * 1000 > getTime() && (frozen = true);

			// 设置次数并且还没到规定次数
			data.stopSubmit[1] == 't' && frozenTimes < data.stopSubmit[0] && (frozen = true, setFrozenTimes(frozenTimes + 1));
		}

		// 显示过并且设置了显示后的限制
		if (data.stopClose !== null && showTime !== null) {
			// 永久禁用
			data.stopClose === true && (frozen = true);

			// 设置时间并且还没到过期时间
			data.stopClose[1] == 's' && showTime + data.stopClose[0] * 1000 > getTime() && (frozen = true);

			// 设置次数并且还没到规定次数
			data.stopClose[1] == 't' && (frozenTimes < data.stopClose[0] ? (frozen = true, setFrozenTimes(frozenTimes + 1)) : setFrozenTimes(0));
		}

		// 下滑触发
		const down = height => {
			// 不让弹
			if (!common(data.slideDown, downShowed)) return false;

			// 高度到了 弹
			if (height > data.slideDown) {
				downShowed = true;
				onceShowed = true;
				this.show();
			}
		}

		// 上滑触发
		const up = height => {
			// 不让弹
			if (!common(data.slideUp, upShowed)) return false;

			// 延迟 弹
			if ($.type(data.slideUp) == 'string') {
				setTimeout(() => (upShowed = true, onceShowed = true, this.show()), parseFloat(data.slideUp) * 1000);
			}

			// 高度到了 弹
			if ($.type(data.slideUp) == 'number' && upSlidingDistance > data.slideUp) {
				upShowed = true;
				onceShowed = true;
				this.show();
			}
		}

		// 延迟触发
		const timeout = () => {
			// 不让弹
			if (!common(data.timeout, timeoutShowed)) return false;

			// 弹
			setTimeout(() => (timeoutShowed = true, onceShowed = true, this.show()), data.timeout * 1000);
		}

		// 调用下滑触发和上滑触发
		var scrollTop = $(window).scrollTop();
		$(window).scroll(e => {
			var current = $(window).scrollTop(), change = current - scrollTop;

			if (change > 0) {
				upSlidingDistance = 0;
				down(current);
			} else {
				upSlidingDistance += change * -1;
				up(current);
			}

			scrollTop = current;
		});

		// 调用延迟触发
		timeout();

		// 解析参数
		function parseValue(value, name) {
			let type = $.type(value), height = $(document).height() - $(window).innerHeight();

			switch (name) {
				case 'form':
					if (type == 'string') return value;
					break;

				case 'once':
					if (type == 'boolean') return value;
					return false;
					break;

				case 'click':
					if (type == 'string') return value;
					break;

				case 'cycle':
					if (type == 'number' && value > 0) return value;
					break;

				case 'slideDown':
					if (type == 'function') return parseValue(eval('(' + value + ')(' + height + ')'), name);
					if (type == 'number') {
						if (value >= 0 && value <= 1) return height * value;
						if (value > 1) return value;
					}
					break;

				case 'slideUp':
					if (type == 'function') return parseValue(eval('(' + value + ')(' + height + ')'), name);
					if (type == 'number') {
						if (value >= 0 && value <= 1) return height * value;
						if (value > 1) return value;
					}
					if (type == 'string' && value.substr(-1) == 's') {
						value = parseInt(value.substr(0, value.length - 1));
						if (!isNaN(value)) return value.toString();
					}
					break;

				case 'timeout':
					if (type == 'number' && value >= 0) return value;
					break;

				case 'stopClose':
					if (value === true) return true;
					if (type == 'string') {
						if (value.substr(-1) == 's') {
							value = parseInt(value.substr(0, value.length - 1));
							if (!isNaN(value) && value > 0) return [value, 's'];
						}
						if (value.substr(-1) == 't') {
							value = parseInt(value.substr(0, value.length - 1));
							if (!isNaN(value) && value > 0) return [value, 't'];
						}
					}
					break;

				case 'stopSubmit':
					if (value === true) return true;
					if (type == 'array') {
						if (value.substr(-1) == 's') {
							value = parseInt(value.substr(0, value.length - 1));
							if (!isNaN(value) && value > 0) return [value, 's'];
						}
						if (value.substr(-1) == 't') {
							value = parseInt(value.substr(0, value.length - 1));
							if (!isNaN(value) && value > 0) return [value, 't'];
						}
					}
					break;

				case 'frequency':
					if (type == 'number' && value >= 0 && value <= 100) return value;
					return 100;
					break;

				case 'closeButton':
					if (type == 'string') return value;
					break;

				case 'switchType':
					if (value == 'class') return 'class';
					if (value == 'css') return 'css';
					return 'css';
					break;

				case 'switchShow':
					if (data.switchType == 'class') {
						if (type == 'string') return value;
						return '';
					}

					if (data.switchType == 'css') {
						if ($.type(value) == 'array' && $.type(value[0]) == 'string' && $.type(value[1]) == 'string') return [value[0], value[1]];
						return ['display', 'block'];
					}
					break;

				case 'switchHide':
					if (data.switchType == 'class') {
						if (type == 'string') return value;
						return '';
					}

					if (data.switchType == 'css') {
						if ($.type(value) == 'array' && $.type(value[0]) == 'string' && $.type(value[1]) == 'string') return [value[0], value[1]];
						return ['display', 'none'];
					}
					break;

				case 'animationDuration':
					if (data.switchType == 'css' && type == 'number' && value > 0) return value;
					break;

				case 'mask':
					if (type == 'object') {
						return {
							selector: parseValue(value.selector, 'mask.selector'),
							zIndex: parseValue(value.zIndex, 'mask.zIndex'),
							opacity: parseValue(value.opacity, 'mask.opacity'),
							background: parseValue(value.background, 'mask.background'),
							close: parseValue(value.close, 'mask.close')
						};
					}
					break;

				case 'mask.selector':
					if (type == 'string') return value;
					break;

				case 'mask.zIndex':
					if (type == 'number') return value;
					return 9999;
					break;

				case 'mask.opacity':
					if (type == 'number' && value >= 0 && value <= 1) return value;
					return .5;
					break;

				case 'mask.background':
					if (type == 'string') return value;
					return 'black';
					break;

				case 'mask.close':
					if (type == 'boolean') return value;
					return false;
					break;

				case 'before':
					if (type == 'function') return value;
					return null;
					break;
			}

			return null;
		}

		// 生成范围内的随机数
		function random(min, max) {
			return min < 0 || max < 0 || min > max ? null : parseInt(Math.random() * (max - min + 1));
		}

		// 获取当前时间
		function getTime() {
			return (new Date()).getTime();
		}

		// 设置显示时间
		function setShowTime() {
			cookie({ [cookiePopupShowTime]: (new Date()).getTime().toString() });
		}

		// 获取显示时间
		function getShowTime() {
			return cookie(cookiePopupShowTime);
		}

		// 设置提交事件
		function setSubmitTime() {
			cookie({ [cookiePopupSubmitTime]: (new Date()).getTime().toString() });
		}

		// 获取提交时间
		function getSubmitTime() {
			return cookie(cookiePopupSubmitTime);
		}

		// 设置冻结次数
		function setFrozenTimes(times) {
			cookie({ [cookiePopupFrozenTimes]: times.toString() });
		}

		// 获取冻结次数
		function getFrozenTimes() {
			return cookie(cookiePopupFrozenTimes, 0)
		}

		// 设置周期内访问量 新周期从0开始或+1
		function setCycleVisits() {
			cycleVisits[0] = data.cycle === null || cycleVisits[1] + data.cycle * 1000 > getTime() ? cycleVisits[0] + 1 : 1;
			cycleVisits[1] = getTime();
			cookie({ [cookiePopupCycleVisits]: JSON.stringify(cycleVisits) });
		}

		// 获取周期内访问量
		function getCycleVisits() {
			return JSON.parse(cookie(cookiePopupCycleVisits, JSON.stringify([0, 0])));
		}

		// 操作cookie
		function cookie(name, value = null) {
			if ($.type(name) == 'object') {
				for (let key in name) {
					if ($.type(key) != 'string') continue;
					value = name[key];
					if ($.type(value) == 'null') value = '';
					if ($.type(value) == 'number') value = value.toString();
					if ($.type(value) != 'string') continue;
					document.cookie = key + '=' + value + (value.length == 0 ? '; expires=' + (new Date(0)) : '');
				}
			}

			if ($.type(name) == 'string') {
				var cookieList = document.cookie.split('; '), cookie = {};
				for (let i = 0; i < cookieList.length; i++) {
					let separator = cookieList[i].indexOf('=');
					cookie[cookieList[i].substr(0, separator)] = cookieList[i].substr(separator + 1);
				}

				for (let key in cookie) {
					if (key == name) return isNaN(parseInt(cookie[key])) ? cookie[key] : parseInt(cookie[key]);
				}

				return value;
			}
		}

		// 判断能不能弹 通用部分
		function common(value, showed) {
			// 没设置 不弹
			if (value === null) return false;

			// 弹过了 不弹
			if (showed) return false;

			// 没随机到 不弹
			if (!frequency) return false;

			// 关闭过或提交过 不弹
			if (frozen) return false;

			// 只能弹一次且弹过了 不弹
			if (data.once && onceShowed) return false;

			// 弹之前执行函数返回值为false 不弹
			if (data.before) {
				var a = eval('(' + data.before + ')()');
				if (!a) return false;
			}

			// 弹
			return true;
		}

		// 显示
		this.show = () => {
			showed = true;
			setShowTime();

			if (data.switchType == 'class') {
				popup.removeClass(data.switchHide).addClass(data.switchShow);
			}

			if (data.switchType == 'css') {
				var e = popup.css(data.switchHide[0], '').css(data.switchShow[0], data.switchShow[1]);
				data.animationDuration && e.css('opacity', 0).animate({ opacity: 1 }, data.animationDuration * 1000);
			}

			mask && mask.css('display', 'block');

			return false;
		}

		// 隐藏
		this.hide = () => {
			if (data.switchType == 'class') {
				popup.removeClass(data.switchShow).addClass(data.switchHide);
			}

			if (data.switchType == 'css') {
				data.animationDuration ? popup.css('opacity', 1).animate({ opacity: 0 }, data.animationDuration * 1000, () => {
					popup.css(data.switchShow[0], '').css(data.switchHide[0], data.switchHide[1]);
				}) : popup.css(data.switchShow[0], '').css(data.switchHide[0], data.switchHide[1]);
			}

			mask && mask.css('display', 'none');

			return false;
		}

		// 提交
		this.submit = () => {
			setSubmitTime();
		}
	}

	global.ScrollPopup = ScrollPopup;
});