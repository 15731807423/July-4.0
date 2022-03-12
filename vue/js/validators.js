;
(function(global) {
    // Object.assign PollyFill
    if (typeof Object.assign !== 'function') {
        // Must be writable: true, enumerable: false, configurable: true
        Object.defineProperty(Object, "assign", {
            value: function assign(target, varArgs) { // .length of function is 2
                'use strict';
                if (target === null || target === undefined) {
                    throw new TypeError('Cannot convert undefined or null to object');
                }
                var to = Object(target);
                for (var index = 1; index < arguments.length; index++) {
                    var nextSource = arguments[index];
                    if (nextSource !== null && nextSource !== undefined) {
                        for (var nextKey in nextSource) {
                            // Avoid bugs when hasOwnProperty is shadowed
                            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                to[nextKey] = nextSource[nextKey];
                            }
                        }
                    }
                }
                return to;
            },
            writable: true,
            configurable: true
        });
    }
    // classExtend
    Function.prototype.classExtend = function classExtend(supper, methods) {
        this.prototype = Object.assign(Object.create(supper.prototype), {
            constructor: this,
        }, methods || {});
    };
    let validatorIndex = 1;
    let groupIndex = 1;
    const defineProp = Object.defineProperty;

    function realArgs(args) {
        args = Array.prototype.slice.call(args);
        if (args.length == 1 && Array.isArray(args[0])) {
            return args[0];
        }
        return args;
    }

    function countBy(data, prop) {
        return data.reduce(function(result, datum) {
            const value = datum[prop];
            if (value != null) {
                if (result[value]) {
                    result[value]++;
                } else {
                    result[value] = 1;
                }
            }
            return result;
        }, {});
    }

    function BaseValidator(prop, label) {
        // id
        defineProp(this, 'id', {
            value: 'validator_' + validatorIndex++
        });
        // 属性
        defineProp(this, 'prop', {
            value: prop
        });
        this.label = label || prop;
        this.active = false;
        this.total = 0;
        // 格式转换器
        this.caster = function(value) {
            return value
        };
        // 值获取器
        this.resolver = function(record) {
            return record[this.prop]
        };
        // 值验证器
        this.validator = function(value) {
            return false
        };
        let _group = null;
        // 所属分组
        defineProp(this, 'group', {
            set: function(newGroup) {
                if (newGroup && newGroup instanceof ValidatorGroup) {
                    _group = newGroup;
                } else {
                    throw '分组无效';
                }
            },
            get: function() {
                return _group;
            },
        });
    }

    BaseValidator.prototype = {
        constructor: BaseValidator,
        setGroup: function(group) {
            this.group = group;
            return this;
        },
        setCaster: function(caster) {
            if (caster instanceof Function) {
                this.caster = caster.bind(this);
            }
            return this;
        },
        setResolver: function(resolver) {
            if (resolver instanceof Function) {
                this.resolver = resolver.bind(this);
            }
            return this;
        },
        setValidator: function(validator) {
            if (validator instanceof Function) {
                this.validator = validator.bind(this);
            }
            return this;
        },
        deactivate: function() {
            this.active = false;
            return this;
        },
        activate: function() {
            this.active = true;
            return this;
        },
        isActive: function() {
            return !!this.active;
        },
        validate: function(record) {
            const args = Array.prototype.slice.call(arguments);
            args[0] = this.caster(this.resolver(record));
            return !!this.validator.apply(this, args);
        },
        count: function(data) {
            if (data) {
                const validator = this;
                return this.total = data.reduce(function(total, datum) {
                    return total + validator.validate(datum);
                }, 0);
            }
            return this.total;
        },
    };

    global.BaseValidator = BaseValidator;
    // 值验证器，由指定的属性和值自动生成验证函数
    function ValueValidator(prop, value, label) {
        BaseValidator.call(this, prop, label);
        this.label = label || value;
        defineProp(this, 'value', {
            value: this.caster(value)
        });
        this.setValidator(function(value) {
            if (value.indexOf(', ') == -1) {
                return value === this.value;
            } else {
                value = value.split(', ');
                return value.indexOf(this.value) != -1;
            }
            // return value.indexOf(this.value) != -1;
            // return value === this.value;
        });
    }

    ValueValidator.classExtend(BaseValidator, {});
    // 范围验证器，由指定的属性和范围自动生成验证函数
    function RangeValidator(prop, min, max, label) {
        BaseValidator.call(this, prop, label);
        this.label = label || prop + ' ' + min + ' - ' + max;
        defineProp(this, 'min', {
            value: this.caster(min)
        })
        defineProp(this, 'max', {
            value: this.caster(max)
        })
        this.setValidator(function(value) {
            return value >= this.min && value <= this.max;
        });
    }

    RangeValidator.classExtend(BaseValidator, {});
    global.RangeValidator = RangeValidator;
    // 枚举值验证器，由指定的属性和枚举值自动生成验证函数
    function EnumValidator(prop, items, label) {
        BaseValidator.call(this, prop);
        this.label = label || items.join(',');
        defineProp(this, 'items', {
            value: items || []
        });
        this.setValidator(function(value) {
            return this.items.indexOf(value) >= 0;
        });
    }

    EnumValidator.classExtend(BaseValidator, {});
    // 包含验证器，由指定的属性和枚举值自动生成验证函数
    function ContainsValidator(prop, caseSensitive, label) {
        BaseValidator.call(this, prop, label);
        defineProp(this, 'caseSensitive', {
            value: !! caseSensitive
        });
        this.setValidator(function(value, keywords) {
            keywords = keywords == null ? this.keywordsResolver() : keywords;
            if (!keywords.length) {
                return true;
            }
            if (!this.caseSensitive) {
                value = value.toLowerCase();
                keywords = keywords.toLowerCase();
            }
            return value.indexOf(keywords) >= 0;
        });
        this.setKeywordsResolver(function() {
            return '';
        });
    }

    ContainsValidator.classExtend(BaseValidator, {
        setKeywordsResolver: function(resolver) {
            if (resolver instanceof Function) {
                this.keywordsResolver = resolver.bind(this);
            }
            return this;
        },
    });

    global.ContainsValidator = ContainsValidator;

    function ValidatorGroup(multiple) {
        defineProp(this, 'id', {
            value: 'group_' + groupIndex++
        });
        defineProp(this, 'multiple', {
            value: multiple == null ? true : !! multiple
        });
        defineProp(this, 'actived', {
            get: function() {
                return this._validators.reduce(function(total, validator) {
                    return total + validator.active;
                }, 0);
            },
        });
        defineProp(this, 'total', {
            get: function() {
                return this._validators.reduce(function(total, validator) {
                    return total + validator.total;
                }, 0);
            },
        });
        this._validators = [];
    }

    ValidatorGroup.prototype = {
        constructor: ValidatorGroup,
        // 添加一个过滤选项
        add: function(validator) {
            if (validator && validator instanceof BaseValidator) {
                validator.group = this;
                this._validators.push(validator);
            }
            return this;
        },
        // 获取验证器
        all: function(filter) {
            filter = filter && filter instanceof Function ? filter : null;
            if (!filter) {
                return this._validators.slice();
            }
            const validators = [];
            this._validators.forEach(function(validator) {
                if (filter.call(null, validator)) {
                    validators.push(validator);
                }
            });
            return validators;
        },
        // 获取所有可用的验证器
        allActive: function() {
            const validators = [];
            this._validators.forEach(function(validator) {
                if (validator.active) {
                    validators.push(validator);
                }
            });
            return validators;
        },
        activate: function() {
            const ids = realArgs(arguments);
            const validators = this._validators;
            for (let i = 0; i < validators.length; i++) {
                const validator = validators[i];
                validator.active = ids.indexOf(validator.id) >= 0;
            }
            return this;
        },
        // 过滤数据
        filter: function(data) {
            return data.filter(this.validate);
        },
        validate: function(record) {
            const validators = this._validators;
            let actived = 0;
            for (let i = 0; i < validators.length; i++) {
                const validator = validators[i];
                if (validator.active && validator.validate(record)) {
                    return true;
                }
                actived += validator.active;
            }
            return actived === 0;
        },
        count: function(data) {
            this._validators.forEach(function(validator) {
                validator.count(data);
            });
            return this;
        },
    };

    // 批量创建值验证器
    function getValueValidators(meta) {
        var validators = [];
        const prop = meta.prop;
        const data = Array.isArray(meta.data) ? meta.data : [];
        for (const key in countBy(data, prop)) {
            if (key == '-') continue;
            let key2 = key.split(', ');
            if (key2.length == 1) {
                validators.push(new ValueValidator(prop, key));
            } else {
                for (var i = 0; i < key2.length; i++) {
                    validators.push(new ValueValidator(prop, key2[i]));
                }
            }
        }

        return sortList(validators, 'label');
    }

    function sortList(arr, flag){
        return arr.sort((a, b) => { 
            return a[flag].localeCompare(b[flag])
        })
    }

    // 批量创建检索验证器
    function getContainsValidators(meta) {
        const validators = [];
        const props = Array.isArray(meta.prop) ? meta.prop : [meta.prop];
        props.forEach(function(prop) {
            validators.push(new ContainsValidator(prop));
        });
        return validators;
    }

    ValidatorGroup.make = function(meta) {
        var group = new ValidatorGroup(meta.multiple), group2 = [], name = [];

        let validators = Array.isArray(meta.validators) ? meta.validators : [];
        switch (meta.type) {
            case 'value':
                validators = validators.concat(getValueValidators(meta));
                break;
            case 'search':
                validators = validators.concat(getContainsValidators(meta));
                break;
        }
        validators.forEach(function(validator) {
            group.add(validator);
        });
        if (Array.isArray(meta.data) && meta.data.length) {
            group.count(meta.data);
        }

        for (var i = 0; i < group._validators.length; i++) {
            if (name.indexOf(group._validators[i].label) == -1) {
                group2.push(group._validators[i]);
                name.push(group._validators[i].label);
            }
        }

        group._validators = group2;

        return group;
    }

    global.ValidatorGroup = ValidatorGroup;
})(window);