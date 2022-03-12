Vue.component('data-filter', {

    template: '<slot v-bind:groups="groups" v-bind:values="values"></slot>',

    props: {
        groupsMeta: {
            type: Array,
            required: true,
        },
        records: {
            type: Array,
            default: [],
        },
        defaultScreen: {
            type: Array,
            default: []
        }
    },

    data: function() {
        return {
            groups: {},
            values: {},
        };
    },

    created: function() {
        this.initGroups();
    },

    watch: {
        records: function(newRecords, oldRecords) {
            this.initGroups();

            this.$emit('after-filter', newRecords.slice());
        },
    },

    methods: {

        // 初始化筛选器
        initGroups: function() {
            var records = this.records;
            var filter = this;
            var groups = {};
            var values = {};

            // 根据构造信息生成验证器组
            this.groupsMeta.forEach(function(meta) {
                meta.data = records;
                var group = ValidatorGroup.make(meta);
                groups[meta.name] = group;
                values[meta.name] = filter.getDefaultGroupValue(meta);

                // 如果是检索验证器组，则设置关键字获取器
                if (meta.type == 'search') {
                    var prop = meta.name;
                    group.all().forEach(function(validator) {
                        // 设置关键字获取器
                        validator.setKeywordsResolver(function() {
                            return filter.values && filter.values[prop] || '';
                        });

                        // 激活验证器
                        validator.activate();
                    });
                }
            });

            if (this.defaultScreen.length == 2 && this.defaultScreen[0] in values) {
                for (var i = 0; i < groups[this.defaultScreen[0]]._validators.length; i++) {
                    if (groups[this.defaultScreen[0]]._validators[i].label == this.defaultScreen[1]) {
                        groups[this.defaultScreen[0]]._validators[i].active = true;
                        values[this.defaultScreen[0]] = groups[this.defaultScreen[0]]._validators[i].id;
                    }
                }

                this.groups = groups;
                this.values = values;

                this.handleValidatorChange(this.defaultScreen[0]);
            } else if (this.defaultScreen.length == 4 && this.defaultScreen[0] in values && this.defaultScreen[2] in values) {
                for (var i = 0; i < groups[this.defaultScreen[0]]._validators.length; i++) {
                    if (groups[this.defaultScreen[0]]._validators[i].label == this.defaultScreen[1]) {
                        groups[this.defaultScreen[0]]._validators[i].active = true;
                        values[this.defaultScreen[0]] = groups[this.defaultScreen[0]]._validators[i].id;
                    }
                }
                for (var i = 0; i < groups[this.defaultScreen[2]]._validators.length; i++) {
                    if (groups[this.defaultScreen[2]]._validators[i].label == this.defaultScreen[3]) {
                        groups[this.defaultScreen[2]]._validators[i].active = true;
                        values[this.defaultScreen[2]] = groups[this.defaultScreen[2]]._validators[i].id;
                    }
                }

                this.groups = groups;
                this.values = values;

                this.handleValidatorChange(this.defaultScreen[0]);
            } else {
                this.groups = groups;
                this.values = values;
            }

            return this;
        },

        // 重置筛选器
        reset: function() {
            var groups = this.groups;
            var values = {};
            var filter = this;

            this.groupsMeta.forEach(function(meta) {
                values[meta.name] = filter.getDefaultGroupValue(meta);
                if (meta.type != 'search') {
                    groups[meta.name].all().forEach(function(validator) {
                        // 停用验证器
                        validator.deactivate();
                    });
                }
            });

            this.values = values;
            this.$emit('after-filter', this.records.slice());

            return this;
        },

        getDefaultGroupValue: function(meta) {
            if (meta.default !== undefined) {
                return meta.default;
            }

            if (meta.type == 'search') {
                return '';
            }

            return meta.multiple === undefined || meta.multiple ? [] : '';
        },

        // 可选的验证器改变时执行
        handleValidatorChange: function(groupName) {
            if (['series_3'].indexOf(groupName) != -1) {
                this.values.series_2 = '';
                this.values.media = '';
                this.values.applicable = '';
                this.values.reinforcement = '';
                this.values.temperature = '';
                for (var i = 0; i < this.groups.series_2._validators.length; i++) {
                    this.groups.series_2._validators[i].active = false;
                }
                for (var i = 0; i < this.groups.media._validators.length; i++) {
                    this.groups.media._validators[i].active = false;
                }
                for (var i = 0; i < this.groups.applicable._validators.length; i++) {
                    this.groups.applicable._validators[i].active = false;
                }
                for (var i = 0; i < this.groups.reinforcement._validators.length; i++) {
                    this.groups.reinforcement._validators[i].active = false;
                }
                for (var i = 0; i < this.groups.temperature._validators.length; i++) {
                    this.groups.temperature._validators[i].active = false;
                }
            }

            if (['series_2', 'media', 'applicable', 'reinforcement', 'temperature'].indexOf(groupName) != -1) {
                this.values.series_3 = '';
                for (var i = 0; i < this.groups.series_3._validators.length; i++) {
                    this.groups.series_3._validators[i].active = false;
                }
            }

            this.groups[groupName].activate(this.values[groupName]);

            this.handleFilterChange();
        },

        // 筛选条件改变时重新筛选数据
        handleFilterChange: function() {
            var groups = this.groups;

            var records = this.records.filter(function(record) {
                for (var key in groups) {
                    if (!groups[key].validate(record)) return false;
                }
                return true;
            });

            this.$emit('after-filter', records);
        },
    },
});
