const translate = {
	url: '',

	loading: '',

	message: '',

	nodes: [],

	success: (data) => {
		console.log(data)
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		return this;
	},

	createBatch(url, nodes, success = null) {
		this.nodes = nodes;

		if (success != null) this.success = success;

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post(url, { nodes: nodes }).then(response => {
            loading.close();
            if (typeof response.data == 'string') {
                this.message.error(response.data);
            } else {
            	this.getBatch(response.data);
            }
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	createPage(url, data, success = null) {
		if (success != null) this.success = success;

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post(url, data).then(response => {
            loading.close();
            if (typeof response.data == 'string') {
                this.message.error(response.data);
            } else {
            	this.getPage(response.data);
            }
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	createTpl(url, success = null) {
		if (success != null) this.success = success;

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		axios.post(url, {}).then(response => {
            loading.close();
            if (typeof response.data == 'string') {
                this.message.error(response.data);
            } else {
            	this.getTpl(response.data);
            }
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

		data.nodes = this.nodes;

		setTimeout(() => {
			axios.post('/manage/translate/result', data).then(response => {
	            loading.close();
	            var result = response.data;

	            if (result instanceof Object) {
	            	var status = [], complete = true;

	            	for (let key in result.lang) {
	            		if (!result.lang[key].result) continue;
	            		status.push(key + ':' + result.lang[key].result);
	            	}

	            	status = status.join('、');

	            	for (let key in result.lang) {
	            		if (result.lang[key].complete === false) complete = false;
	            	}

	            	if (complete) {
		            	this.message.success('翻译完成，' + status);
		            	this.success(result);
	            	} else {
	            		this.getBatch(result, status, i + 1);
	            	}
	            } else if (typeof result == 'string') {
	            	this.$message.error(result);
	            }
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	getPage(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/result', data).then(response => {
	            loading.close();
	            var result = response.data;

	            if (result instanceof Object) {
	            	var status = result.result.result, complete = result.result.complete;

	            	if (complete) {
		            	this.message.success(status);
		            	this.success(result.result.content);
	            	} else {
	            		this.getPage(result, status, i + 1);
	            	}
	            } else if (typeof result == 'string') {
	            	this.$message.error(result);
	            }
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	getTpl(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/result', data).then(response => {
	            loading.close();
	            var result = response.data;

	            if (result instanceof Object) {
	            	var status = result.result.result, complete = result.result.complete;

	            	if (complete) {
		            	this.message.success(status);
	            	} else {
	            		this.getTpl(result, status, i + 1);
	            	}
	            } else if (typeof result == 'string') {
	            	this.$message.error(result);
	            }
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