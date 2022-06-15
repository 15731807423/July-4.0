const translate = {
	url: '',

	loading: '',

	message: '',

	success: (data) => {
		console.log(data)
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		return this;
	},

	createAll(url, success = null) {
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
            	this.getAll(response.data);
            }
        }).catch(err => {
            loading.close();
            console.error(err);
            this.$message.error('发生错误，可查看控制台');
        });
	},

	createBatch(url, data, success = null) {
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
            	this.getBatch(response.data);
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

	getAll(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为 ' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/result', data).then(response => {
	            loading.close();
	            var result = response.data;

	            if (result instanceof Object) {
	            	var status = [];

	            	for (let key in result) status.push(key + ':' + result[key]);

	            	status = status.join('、');

		            if (this.inObject('ready', result) || this.inObject('translating', result)) {
		            	for (let key in result) {
		            		if (result[key] == 'translate' || result[key] == 'error') {
		            			data[key].result = result[key];
		            		}
		            	}
		            	this.getAll(data, status, i + 1);
		            } else {
		            	this.message.success('翻译完成，' + status);
		            	this.success(result);
		            }
	            } else if (typeof result === 'string') {
	            	this.message.error(result);
	            }
	        }).catch(err => {
	            loading.close();
	            console.error(err);
	            this.$message.error('发生错误，可查看控制台');
	        });
		}, 2000);
	},

	getBatch(data, status = null, i = 1) {
		const loading = this.loading({
			lock: true,
			text: (status ? '第' + (i - 1) + '次结果为' + status + '，' : '') + '开始第' + i + '次获取结果 ...',
			background: 'rgba(255, 255, 255, 0.7)',
		});

		setTimeout(() => {
			axios.post('/manage/translate/result', data).then(response => {
	            loading.close();
	            if (typeof response.data == 'string') {
	            	if (response.data == 'error') {
	            		this.message.error('翻译失败');
	            	} else if (response.data == 'ready' || response.data == 'translating') {
	            		this.getBatch(data, response.data, i + 1);
	            	} else {
	            		this.message.error(response.data);
	            	}
	            } else {
	            	this.message.success('翻译成功');
	    			this.success(response.data);
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
	            if (typeof response.data == 'string') {
	            	if (response.data == 'error') {
	            		this.message.error('翻译失败');
	            	} else if (response.data == 'ready' || response.data == 'translating') {
	        			this.getTpl(data, response.data, i + 1);
	            	} else {
	            		this.message.error(response.data);
	            	}
	            } else {
	            	this.message.success('生成模板成功');
	    			this.success(response.data);
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