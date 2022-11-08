const translate = {
	url: '',

	loading: '',

	message: '',

	nodes: [],

	success: (data) => {
		console.log(data)
	},

	error: (message = '翻译失败') => {
		this.message.error(message);
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		return this;
	},

	batch(nodes, success = null) {
		this.nodes = nodes;

		this.success = function (data) {
			var status = [];
			for (let key in data) {
				status.push(key + '：' + data[key].message);
			}
			this.message.success(status.join('、'));
			success ? success(data) : '';
		}

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/task/batch', { nodes: nodes }).then(response => {
			loading.close();
			var status = response.data;

			if (!status.status) {
				this.message.error(status.message);
				return false;
			}

			this.getBatch({ nodes: nodes, data: status.data });
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	getBatch(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为 ' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/task/batch/result', data).then(response => {
	            loading.close();
	            var status = response.data;
	            data.data = status.data || data.data;

	            if (status.status === null) this.getBatch(data, status.message, i + 1);
	            if (status.status === true) this.success(status.data);
	            if (status.status === false) this.error(status.message);
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	page(data, success = null) {
		if (success != null) this.success = success;

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/task/page', data).then(response => {
			loading.close();
			var status = response.data;

			if (!status.status) {
				this.message.error(status.message);
				return false;
			}

			data.data = status.data;

			this.getPage(data);
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	getPage(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/task/page/result', data).then(response => {
	            loading.close();
	            var status = response.data;
	            data.data = status.data || data.data;

	            if (status.status === null) this.getPage(data, status.message, i + 1);
	            if (status.status === true) this.success(status.data);
	            if (status.status === false) this.error(status.message);
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	tpl(code, success = null) {
		this.success = function (data) {
			this.message.success(data.message ? data.message : '翻译成功');
			success ? success(data) : '';
		}

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post('/manage/translate/task/tpl', { code: code }).then(response => {
			loading.close();
			var status = response.data;

			if (!status.status) {
				this.message.error(status.message);
				return false;
			}

			this.getTpl({ code: code, data: status.data });
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	getTpl(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/task/tpl/result', data).then(response => {
	            loading.close();
	            var status = response.data;
	            data.data = status.data || data.data;

	            if (status.status === null) this.getTpl(data, status.message, i + 1);
	            if (status.status === true) this.success(status.data || {});
	            if (status.status === false) this.error(status.message);
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	inObject(value, object) {
		for (let key in object) {
			if (object[key] === value) return true;
		}
		return false;
	}
};