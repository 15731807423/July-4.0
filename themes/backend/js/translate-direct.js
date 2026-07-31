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
			message: message,
			type: 'error',
			duration: 0,
			showClose: true
		});
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
			success ? success(data) : '';
		}

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
			const data = response.data;

			// 如果翻译失败 弹出错误信息
			if (!data.status) {
				this.error((code ? '[' + code + '] ' : '') + data.message);
				return false;
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
		}).catch(err => {
			loading.close();
			console.error(err);
			this.error(
				err.response && err.response.data && err.response.data.message
					? err.response.data.message
					: '翻译接口调用失败'
			);
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
            var data = response.data;
            data.status ? this.success(data.data) : this.error(data.message);
        }).catch(err => {
            loading.close();
            console.error(err);
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
			success ? success(data) : '';
		}

		const loading = this.loading({
			lock: true,
			text: '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/direct/tpl', { code: code }).then(response => {
            loading.close();
            var data = response.data;

        	// 弹出结果 执行回调函数
        	data.status ? this.success(data.data || {}) : this.error(data.message);
        }).catch(err => {
            loading.close();
            console.error(err);
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
