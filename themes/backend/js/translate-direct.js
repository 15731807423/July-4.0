const translate = {
	url: '',

	loading: '',

	message: '',

	nodes: [],

	success: data => {
		// console.log(data)
	},

	error: (message = '翻译失败') => {
		this.message.error(message);
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		return this;
	},

	// 直接翻译 获取翻译结果
	batch(nodes, success = null) {
		this.nodes = nodes;
		this.success = success || this.success;

		const loading = this.loading({
			lock: true,
			text: '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		// 直接翻译 获取翻译结果
		axios.post('/manage/translate/direct/batch', { nodes: nodes }).then(response => {
            loading.close();
            // 准备需要的变量
            var data = response.data, status = [];

            // 如果翻译失败 弹出错误信息
            if (!data.status) {
            	this.message.error(data.message);
            	return false;
            }

            // 整理每个语言的结果
        	for (let key in data.data) {
        		status.push(key + ':' + data.data[key].message);
        	}

        	// 弹出结果 执行回调函数
        	this.message.success('翻译完成，' + status.join('、'));
        	this.success(data);
        }).catch(err => {
            loading.close();
            console.error(err);
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
            data.status ? this.success(data.data) : this.error();
        }).catch(err => {
            loading.close();
            console.error(err);
        });
	},

	// 翻译模板文件并创建文件
	tpl(code, success = null) {
		this.success = success || this.success;

		const loading = this.loading({
			lock: true,
			text: '开始翻译 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/direct/tpl', { code: code }).then(response => {
            loading.close();
            var data = response.data;

        	// 弹出结果 执行回调函数
        	data.status ? this.message.success('翻译完成') : this.message.error('翻译失败');
            data.status ? this.success(data) : this.error();
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