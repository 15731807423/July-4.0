const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

class MemoryStorage {
	constructor() {
		this.values = new Map();
	}

	getItem(key) {
		return this.values.has(key) ? this.values.get(key) : null;
	}

	setItem(key, value) {
		this.values.set(key, String(value));
	}

	removeItem(key) {
		this.values.delete(key);
	}
}

const storage = new MemoryStorage();
const context = {
	console,
	Date,
	JSON,
	Math,
	Object,
	Array,
	Number,
	Boolean,
	String,
	setTimeout,
	clearTimeout,
	window: {
		sessionStorage: storage,
		addEventListener() {}
	},
	axios: {
		CancelToken: {
			source() {
				return { token: {}, cancel() {} };
			}
		}
	}
};

const source = fs.readFileSync(
	path.resolve(__dirname, '../../../themes/backend/js/translate-task.js'),
	'utf8'
);
vm.runInNewContext(source + '\n;globalThis.__translate = translate;', context);

const translate = context.__translate;
translate.frame(
	() => ({ close() {} }),
	() => {}
);

const request = {
	code: 'de',
	text: '{"title":"Source"}',
	data: '{"task_id":"example"}'
};

assert.strictEqual(translate.storePendingTask('page', request), true);
assert.deepStrictEqual(
	JSON.parse(JSON.stringify(translate.readPendingTask('page').request)),
	request
);

let resumed = null;
translate.getPage = (data, message, attempt, poller, startedAt) => {
	resumed = { data, message, attempt, poller, startedAt };
};
assert.strictEqual(translate.resume('page', () => {}), true);
assert.strictEqual(resumed.message, '已恢复未完成任务');
assert.deepStrictEqual(JSON.parse(JSON.stringify(resumed.data)), request);
assert.ok(Number.isFinite(resumed.startedAt));

assert.strictEqual(translate.cancelPolling('page', false), true);
assert.strictEqual(translate.readPendingTask('page'), null);

storage.setItem(translate.pendingTaskKey, '{broken');
assert.strictEqual(translate.readPendingTask(), null);

translate.writePendingTask({
	version: 1,
	type: 'page',
	startedAt: Date.now() - 2000,
	expiresAt: Date.now() - 1000,
	request
});
assert.strictEqual(translate.readPendingTask(), null);

console.log('translate-task session recovery tests passed');
