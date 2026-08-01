const translate = {
	url: '',

	loading: '',

	message: '',

	nodes: [],

	batchCodes: [],

	batchIndex: 0,

	batchResults: [],

	success(data) {
		console.log(data)
	},

	error(message = '翻译失败') {
		this.message({
			message: typeof message === 'string' && message.trim() ? message : '翻译失败',
			type: 'error',
			duration: 0,
			showClose: true
		});
	},

	getErrorMessage(error, fallback = '翻译接口调用失败') {
		if (!error || !error.response) {
			return error && error.code === 'ECONNABORTED'
				? '翻译请求超时，请检查网络后重试'
				: '网络连接异常，请检查网络后重试';
		}

		const data = error.response.data;
		if (data && typeof data.message === 'string' && data.message.trim()) {
			return data.message;
		}

		if (data && data.errors && typeof data.errors === 'object') {
			const keys = Object.keys(data.errors);
			if (keys.length) {
				const first = data.errors[keys[0]];
				if (Array.isArray(first) && typeof first[0] === 'string') return first[0];
				if (typeof first === 'string') return first;
			}
		}

		return fallback;
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		return this;
	},

	// 直接翻译 获取翻译结果
	batch(nodes, success = null, codes = []) {
		this.nodes = nodes;
		this.batchCodes = Array.isArray(codes) && codes.length ? codes : [null];
		this.batchIndex = 0;
		this.batchResults = [];

		this.success = function (data) {
			// var status = [];
			// for (let key in data) {
			// 	status.push(key + '：' + data[key].message);
			// }
			this.message({
				message: data,
				type: 'success',
				duration: 0,
				showClose: true
			});
			if (success) success(data);
		};

		this.runBatch();
	},

	runBatch() {
		const code = this.batchCodes[this.batchIndex];
		const loading = this.loading({
			lock: true,
			text: (code ? '[' + code + '] ' : '') + '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});
		const payload = { nodes: this.nodes };
		if (code) payload.code = code;

		// 直接翻译 获取翻译结果
		axios.post('/manage/translate/direct/batch', payload).then(response => {
			loading.close();
			const data = response && response.data ? response.data : {};

			// 如果翻译失败 弹出错误信息
			if (data.status !== true) {
				this.error((code ? '[' + code + '] ' : '') + (data.message || '翻译失败'));
				return;
			}

			this.batchResults.push(code);
			this.batchIndex++;
			if (this.batchIndex < this.batchCodes.length) {
				this.runBatch();
				return;
			}

			const completed = this.batchResults.filter(item => item);
			this.success(completed.length
				? '已完成 ' + completed.length + ' 种语言：' + completed.join('、')
				: data.data);
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	// 编辑页面翻译文本内容
	page(data, success = null) {
		this.success = success || this.success;

		const loading = this.loading({
			lock: true,
			text: '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		// 编辑页面翻译文本内容
		axios.post('/manage/translate/direct/page', data).then(response => {
			loading.close();
			const status = response && response.data ? response.data : {};
			status.status
				? this.success(status.data)
				: this.error(status.message || '页面翻译失败');
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	// 翻译模板文件并创建文件
	tpl(code, success = null) {
		this.success = function (data) {
			this.message({
				message: data.message ? data.message : '翻译成功',
				type: 'success',
				duration: 0,
				showClose: true
			});
			if (success) success(data);
		};

		const loading = this.loading({
			lock: true,
			text: '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/direct/tpl', { code: code }).then(response => {
			loading.close();
			const status = response && response.data ? response.data : {};

			// 弹出结果 执行回调函数
			status.status
				? this.success(status.data || {})
				: this.error(status.message || '模板翻译失败');
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	inObject(value, object) {
		for (let key in object) {
			if (object[key] === value) return true;
		}
		return false;
	},

	result(status) {
		switch (status) {
			case true:
				return '成功';

			case false:
				return '失败';

			default:
				return '未知状态';
		}
	}
};
