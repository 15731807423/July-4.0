LazyScript.load('jquery', function (global) {
    function Calculation(data) {
        data.param              = parseValue(data.param, 'param');
        data.error              = parseValue(data.error, 'error');
        data.disabled           = parseValue(data.disabled, 'disabled');
        data.hidden             = parseValue(data.hidden, 'hidden');
        data.submit             = parseValue(data.submit, 'string');
        data.gather             = parseValue(data.gather, 'function');
        data.errorShowType      = parseValue(data.errorShowType, 'errorShowType');
        data.result             = parseValue(data.result, 'result');
        data.resultClear        = parseValue(data.resultClear, 'boolean', true);
        data.resultCondition    = parseValue(data.resultCondition, 'string');
        data.reset              = parseValue(data.reset, 'string');

        // 存放所有参数 表单的值 绑定错误信息的id 提交按钮 重置按钮
        const param = {}, form = {}, bind = [], submit = data.submit ? $(data.submit) : null, reset = data.reset ? $(data.reset) : null;

        //  把所有表单放进对象里 表单的值定义全局变量
        for (let i = 0; i < data.param.length; i++) {
            let item = data.param[i], element = $(item.selector);
            param[item.name] = {
                selector: item.selector,
                element: element,
                number: item.number,
                decimal: item.decimal,
                type: item.type,
                range: item.range,
                sync: item.sync,
                statusError: false,
                statusDisabled: false,
                statusHidden: false,
                errorMessage: []
            };

            form[item.name] = null;
        }

        // 循环处理每个参数
        for (let key in param) {
            // 如果是文本框 监听输入事件 如果是下拉菜单等 监听改变事件
            // param[key].type == 'input' ? listenInput(key) : param[key].element.change(change);
            param[key].type == 'input' ? listenInput(key) : listenOther(key);
        }

        // 处理绑定错误信息
        for (var i = 0; i < data.error.length; i++) {
            if (data.error[i].bind === null) continue;
            if (data.error[i].bind === i) continue;

            let array = [i, data.error[i].bind], json = JSON.stringify(array.sort((a, b) => { return a - b; }));

            bind.indexOf(json) === -1 && bind.push(array);
        }

        // 如果有提交按钮 点击按钮时触发计算
        submit && submit.click(calculationCheck);

        // 如果有重置按钮 点击按钮时清空表单和结果
        reset && reset.click(function () {
            // 循环所有表单
            for (let key in param) {
                // 移除错误
                param[key].statusError = false;
                param[key].errorMessage = [];

                // 重置元素
                for (var i = 0; i < param[key].element.length; i++) {
                    param[key].element.eq(i).parent().html(param[key].element.eq(i).parent().html());
                }
            }

            // 清空错误信息
            data.gather ? gather() : dispersed();

            // 重新注册全部参数
            for (let key in param) {
                param[key].element = $(param[key].selector);
                param[key].type == 'input' ? listenInput(key) : listenOther(key);
            }

            // 值发生了变化
            change();

            // 清空结果
            result(null);
        });

        // 执行一次 处理禁用隐藏
        change();

        // 解析参数
        function parseValue(value, name, defaultValue) {
            let type = $.type(value);

            switch (name) {
                case 'string':
                    if (type == 'string') return value;
                    break;

                case 'function':
                    if (type == 'function') return value;
                    break;

                case 'boolean':
                    if (type == 'boolean') return value;
                    break;

                case 'number':
                    if (type == 'number') return value;
                    break;

                case 'param':
                    if (type != 'array') return [];
                    for (var i = 0; i < value.length; i++) {
                        value[i] = {
                            name        : parseValue(value[i].name, 'string'),
                            selector    : parseValue(value[i].selector, 'string'),
                            type        : parseValue(value[i].type, 'param[].type'),
                            number      : parseValue(value[i].number, 'boolean', true),
                            range       : parseValue(value[i].range, 'param[].range'),
                            decimal     : parseValue(value[i].decimal, 'param[].decimal', 0),
                            sync        : parseValue(value[i].sync, 'boolean', false),
                        };
                    }
                    return value;
                    break;

                case 'param[].type':
                    if (type == 'string' && ['input', 'radio', 'checkbox', 'select'].indexOf(value) !== -1) return value;
                    break;

                case 'param[].range':
                    if (type == 'array' && value.length == 2 && !isNaN(parseFloat(value[0])) && !isNaN(parseFloat(value[1]))) return [parseFloat(value[0]), parseFloat(value[1])];
                    break;

                case 'param[].decimal':
                    if (type == 'number' && value >= 0 && value <= 5) return parseInt(value);
                    break;

                case 'error':
                    if (type == 'array') {
                        for (var i = 0; i < value.length; i++) {
                            value[i] = {
                                name        : parseValue(value[i].name, 'string'),
                                condition   : parseValue(value[i].condition, 'string'),
                                message     : parseValue(value[i].message, 'string'),
                                bind        : parseValue(value[i].bind, 'number'),
                                false       : parseValue(value[i].false, 'function'),
                                true        : parseValue(value[i].true, 'function')
                            };
                        }
                        return value;
                    }
                    return [];
                    break;

                case 'disabled':
                    if (type == 'array') {
                        for (var i = 0; i < value.length; i++) {
                            value[i] = {
                                name        : parseValue(value[i].name, 'string'),
                                condition   : parseValue(value[i].condition, 'string'),
                                false       : parseValue(value[i].false, 'function'),
                                true        : parseValue(value[i].true, 'function')
                            };
                        }
                        return value;
                    }
                    return [];
                    break;

                case 'hidden':
                    if (type == 'array') {
                        for (var i = 0; i < value.length; i++) {
                            value[i] = {
                                name        : parseValue(value[i].name, 'string'),
                                condition   : parseValue(value[i].condition, 'string'),
                                false       : parseValue(value[i].false, 'function'),
                                true        : parseValue(value[i].true, 'function')
                            };
                        }
                        return value;
                    }
                    return [];
                    break;

                case 'errorShowType':
                    return data.submit ? (value === 2 ? 2 : 1) : 1;
                    break;

                case 'result':
                    if (type == 'array') {
                        for (var i = 0; i < value.length; i++) {
                            value[i] = {
                                calculation : parseValue(value[i].calculation, 'result[].calculation'),
                                selector    : parseValue(value[i].selector, 'string'),
                                decimal     : parseValue(value[i].decimal, 'number', 2)
                            };
                            value[i].element = $(value[i].selector);
                        }
                        return value;
                    }
                    return [];
                    break;

                case 'result[].calculation':
                    if (type == 'string' || type == 'function') return value;
                    break;
            }

            return defaultValue === undefined ? null : defaultValue;
        }

        // 监听文本框的输入事件 判断小数长度等
        function listenInput(name) {
            param[name].element.bind('input propertychange', () => listenInputEvent(name));
        }

        // 监听其他元素的修改事件
        function listenOther(name) {
            param[name].element.change(function () {
                param[name].sync && param[name].element.val($(this).val());
                change(name);
            });
        }

        // 输入事件
        function listenInputEvent(name) {
            // 获取元素的值、小数点数量和小数点位置
            var value = param[name].element.val(), spotCount = value.split('.').length - 1, spot = value.indexOf('.');

            // 如果可以不是数字 只执行change
            if (!param[name].number) return change();

            // 如果小数点数量异常 只留第一个小数点
            spotCount > 1 && (value = value.substr(0, spot) + '.' + value.substr(spot + 1).replaceAll('.', ''));

            // 如果不是数字 清空文本框 文本框为空时不显示错误信息
            if (isNaN(parseFloat(value))) {
                // 清空文本框
                param[name].element.val('');
            } else {
                // 如果没有小数点 说明是整数 否则 截取小数点后指定数量字符
                value = spot === -1 ? parseInt(value) : value.substr(0, spot + 1 + param[name].decimal);

                // 如果设置了范围 判断
                if (param[name].range) {
                    value < param[name].range[0] && (value = param[name].range[0]);
                    value > param[name].range[1] && (value = param[name].range[1]);
                }

                // 处理后的数字赋值
                param[name].element.val(value);
            }

            // 执行
            change(name);
        }

        // 有值发生变化了就要触发
        function change(name) {
            refresh();
            check(name);

            // 如果没有提交按钮 计算结果
            submit || calculationCheck();
        }

        // 刷新所有表单的值
        function refresh() {
            for (let key in param) {
                let item = param[key];
                if (item.type == 'checkbox') {
                    // 多选表单 把选中的值放进数组 如果必须是数字转成数字
                    var value = [];
                    if (item.number) {
                        $(item.selector + ':checked').each((index, element) => value.push(parseFloat(element.value)));
                    } else {
                        $(item.selector + ':checked').each((index, element) => value.push(element.value));
                    }
                    form[key] = JSON.stringify(value);
                } else if (item.type == 'radio') {
                    // 单选表单 获取选中的值 如果没选 值为null 如果必须是数字转成数字
                    var value = $(item.selector + ':checked').val();
                    value === undefined && (value = null);
                    item.number && (value = parseFloat(value));
                    form[key] = value;
                } else {
                    // 文本框和下拉菜单 获取值 如果必须是数字转成数字
                    var value = item.element.val();
                    item.number && (value = value.length == 0 ? null : parseFloat(value));
                    form[key] = value;
                }
            }
        }

        // 检查下面三项
        function check(name) {
            checkError(name);
            checkDisabled(name);
            checkHidden(name);
        }

        // 检查有没有错误
        function checkError(name) {
            var ignore = [];

            if (name) {
                // 循环每个绑定的组
                for (var i = 0; i < bind.length; i++) {
                    // 根据当前修改的参数 获取组里哪个是被修改的 哪个是对比的
                    let update, compare;
                    data.error[bind[i][0]].name == name && (update = bind[i][0], compare = bind[i][1]);
                    data.error[bind[i][1]].name == name && (update = bind[i][1], compare = bind[i][0]);

                    if (update !== null && compare !== null) {
                        // 如果有 记录对比的项的错误信息被忽略 并移除对比参数的错误信息
                        ignore.push(compare);
                        param[data.error[compare].name].statusError = false;
                        param[data.error[compare].name].errorMessage = [];
                        data.error[compare].false();
                    }
                }
            }

            // 如果错误信息一起展示
            if (data.gather) {
                // 先移除全部错误
                for (let key in param) {
                    param[key].statusError = false;
                    param[key].errorMessage = [];
                }

                // 全部参数的错误信息
                var message = {};

                // 检查每个错误
                for (let i = 0; i < data.error.length; i++) {
                    // 忽略的错误跳过
                    if (ignore.indexOf(i) !== -1) continue;

                    // 判断文本框的值是否错误
                    if (param[data.error[i].name].type == 'input') {
                        run(`${data.error[i].name} !== null && (${data.error[i].condition})`) && (
                            message[data.error[i].name]
                            ? message[data.error[i].name].push(data.error[i].message)
                            : message[data.error[i].name] = [data.error[i].message]
                        );
                    }
                }

                for (let key in message) {
                    param[key].statusError = true;
                    param[key].errorMessage = message[key];
                }

                // 如果input触发显示事件 立即执行一次
                data.errorShowType == 1 && gather();
            } else {
                var list = [];

                // 检查每个错误
                for (let i = 0; i < data.error.length; i++) {
                    // 忽略的错误跳过
                    if (ignore.indexOf(i) !== -1) continue;

                    // 判断文本框的值是否错误
                    if (param[data.error[i].name].type == 'input') {
                        // 已经出错了就不再判断
                        if (list.indexOf(data.error[i].name) !== -1) continue;

                        if (run(`${data.error[i].name} !== null && (${data.error[i].condition})`)) {
                            // 如果有值且执行结果为真且该参数还没出现错误 记录下来 因为只显示第一个错误 执行真函数 参数设置为错误 设置错误信息
                            list.push(data.error[i].name);
                            // data.error[i].true(data.error[i].message);
                            param[data.error[i].name].statusError = true;
                            param[data.error[i].name].errorMessage = [data.error[i].message];
                        } else {
                            // 没有问题则执行假函数 参数设置为正确 设置错误信息为空数组
                            // data.error[i].false();
                            param[data.error[i].name].statusError = false;
                            param[data.error[i].name].errorMessage = [];
                        }
                    }
                }

                // 如果input触发显示事件 立即执行一次
                data.errorShowType == 1 && dispersed();
            }
        }

        // 检查有没有禁用
        function checkDisabled() {
            // 记录是否有值被修改
            var status = false;

            for (var i = 0; i < data.disabled.length; i++) {
                if (param[data.disabled[i].name].type == 'input') {
                    if (run(data.disabled[i].condition)) {
                        // 结果为真应该禁用 但是先判断当前是否禁用 如果是不执行任何操作 否则执行真函数 修改状态 清空值并记录
                        if (param[data.disabled[i].name].statusDisabled == false) {
                            data.disabled[i].true();
                            param[data.disabled[i].name].statusDisabled = true;
                            param[data.disabled[i].name].element.val('');
                            status = true;
                        }
                    } else {
                        // 结果为假应该解除 但是先判断当前是否解除 如果是不执行任何操作 否则执行假函数 修改状态
                        if (param[data.disabled[i].name].statusDisabled == true) {
                            data.disabled[i].false();
                            param[data.disabled[i].name].statusDisabled = false;
                        }
                    }
                }
            }

            // 真表示有参数的值被修改为空 需要再次执行change
            status && change();
        }

        // 检查有没有隐藏
        function checkHidden() {
            for (var i = 0; i < data.hidden.length; i++) {
                // 执行表达式 如果为真 执行真函数并记录 否则 执行假函数并移除记录
                run(data.hidden[i].condition)
                ? (data.hidden[i].true(), param[data.hidden[i].name].statusHidden = true)
                : (data.hidden[i].false(), param[data.hidden[i].name].statusHidden = false);
            }
        }

        // 判断一个参数是否错误
        function error(name) {
            return param[name].statusError;
        }

        // 判断一个参数是否禁用
        function disabled(name) {
            return param[name].statusDisabled;
        }

        // 分散显示错误信息 返回是否有错的结果
        function dispersed() {
            // 默认没有问题
            var status = true;

            // 先执行一次所有错误的false函数 再执行报错的true函数
            for (var i = 0; i < data.error.length; i++) {
                data.error[i].false();
            }

            for (let key in param) {
                // 有参数报错 记录
                param[key].statusError && (status = false);
            }

            if (status) return true;

            // 执行报错的true函数
            for (let key in param) {
                if (param[key].statusError) {
                    for (var i = 0; i < data.error.length; i++) {
                        if (param[key].errorMessage[0] == data.error[i].message) {
                            data.error[i].true(param[key].errorMessage[0]);
                            break;
                        }
                    }
                }
            }

            return false;
        }

        // 集中显示错误信息 返回是否有错的结果
        function gather() {
            // 没开启没事儿
            if (!data.gather) return true;

            var errorMessage = {};
            for (let key in param) {
                param[key].errorMessage.length > 0 && (errorMessage[key] = param[key].errorMessage);
            }

            if (Object.keys(errorMessage).length > 0) {
                // 有错误信息则执行 清空结果内容 返回假表示有错
                run('(' + data.gather + ')(' + JSON.stringify(errorMessage) + ')');
                data.resultClear && result(null);
                return false;
            } else {
                // 没错误信息则传null 表示没错 用来隐藏元素 返回真表示没错
                run('(' + data.gather + ')(null)');
                return true;
            }
        }

        // 判断所有的值是不是都填上了且没有不合法值 即能不能开始计算结果了 如果能 执行计算
        function calculationCheck() {
            // 参数状态
            var status = true;

            for (let key in param) {
                // 被隐藏的表单不进行判断
                if (param[key].statusHidden) continue;

                // 表单报错不行
                param[key].statusError && (status = false);
            }

            // 如果上面的判断都通过了 执行传进来的条件
            status && (status = run('!!(' + data.resultCondition + ')'));

            // 显示错误信息
            // 这个函数calculationCheck可能是input触发也可能是提交按钮触发
            // 如果是input触发 那已经执行过了
            // 如果是提交按钮触发 且设置为提交按钮判断错误 则触发一次
            // 注意 input的已经触发过了 没必要再触发一次
            // 函数返回一个结果表示有没有错 没错则可以计算 没开启gather则视为没错
            data.errorShowType == 2 && ((data.gather ? gather() : dispersed()) || (status = false));

            // 参数没问题时计算 有问题时清空结果
            status ? calculation() : (data.resultClear && result(null));
        }

        // 计算结果
        function calculation() {
            for (let i = 0; i < data.result.length; i++) {
                let item = data.result[i], type = $.type(item.calculation);

                // 字符串时执行表达式 函数时执行函数 否则为空
                if (type == 'string') {
                    var value = run(item.calculation);
                } else if (type == 'function') {
                    var value = run('(' + item.calculation + ')()');
                } else {
                    var value = '';
                }

                // 如果结果是一个数字 处理小数 不是数字不管 比如1.2%
                value === parseFloat(value) && (value = toFixed(value, data.result[i].decimal));

                // 设置结果
                result(i, value);
            }
        }

        // 结果赋值
        function result(index, value) {
            if (index === null) {
                for (var i = 0; i < data.result.length; i++) {
                    data.result[i].element.html('');
                }
            } else {
                data.result[index].element.html(value)
            }
        }

        // 四舍五入函数
        function toFixed(number, length) {
            if (isNaN(parseFloat(number))) return '';
            return Math.round((parseFloat(number) + Number.EPSILON) * Math.pow(10, length)) / Math.pow(10, length);
        }

        // 判断是不是数字
        function number(value) {
            return value === parseFloat(value).toString()
        }

        // 执行一段代码或一个函数
        function run(expression) {
            for (let key in form) {
                eval(`var ${key} = ${$.type(form[key]) == 'string' ? `"${form[key]}"` : form[key]};`);
            }

            return eval(expression);
        }
    }

	global.Calculation = Calculation;
});