const translate = {
	url: '',

	loading: '',

	message: '',

	nodes: [],

	batchCodes: [],

	batchIndex: 0,

	batchResults: [],

	pollOptions: {
		initialDelay: 2000,
		maxDelay: 10000,
		maxAttempts: 100,
		maxFailures: 3,
		maxDuration: 15 * 60 * 1000,
		requestTimeout: 30000
	},

	pollers: {},

	escapeListener: null,

	pendingTaskKey: 'july.translate.pending-task.v1',

	success(data) {
		console.log(data);
	},

	error(message = '翻译失败') {
		this.notify(message, 'error', 0);
	},

	notify(message, type = 'warning', duration = 5000) {
		if (typeof this.message !== 'function') return;

		this.message({
			message: typeof message === 'string' && message.trim() ? message : '翻译失败',
			type: type,
			duration: duration,
			showClose: true
		});
	},

	frame(loading, message) {
		this.loading = loading;
		this.message = message;
		this.bindPollingCancellation();
		return this;
	},

	bindPollingCancellation() {
		if (this.escapeListener || typeof window === 'undefined') return;

		this.escapeListener = event => {
			if (event.key === 'Escape') this.cancelPolling();
		};
		window.addEventListener('keydown', this.escapeListener);
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

	readPendingTask(type = null) {
		if (typeof window === 'undefined' || !window.sessionStorage) return null;

		let task;
		try {
			task = JSON.parse(window.sessionStorage.getItem(this.pendingTaskKey));
		} catch (error) {
			window.sessionStorage.removeItem(this.pendingTaskKey);
			return null;
		}

		const valid = task
			&& task.version === 1
			&& ['batch', 'page', 'tpl'].includes(task.type)
			&& Number.isFinite(task.startedAt)
			&& Number.isFinite(task.expiresAt)
			&& task.expiresAt > Date.now()
			&& task.request
			&& typeof task.request === 'object'
			&& typeof task.request.data === 'string'
			&& task.request.data.length > 0;

		if (!valid) {
			window.sessionStorage.removeItem(this.pendingTaskKey);
			return null;
		}

		return type && task.type !== type ? null : task;
	},

	writePendingTask(task) {
		if (typeof window === 'undefined' || !window.sessionStorage) return false;

		try {
			window.sessionStorage.setItem(this.pendingTaskKey, JSON.stringify(task));
			return true;
		} catch (error) {
			return false;
		}
	},

	clearPendingTask(type = null) {
		if (typeof window === 'undefined' || !window.sessionStorage) return false;

		const task = this.readPendingTask();
		if (type && task && task.type !== type) return false;

		window.sessionStorage.removeItem(this.pendingTaskKey);
		return Boolean(task);
	},

	storePendingTask(type, request, details = {}) {
		const startedAt = Date.now();
		return this.writePendingTask(Object.assign({
			version: 1,
			type: type,
			startedAt: startedAt,
			expiresAt: startedAt + this.pollOptions.maxDuration,
			attempts: 0,
			failures: 0,
			request: request
		}, details));
	},

	updatePendingTask(type, request, poller, details = {}) {
		const task = this.readPendingTask(type);
		if (!task) return false;

		return this.writePendingTask(Object.assign(task, details, {
			request: request,
			attempts: poller ? poller.attempts : task.attempts,
			failures: poller ? poller.failures : task.failures
		}));
	},

	resume(type, success = null) {
		const task = this.readPendingTask(type);
		if (!task) return false;

		this.cancelPolling(null, false, false);
		this.notify('检测到未完成的翻译任务，正在继续获取结果', 'info');

		if (type === 'batch') {
			if (!Array.isArray(task.nodes)
				|| !Array.isArray(task.batchCodes)
				|| !Number.isInteger(task.batchIndex)
				|| task.batchIndex < 0
				|| task.batchIndex >= task.batchCodes.length
				|| !Array.isArray(task.batchResults)) {
				this.clearPendingTask(type);
				return false;
			}

			this.nodes = task.nodes;
			this.batchCodes = task.batchCodes;
			this.batchIndex = task.batchIndex;
			this.batchResults = task.batchResults;
			this.success = data => {
				this.message({
					message: data,
					type: 'success',
					duration: 0,
					showClose: true
				});
				if (success) success(data, task);
			};
			this.getBatch(
				task.request,
				'已恢复未完成任务',
				task.attempts + 1,
				null,
				task.startedAt,
				task.failures
			);
			return true;
		}

		if (type === 'page') {
			this.success = success || this.success;
			this.getPage(
				task.request,
				'已恢复未完成任务',
				task.attempts + 1,
				null,
				task.startedAt,
				task.failures
			);
			return true;
		}

		this.success = data => {
			this.message({
				message: data && data.message ? data.message : '翻译成功',
				type: 'success',
				duration: 0,
				showClose: true
			});
			if (success) success(data, task);
		};
		this.getTpl(
			task.request,
			'已恢复未完成任务',
			task.attempts + 1,
			null,
			task.startedAt,
			task.failures
		);
		return true;
	},

	startPolling(type, initialAttempts = 0, startedAt = null, initialFailures = 0) {
		this.stopPolling(type);

		const poller = {
			startedAt: Number.isFinite(startedAt) ? startedAt : Date.now(),
			attempts: Math.max(0, initialAttempts),
			failures: Math.max(0, initialFailures),
			timer: null,
			deadlineTimer: null,
			loading: null,
			cancelSource: null,
			stopped: false
		};
		this.pollers[type] = poller;
		const remaining = Math.max(0, this.pollOptions.maxDuration - (Date.now() - poller.startedAt));
		poller.deadlineTimer = setTimeout(() => {
			if (!this.isPolling(type, poller)) return;

			this.stopPolling(type);
			this.clearPendingTask(type);
			this.error('等待翻译结果超时，已停止自动查询，请稍后重试');
		}, remaining);

		return poller;
	},

	isPolling(type, poller) {
		return this.pollers[type] === poller && !poller.stopped;
	},

	closePollLoading(poller) {
		if (!poller || !poller.loading) return;

		poller.loading.close();
		poller.loading = null;
	},

	stopPolling(type) {
		const poller = this.pollers[type];
		if (!poller) return false;

		poller.stopped = true;
		if (poller.timer !== null) clearTimeout(poller.timer);
		if (poller.deadlineTimer !== null) clearTimeout(poller.deadlineTimer);
		if (poller.cancelSource) poller.cancelSource.cancel('已停止获取翻译结果');
		poller.timer = null;
		poller.deadlineTimer = null;
		poller.cancelSource = null;
		this.closePollLoading(poller);
		delete this.pollers[type];

		return true;
	},

	cancelPolling(type = null, showMessage = true, clearPending = true) {
		const types = type ? [type] : Object.keys(this.pollers);
		const pending = this.readPendingTask();
		if (!type && pending && !types.includes(pending.type)) types.push(pending.type);
		let stopped = false;

		types.forEach(item => {
			stopped = this.stopPolling(item) || stopped;
			if (clearPending) stopped = this.clearPendingTask(item) || stopped;
		});

		if (stopped && showMessage) this.notify('已停止获取翻译结果', 'warning');

		return stopped;
	},

	pollDelay(attempts) {
		return Math.min(
			this.pollOptions.initialDelay * Math.pow(1.5, attempts),
			this.pollOptions.maxDelay
		);
	},

	canContinuePolling(type, poller) {
		if (!this.isPolling(type, poller)) return false;

		const timedOut = Date.now() - poller.startedAt >= this.pollOptions.maxDuration;
		const exhausted = poller.attempts >= this.pollOptions.maxAttempts;
		if (!timedOut && !exhausted) return true;

		this.stopPolling(type);
		this.clearPendingTask(type);
		this.error(timedOut
			? '等待翻译结果超时，已停止自动查询，请稍后重试'
			: '获取翻译结果次数过多，已停止自动查询，请稍后重试');

		return false;
	},

	schedulePoll(type, poller, text, callback) {
		if (!this.canContinuePolling(type, poller)) return;

		const attempt = poller.attempts + 1;
		const delay = this.pollDelay(poller.attempts);
		poller.loading = this.loading({
			lock: true,
			text: text + '（第' + attempt + '次，按 Esc 可停止等待）',
			background: 'rgba(255, 255, 255, 0.7)'
		});

		poller.timer = setTimeout(() => {
			poller.timer = null;
			if (!this.isPolling(type, poller)) {
				this.closePollLoading(poller);
				return;
			}

			poller.attempts = attempt;
			callback();
		}, delay);
	},

	pollRequest(type, poller, endpoint, data, text, onResponse, retry) {
		this.schedulePoll(type, poller, text, () => {
			poller.cancelSource = axios.CancelToken ? axios.CancelToken.source() : null;
			const requestOptions = { timeout: this.pollOptions.requestTimeout };
			if (poller.cancelSource) requestOptions.cancelToken = poller.cancelSource.token;

			axios.post(endpoint, data, requestOptions).then(response => {
				poller.cancelSource = null;
				this.closePollLoading(poller);
				if (!this.isPolling(type, poller)) return;

				poller.failures = 0;
				onResponse(response && response.data ? response.data : {}, poller);
			}).catch(error => {
				poller.cancelSource = null;
				this.closePollLoading(poller);
				if (!this.isPolling(type, poller)) return;

				poller.failures++;
				const message = this.getErrorMessage(error);
				if (poller.failures >= this.pollOptions.maxFailures) {
					this.stopPolling(type);
					this.clearPendingTask(type);
					this.error(message + '，已停止自动重试');
					return;
				}

				this.notify(
					message + '，稍后自动重试（' + poller.failures + '/' + this.pollOptions.maxFailures + '）',
					'warning'
				);
				retry(message, poller);
			});
		});
	},

	batch(nodes, success = null, codes = []) {
		this.cancelPolling(null, false);
		this.nodes = nodes;
		this.batchCodes = Array.isArray(codes) && codes.length ? codes : [null];
		this.batchIndex = 0;
		this.batchResults = [];

		this.success = function (data) {
			this.message({
				message: data,
				type: 'success',
				duration: 0,
				showClose: true
			});
			if (success) success(data);
		};

		this.createBatchTask();
	},

	createBatchTask() {
		const code = this.batchCodes[this.batchIndex];
		const loading = this.loading({
			lock: true,
			text: (code ? '[' + code + '] ' : '') + '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)'
		});
		const payload = { nodes: this.nodes };
		if (code) payload.code = code;

		axios.post('/manage/translate/task/batch', payload).then(response => {
			loading.close();
			const status = response && response.data ? response.data : {};

			if (status.status !== true) {
				this.error((code ? '[' + code + '] ' : '') + (status.message || '创建翻译任务失败'));
				return;
			}

			const request = { nodes: this.nodes, code: code, data: status.data };
			this.storePendingTask('batch', request, {
				nodes: this.nodes,
				batchCodes: this.batchCodes,
				batchIndex: this.batchIndex,
				batchResults: this.batchResults
			});
			this.getBatch(request);
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	getBatch(data, statusMessage = null, i = 1, poller = null, startedAt = null, failures = 0) {
		const code = data.code;
		const state = poller || this.startPolling('batch', i - 1, startedAt, failures);
		this.updatePendingTask('batch', data, state, {
			nodes: this.nodes,
			batchCodes: this.batchCodes,
			batchIndex: this.batchIndex,
			batchResults: this.batchResults
		});
		const prefix = code ? '[' + code + '] ' : '';
		const text = prefix
			+ (statusMessage ? '上次结果：' + statusMessage + '，' : '')
			+ '准备获取翻译结果 ...';

		this.pollRequest(
			'batch',
			state,
			'/manage/translate/task/batch/result',
			data,
			text,
			status => {
				if (Object.prototype.hasOwnProperty.call(status, 'data') && status.data !== null) {
					data.data = status.data;
				}

				if (status.status === null) {
					this.getBatch(data, status.message, state.attempts + 1, state);
					return;
				}

				this.stopPolling('batch');
				if (status.status === true) {
					this.batchResults.push(code);
					this.batchIndex++;
					if (this.batchIndex < this.batchCodes.length) {
						this.createBatchTask();
						return;
					}

					this.clearPendingTask('batch');
					const completed = this.batchResults.filter(item => item);
					this.success(completed.length
						? '已完成 ' + completed.length + ' 种语言：' + completed.join('、')
						: status.data);
					return;
				}

				this.clearPendingTask('batch');
				this.error(prefix + (status.message || '获取翻译结果失败'));
			},
			message => this.getBatch(data, message, state.attempts + 1, state)
		);
	},

	page(data, success = null) {
		this.cancelPolling(null, false);
		this.success = success || this.success;

		const loading = this.loading({
			lock: true,
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)'
		});

		axios.post('/manage/translate/task/page', data).then(response => {
			loading.close();
			const status = response && response.data ? response.data : {};

			if (status.status !== true) {
				this.error(status.message || '创建翻译任务失败');
				return;
			}

			data.data = status.data;
			this.storePendingTask('page', data);
			this.getPage(data);
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	getPage(data, statusMessage = null, i = 1, poller = null, startedAt = null, failures = 0) {
		const state = poller || this.startPolling('page', i - 1, startedAt, failures);
		this.updatePendingTask('page', data, state);
		const text = (statusMessage ? '上次结果：' + statusMessage + '，' : '')
			+ '准备获取页面翻译结果 ...';

		this.pollRequest(
			'page',
			state,
			'/manage/translate/task/page/result',
			data,
			text,
			status => {
				if (Object.prototype.hasOwnProperty.call(status, 'data') && status.data !== null) {
					data.data = status.data;
				}

				if (status.status === null) {
					this.getPage(data, status.message, state.attempts + 1, state);
					return;
				}

				this.stopPolling('page');
				if (status.status === true) {
					this.clearPendingTask('page');
					this.success(status.data);
					return;
				}

				this.clearPendingTask('page');
				this.error(status.message || '获取页面翻译结果失败');
			},
			message => this.getPage(data, message, state.attempts + 1, state)
		);
	},

	tpl(code, success = null) {
		this.cancelPolling(null, false);
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
			text: '开始创建任务 ...',
			background: 'rgba(255, 255, 255, 0.7)'
		});

		axios.post('/manage/translate/task/tpl', { code: code }).then(response => {
			loading.close();
			const status = response && response.data ? response.data : {};

			if (status.status !== true) {
				this.error(status.message || '创建翻译任务失败');
				return;
			}

			const request = { code: code, data: status.data };
			this.storePendingTask('tpl', request);
			this.getTpl(request);
		}).catch(error => {
			loading.close();
			this.error(this.getErrorMessage(error));
		});
	},

	getTpl(data, statusMessage = null, i = 1, poller = null, startedAt = null, failures = 0) {
		const state = poller || this.startPolling('tpl', i - 1, startedAt, failures);
		this.updatePendingTask('tpl', data, state);
		const text = (statusMessage ? '上次结果：' + statusMessage + '，' : '')
			+ '准备获取模板翻译结果 ...';

		this.pollRequest(
			'tpl',
			state,
			'/manage/translate/task/tpl/result',
			data,
			text,
			status => {
				if (Object.prototype.hasOwnProperty.call(status, 'data') && status.data !== null) {
					data.data = status.data;
				}

				if (status.status === null) {
					this.getTpl(data, status.message, state.attempts + 1, state);
					return;
				}

				this.stopPolling('tpl');
				if (status.status === true) {
					this.clearPendingTask('tpl');
					this.success(status.data || {});
					return;
				}

				this.clearPendingTask('tpl');
				this.error(status.message || '获取模板翻译结果失败');
			},
			message => this.getTpl(data, message, state.attempts + 1, state)
		);
	},

	inObject(value, object) {
		for (let key in object) {
			if (object[key] === value) return true;
		}
		return false;
	}
};
