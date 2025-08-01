function Swiper(data) {
    data = parseValue(data, 'data');

    // 关闭边缘抵抗等同于抵抗率为1
    data.resistance || (data.resistance = true, data.resistanceRatio = 1);

    // 容器 滑动元素 滑动元素宽 滑动元素高 容器宽 容器高 滑块宽 滑块高 滑动元素滑动范围 前进按钮 后退按钮 分页
    var container, wrapper, wrapperWidth, wrapperHeight, width, height, slideWidth, slideHeight, wrapperPositionRange, prevButton, nextButton, pagination;

    // 当前索引 滑块 原生滑块 前面复制的滑块 后面复制的滑块 原生滑块的总宽 原生滑块的总高 前面复制的滑块的总宽 后面复制的滑块的总宽 前面复制的滑块的总高 后面复制的滑块的总高 分页数量 旧的索引
    var index = 0, slide, native, prevAll = [], nextAll = [], nativeWidth = 0, nativeHeight = 0, prevWidth = 0, nextWidth = 0, prevHeight = 0, nextHeight = 0, pageTotal, indexOld;

    // 当前动画id   移动类型 0静止 1自动播放切换 2鼠标拖动 3惯性运动 4两侧超出反弹 5贴边 6分页切换 7前进切换 8后退切换 9绑定轮播图切换时带动
    var animateId, moveType = 0;

    // 拖拽方向 开始监听窗口变化
    var dragDirection, windowResize = false;

    // 锁 缩略图对象 自动播放的定时任务 自动播放是否工作 边缘
    var lock = false, thumbs, timer, autoplay = true, border = { left: [], right: [], center: [] };

    // 绑定的对象 待办列队 原本绑定的对象
    var bind, queue = [], original_bind = undefined;;

    // 不知道 是否工作 自己
    var enable = true, work = true, self = this;

    var original = deepClone(data);

    // 实例化完成
    this.complete = false;

    this.setThumbsIndex = i => {
        let callback = () => {
            native.removeClass('swiper-thumbs-index');
            native.eq(i).addClass('swiper-thumbs-index');

            // if (!checkShowByElement(native.eq(i))) {
            index = i;
            setPositionByNativeIndex();
            // }
        };

        this.complete ? callback() : queue.push(callback);
    };

    this.queue = callback => {
        queue.push(callback);
    };

    this.setIndex = (i, j, k) => {
        if (!work || container.is(':hidden')) return false;

        index = data.loop ? i : j;
        moveType = 9;

        if (!k) {
            return autoplay;
        }

        if (autoplay) {
            setPositionByNativeIndex(true, () => moveType = 0);
        }
    }

    this.bind = list => {
        original_bind = Array.isArray(list) ? list : [list];
        bind = [];

        for (var i = 0; i < original_bind.length; i++) {
            if (original_bind[i].getContainer().is(':hidden')) {
                continue;
            }
            if (!original_bind[i].work) {
                continue;
            }
            bind.push(original_bind[i]);
        }
    }

    this.work = () => work;
    this.index = () => index;

    this.getContainer = () => container;

    if (!init()) {
        return this;
    }
    // init();

    // 解析参数
    function parseValue(value, name, defaultValue = null, range = null) {
        let type = $.type(value);

        if (name.indexOf('|') !== -1) {
            name = name.split('|');
            if (name.indexOf(type) !== -1) {
                if (type == 'number') return parseValue(value, 'number', defaultValue, range);
                if (type == 'int') return parseValue(value, 'int', defaultValue, range);
                return value;
            }
        }

        switch (name) {
            case 'string':
                if (type == 'string') return value;
                break;

            case 'function':
                if (type == 'function') return value;
                break;

            case 'array':
                if (type == 'array') return value;
                break;

            case 'boolean':
                if (type == 'boolean') return value;
                break;

            case 'object':
                if (type == 'object') return value;
                break;

            case 'number':
                if (type == 'number' && (!range || (value >= range[0] && value <= range[1]))) return value;
                break;

            case 'int':
                if (type == 'number' && value % 1 == 0 && (!range || (value >= range[0] && value <= range[1]))) return value;
                break;

            case 'data':
                if (type == 'object') {
                    return {
                        selector: parseValue(value.selector, 'selector'),

                        vertical: parseValue(value.vertical, 'boolean', false),
                        initialSlide: parseValue(value.initialSlide, 'int', 0, [0, 999]),
                        grabCursor: parseValue(value.grabCursor, 'boolean', false),
                        speed: parseValue(value.speed, 'int', 300, [0, 10000]),
                        rewind: parseValue(value.rewind, 'boolean', false),
                        click: parseValue(value.click, 'function'),
                        clickToThis: parseValue(value.clickToThis, 'boolean', false),
                        change: parseValue(value.change, 'function', null),
                        fitDistance: parseValue(value.fitDistance, 'fitDistance'),

                        slidesPerView: parseValue(value.slidesPerView, 'slidesPerView'),
                        spaceBetween: parseValue(value.spaceBetween, 'number', 0, [0, 1000]),
                        slidesPerGroup: parseValue(value.slidesPerGroup, 'slidesPerGroup'),
                        centeredSlides: parseValue(value.centeredSlides, 'boolean', false),
                        centeredSlidesBounds: parseValue(value.centeredSlidesBounds, 'boolean', false),
                        centerInsufficientSlides: parseValue(value.centerInsufficientSlides, 'boolean', false),

                        loop: parseValue(value.loop, 'boolean', false),

                        allowTouchMove: parseValue(value.allowTouchMove, 'boolean', true),
                        touchRatio: parseValue(value.touchRatio, 'number', 1, [-3, 3]),
                        resistance: parseValue(value.resistance, 'boolean', true),
                        resistanceRatio: parseValue(value.resistanceRatio, 'number', 0.25, [0, 1]),

                        autoplay: parseValue(value.autoplay, 'boolean', false),
                        delay: parseValue(value.delay, 'number', 3000, [1, 100000]),
                        stopOnLastSlide: parseValue(value.stopOnLastSlide, 'boolean', false),
                        pauseOnMouseEnter: parseValue(value.pauseOnMouseEnter, 'boolean', false),

                        freeMode: parseValue(value.freeMode, 'boolean', false),

                        momentum: parseValue(value.momentum, 'momentum', false),

                        pagination: parseValue(value.pagination, 'pagination', false),

                        navigation: parseValue(value.navigation, 'navigation', false),

                        clickToEnlarge: parseValue(value.clickToEnlarge, 'clickToEnlarge', false),

                        selfAdaption: parseValue(value.selfAdaption, 'object'),

                        thumbs: parseValue(value.thumbs, 'thumbs', false),
                        image: parseValue(value.image, 'image', 'img'),
                        text: parseValue(value.text, 'text', ''),
                        parent: parseValue(value.parent, 'object', null),
                    };
                }
                break;

            case 'selector':
                if (type == 'string' || type == 'object') {
                    return value;
                }
                break;

            case 'fitDistance':
                if (type == 'number') {
                    if (value > 0 && value < 1) return value;
                    if (value >= 1 && value % 1 == 0) return value;
                }
                return 0.2;

            case 'slidesPerView':
                if (value == 'auto') return 'auto';
                if (value === false) return false;
                return parseValue(value, 'int', 1);

            case 'slidesPerGroup':
                if (type == 'boolean') {
                    if (value) {
                        let slidesPerView = parseValue(data.slidesPerView, 'slidesPerView');
                        if (slidesPerView == 'auto') return 1;
                        return slidesPerView;
                    } else {
                        return 1;
                    }
                }

                return parseValue(value, 'number', 1, [1, 999]);

            case 'momentum':
                if (value === true || type == 'object') {
                    return {
                        ratio: parseValue(value.ratio, 'number', 1000, [1, 10000]),
                        bounce: parseValue(value.bounce, 'boolean', true),
                        bounceRatio: parseValue(value.bounceRatio, 'int', 1, [1, 10]),
                        minVelocity: parseValue(value.minVelocity, 'number', 0.02, [0.001, 1]),
                        velocityRatio: parseValue(value.velocityRatio, 'int', 1, [1, 5])
                    };
                }
                break;

            case 'pagination':
                if (type == 'object') {
                    return {
                        selector: parseValue(value.selector, 'string'),
                        type: parseValue(value.type, 'pagination.type', 'bullets'),
                        renderBullet: parseValue(value.renderBullet, 'function', () => { return '<span></span>'; }),
                        renderFraction: parseValue(value.renderFraction, 'function', (i, t) => { return [i, '/', t]; }),
                        actionScope: parseValue(value.actionScope, 'boolean', false),
                        html: parseValue(value.html, 'boolean', false)
                    };
                }
                break;

            case 'pagination.type':
                if (value == 'bullets') return 'bullets';
                if (value == 'fraction') return 'fraction';
                if (value == 'progressbar') return 'progressbar';
                break;

            case 'navigation':
                if (type == 'object') {
                    return {
                        prev: parseValue(value.prev, 'string'),
                        next: parseValue(value.next, 'string'),
                        size: parseValue(value.size, 'number', 50, [1, 99]),
                        color: parseValue(value.color, 'string'),
                        hover: parseValue(value.hover, 'navigation.hover', false),
                        actionScope: parseValue(value.actionScope, 'boolean', false),
                    };
                }
                break;

            case 'navigation.hover':
                if (value === false) return false;
                return parseValue(value, 'number', false, [1, 1000]);

            case 'clickToEnlarge':
                if (value === true || type == 'object') {
                    return {
                        selector: parseValue(value.selector, 'string', 'img'),
                    };
                }
                break;

            case 'thumbs':
                if (type == 'object') {
                    let thumbs = parseValue(value, 'data');
                    thumbs.image = parseValue(value.image, 'string|array', 'img');
                    thumbs.text = parseValue(value.text, 'string|array');
                    return thumbs;
                }
                break;

            case 'image':
            case 'text':
                return value;
        }

        return defaultValue;
    }

    // 初始化插件
    function init() {
        container = $(data.selector);

        if (container.length == 0) {
            return false;
        } else if (container.length > 1) {
            for (let i = 0; i < container.length; i++) {
                let params = data;
                params.selector = container[i];
                new Swiper(params);
            }

            return work = false;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.target === container[0]) {
                    run();
                    observer.unobserve(container[0]);
                }
            });
        });

        // 开始观察轮播图容器
        observer.observe(container[0]);
    }

    function run(resize = false) {
        // 窗口大小变化
        windowResize || initWindowResize();

        if (container.is(':hidden')) {
            return work = false;
        } else {
            work = true;
        }

        container.width(parseInt(container.width()));
        if (data.vertical && data.slidesPerView == 'auto') {
            data.slidesPerView = 1;
        }

        setThumbs();

        // 移除之前复制的元素
        $('.swiper-duplicate', container).remove();

        if (container.length == 0) return false;

        initSelfAdaption();

        if (resize === false) {
            slide = $('.swiper-cell', container);
            let html = '';
            slide.each(function () {
                html += $(this).prop('outerHTML');
            });
            slide.remove();
            container.prepend('<div class="swiper-container"><div class="swiper-wrapper">' + html + '</div></div>');
        }

        container.children('.swiper-container').css('height', '100%');
        wrapper = container.children('.swiper-container').css('overflow', 'hidden').children('.swiper-wrapper');
        width = container.width();
        height = container.height();
        slideHeight = [];
        wrapperPositionRange = [0];
        slide = $('.swiper-cell', wrapper).addClass('swiper-native');
        native = $('.swiper-native', wrapper);
        index = resize ? index : (data.initialSlide < slide.length ? data.initialSlide : slide.length - 1);
        indexOld = index;

        if (data.vertical) {
            wrapper.width('100%');
            slide.each((index, value) => slide.eq(index).height((height - data.spaceBetween * (data.slidesPerView - 1)) / data.slidesPerView))
        }

        // if (data.slidesPerView > slide.length) {
        //     data.slidesPerView = slide.length;
        // }

        // 垂直
        if (data.vertical) {
            wrapper.css('width', '100%')
            slide.css('width', '100%')
        }

        if (data.slidesPerView === false) {
            container.css('overflow', 'unset');
            wrapper.css('width', '100%');
        }

        data.fitDistance = data.fitDistance < 1 ? width * data.fitDistance : data.fitDistance;

        // 原生滑块设置索引属性
        native.each((index, value) => native.eq(index).data('index', index));

        // 初始化全部滑块的宽或高
        data.vertical ? initSlideHeight() : initSlideWidth();

        // 初始化无限循环
        data.loop && initLoop();

        pagination = $(data.pagination ? data.pagination.selector : '', data.pagination.actionScope ? 'body' : container);

        // 初始化元素样式
        initElementStyle();

        setTimeout(() => {
            // 原生滑块的总宽或高
            if (data.vertical) {
                native.each((index, value) => (nativeHeight += native.eq(index).outerHeight()));
                nativeHeight += data.spaceBetween * (native.length - 1);
            } else {
                native.each((index, value) => (nativeWidth += native.eq(index).outerWidth()));
                nativeWidth += data.spaceBetween * (native.length - 1);
            }

            // 滑动元素宽或高
            if (data.vertical) {
                wrapperHeight = wrapper.height();
            } else {
                wrapperWidth = wrapper.width();
            }

            // 不循环 不足一屏 居中显示
            if (data.vertical) {
                !data.loop && wrapperHeight <= width && data.centerInsufficientSlides && (() => {
                    setPosition((height - wrapperHeight) / -2);
                    lock = true;
                })();
            } else {
                !data.loop && wrapperWidth <= width && data.centerInsufficientSlides && (() => {
                    setPosition((width - wrapperWidth) / -2);
                    lock = true;
                })();
            }

            // 初始化分页
            initPagination();

            // 滑动元素滑动范围
            if (data.vertical) {
                wrapperPositionRange.push(wrapperHeight - height);

                data.centeredSlides && !data.loop && !data.centeredSlidesBounds && (() => {
                    wrapperPositionRange[0] -= (height - slideHeight[0]) / 2;
                    wrapperPositionRange[1] += (height - slideHeight[slideHeight.length - 1]) / 2;
                })();
            } else {
                wrapperPositionRange.push(wrapperWidth - width);

                data.centeredSlides && !data.loop && !data.centeredSlidesBounds && (() => {
                    wrapperPositionRange[0] -= (width - slideWidth[0]) / 2;
                    wrapperPositionRange[1] += (width - slideWidth[slideWidth.length - 1]) / 2;
                })();
            }

            // 初始化前进后退按钮
            initNavigation();

            // 初始化滑块点击事件
            initClick();

            // 初始化点击放大
            initClickToEnlarge();

            // 初始化缩略图
            initThumbs();

            // 设置当前显示的滑块的索引 因为可能有默认值 默认不是第一个滑块
            setPositionByNativeIndex(false);

            // 拖动时不触发a标签点击事件
            $('a', wrapper).click(function (e) {
                if (moveType == 2) {
                    event.preventDefault();
                    return false;
                }
            });

            // 初始化定时器
            initTimer();

            // 初始化拖动事件
            initDrag();

            for (let i = 0; i < queue.length; i++) {
                queue[i]()
            }
            queue = [];

            self.complete = true;
        }, 500);
    }

    function setThumbs() {
        if (!data.parent) return false;

        container.addClass('swiper').html('');

        if (data.text) {
            $('.swiper-container .swiper-wrapper .swiper-cell.swiper-native ' + data.text, data.parent).each(function () {
                container.append('<div class="swiper-cell"><span>' + $(this).html() + '</span></div>');
            });
        } else if (data.image) {
            $('.swiper-container .swiper-wrapper .swiper-cell.swiper-native ' + data.image, data.parent).each(function () {
                container.append('<div class="swiper-cell"><img src="' + $(this).attr('src') + '"></div>');
            });
        }
    }

    // 窗口大小变化初始化插件
    function resize() {
        if (!work) return false;

        self.complete = false;

        container.css('width', '100%').html(container.html());

        wrapper = wrapperWidth = wrapperHeight = width = height = slideWidth = slideHeight = wrapperPositionRange = prevButton = nextButton = pagination = [];
        slide = native = null;
        prevAll = nextAll = [];
        nativeWidth = prevWidth = nextWidth = 0;
        pageTotal = indexOld = animateId = dragDirection = thumbs = timer = null;
        moveType = 0;
        lock = false;
        border = { left: [], right: [], center: [] };

        data = deepClone(original);

        run(true);
        // self.bind(original_bind);
    }

    // 初始化自适应
    function initSelfAdaption() {
        if (!data.selfAdaption) return false;

        let windowWidth = $(window).width(), info;

        for (let key in data.selfAdaption) {
            if (windowWidth > parseInt(key)) info = data.selfAdaption[key];
        }

        for (let key in info) {
            if ($.type(info[key]) == 'object') {
                for (let key2 in info[key]) {
                    if ($.type(data[key]) != 'object') data[key] = {};
                    data[key][key2] = info[key][key2];
                }
            } else {
                data[key] = info[key];
            }
        }
    }

    // 初始化全部滑块的宽
    function initSlideWidth() {
        // 清空原有内容
        slideWidth = [];

        // 移除滑块的外边距 只能通过参数的间距设置
        slide.css('margin', 0);

        // 获取滑块的宽
        if (data.slidesPerView == 'auto') {
            // 自动时 获取元素的宽
            slide.each((index, value) => slideWidth.push(slide.eq(index).outerWidth()));
        } else {
            // 如果每页显示的滑块数量超过了滑块的总数量
            // data.slidesPerView > slide.length && (data.slidesPerView = slide.length);
            // 设置了数量时 根据数量和间距计算
            slide.each((index, value) => slideWidth.push((width - data.spaceBetween * (data.slidesPerView - 1)) / data.slidesPerView));
        }
    }

    // 初始化全部滑块的高
    function initSlideHeight() {
        // 清空原有内容
        slideHeight = [];

        // 移除滑块的外边距 只能通过参数的间距设置
        slide.css('margin', 0);

        slide.each((index, value) => slideHeight.push(slide.eq(0).outerHeight()));

        // 获取滑块的高
        // if (data.slidesPerView == 'auto') {
        //     // 自动时 获取元素的宽
        //     slide.each((index, value) => slideHeight.push(slide.eq(index).outerHeight()));
        // } else {
        //     // 如果每页显示的滑块数量超过了滑块的总数量
        //     // data.slidesPerView > slide.length && (data.slidesPerView = slide.length);
        //     // 设置了数量时 根据数量和间距计算
        //     slide.each((index, value) => slideHeight.push((height - data.spaceBetween * (data.slidesPerView - 1)) / data.slidesPerView));
        // }
    }

    // 初始化无限循环
    function initLoop() {
        if (data.slidesPerView === false) return false;

        // 需要复制的滑块的数量 如果显示的滑块为自动 则复制全部滑块
        let length = data.slidesPerView == 'auto' ? slide.length : (data.slidesPerView < 10 ? data.slidesPerView : 10);

        if (slide.length < data.slidesPerView) return data.loop = false;

        // 复制到前面的滑块 移除‘原生’class 添加‘复制’和‘开始’class
        let start = slide.slice(length * -1).clone(true).removeClass('swiper-native').addClass(['swiper-duplicate', 'swiper-start']);

        // 复制到后面的滑块 移除‘原生’class 添加‘复制’和‘结束’class
        let end = slide.slice(0, length).clone(true).removeClass('swiper-native').addClass(['swiper-duplicate', 'swiper-end']);

        // $('a', start).removeAttr('data-lightbox');
        // $('a', end).removeAttr('data-lightbox');

        // 添加到滑动元素中
        wrapper.prepend(start).append(end);

        // 重新定义滑块 前面复制的滑块 后面复制的滑块
        slide = $('.swiper-cell', wrapper);
        prevAll = start;
        nextAll = end;

        // 开发用 修改滑块中的数字用来区分原生和复制
        // prevAll.each((index, value) => $('span', prevAll.eq(index)).html('-' + $('span', prevAll.eq(index)).html()));
        // nextAll.each((index, value) => $('span', nextAll.eq(index)).html('+' + $('span', nextAll.eq(index)).html()));

        // 初始化全部滑块的宽或高
        data.vertical ? initSlideHeight() : initSlideWidth();

        // 原生滑块的总宽 前面复制的滑块的总宽 后面复制的滑块的总宽
        if (data.vertical) {
            // nativeHeight = sum(slideHeight.slice(prevAll.length, prevAll.length + native.length)) + data.spaceBetween * (native.length - 1);
            prevHeight = sum(slideHeight.slice(0, prevAll.length)) + data.spaceBetween * prevAll.length;
            nextHeight = sum(slideHeight.slice(prevAll.length + native.length)) + data.spaceBetween * prevAll.length;
        } else {
            // nativeWidth = sum(slideWidth.slice(prevAll.length, prevAll.length + native.length)) + data.spaceBetween * (native.length - 1);
            prevWidth = sum(slideWidth.slice(0, prevAll.length)) + data.spaceBetween * prevAll.length;
            nextWidth = sum(slideWidth.slice(prevAll.length + native.length)) + data.spaceBetween * prevAll.length;
        }
    }

    // 初始化元素样式
    function initElementStyle() {
        let before, after, slideSize;
        if (data.vertical) {
            before = 'margin-top';
            after = 'margin-bottom';
            slideSize = slideHeight;

            // 滑块设置宽度 间距 定位
            slide.each((index, value) => {
                slide.eq(index).outerHeight(slideSize[index]).css(before, data.spaceBetween / 2).css(after, data.spaceBetween / 2);
            });
        } else {
            before = 'margin-left';
            after = 'margin-right';
            slideSize = slideWidth;

            // 滑块设置宽度 间距 定位
            slide.each((index, value) => {
                slide.eq(index).outerWidth(slideSize[index]).css(before, data.spaceBetween / 2).css(after, data.spaceBetween / 2);
            });
        }


        slide.first().css(before, 0);
        slide.last().css(after, 0);

        // 设置滑动元素的样式
        wrapper.css('transform', 'translate3d(0px, 0px, 0px)').css('opacity', 1);

        // 图片禁止拖动 只能在初始化无限循环后执行 因为里面会重置slide对象
        $('img', slide).css('-webkit-user-drag', 'none');
        $('a', slide).css('-webkit-user-drag', 'none');
        container.css('user-select', 'none');
    }

    // 初始化分页
    function initPagination() {
        // 分组列表
        let list = [];

        // 如果第一个不是开头 需要手动创建一个组
        slide.eq(0).data('index') % data.slidesPerGroup != 0 && list.push([]);

        slide.each(i => {
            let index = slide.eq(i).data('index');

            // 倍数时新建 否则往最后一个组里加
            if (index % data.slidesPerGroup == 0) {
                list.push([slide.eq(i)]);
            } else {
                list[list.length - 1].push(slide.eq(i));
            }
        });

        // 从0开始
        border.left.push(0);

        for (let i = 0; i < list.length; i++) {
            let total = 0;
            for (let j = 0; j < list[i].length; j++) {
                if (data.vertical) {
                    total += list[i][j].outerHeight() + data.spaceBetween;
                } else {
                    total += list[i][j].outerWidth() + data.spaceBetween;
                }
            }
            border.left.push(border.left[border.left.length - 1] + total);
        }

        // 最后一个元素没有右边距 所有减去一个
        border.left[border.left.length - 1] -= data.spaceBetween;

        // 复制给右滑
        border.right = JSON.parse(JSON.stringify(border.left));

        // 除了第一个和最后一个 其他元素减去一个间隔
        for (var i = 0; i < border.right.length; i++) {
            if (i == 0 || i == border.right.length - 1) continue;

            border.right[i] -= data.spaceBetween;
        }

        // 复制给中间
        border.center = JSON.parse(JSON.stringify(border.left));

        // 除了第一个和最后一个 其他元素减去半个间隔
        for (var i = 0; i < border.center.length; i++) {
            if (i == 0 || i == border.center.length - 1) continue;

            border.center[i] -= data.spaceBetween / 2;
        }

        // 最大页码
        if (data.vertical) {
            if (data.loop || data.centeredSlides) {
                pageTotal = native.length
            } else if (data.slidesPerView == 'auto') {
                pageTotal = nativeHeight <= height ? 1 : getNativeIndexByPosition(nativeHeight - height) + 2
            } else {
                pageTotal = Math.ceil((native.length - data.slidesPerView) / data.slidesPerGroup) + 1
            }
        } else {
            if (data.loop || data.centeredSlides) {
                pageTotal = native.length
            } else if (data.slidesPerView == 'auto') {
                pageTotal = nativeWidth <= width ? 1 : getNativeIndexByPosition(nativeWidth - width) + 2
            } else {
                pageTotal = Math.ceil((native.length - data.slidesPerView) / data.slidesPerGroup) + 1
            }
        }

        // 一页多个后的最大页码
        if (data.slidesPerGroup > 1) {
            pageTotal = roundUp(native.length / data.slidesPerGroup);
        }

        // 分页按钮和点击事件
        data.pagination && !data.thumbs ? (() => {
            // 只有一页时隐藏按钮 所以没有点击事件
            if (pageTotal == 1) return false;

            // 圆点
            if (data.pagination.type == 'bullets') {
                if (!data.pagination.html) {
                    // 添加ul元素 li元素和span元素
                    pagination.html('<ul></ul>');
                    for (var i = 0; i < pageTotal; i++) {
                        $('ul', pagination).append('<li>' + data.pagination.renderBullet(i) + '</li>')
                    }
                }

                // 每个span元素添加点击事件
                $('ul li', pagination).children().click(function () {
                    // 移动类型分页
                    moveType = 6;

                    index = getTargetIndex('pagination', $(this).parents('li').index());
                    setPositionByNativeIndex(true, () => (moveType = 0));
                });
            }

            // 分式
            else if (data.pagination.type == 'fraction') {
                // 添加元素 设置当前页和最大页
                pagination.html('<span></span><span></span><span></span>');
            }

            // 进度条
            else if (data.pagination.type == 'progressbar') {
                // 添加元素 设置当前页和最大页
                pagination
                    .css('width', '100%')
                    .css('height', 8)
                    .css('background', 'rgba(0, 0, 0, .25)')
                    .html('<div class="swiper-pagination-progressbar" style="height: 100%; background: red;"></div>');
            }
        })() : pagination.remove();
    }

    // 初始化前进后退按钮
    function initNavigation() {
        if (!data.navigation) return prevButton = $(), nextButton = $();

        prevButton = $(data.navigation.prev, data.navigation.actionScope ? 'body' : container);
        nextButton = $(data.navigation.next, data.navigation.actionScope ? 'body' : container);

        if (typeof data.slidesPerView === 'number' && data.slidesPerView >= native.length) {
            prevButton.addClass('disabled');
            nextButton.addClass('disabled');
        } else {
            prevButton.removeClass('disabled');
            nextButton.removeClass('disabled');
        }

        // 前进后退按钮点击事件 禁止用户选择 防止双击时选中内容
        prevButton.click(prev).css('user-select', 'none');
        nextButton.click(next).css('user-select', 'none');

        // 如果鼠标悬停时显示按钮
        data.navigation.hover && (() => {
            prevButton.css('opacity', 0);
            nextButton.css('opacity', 0);
            prevButton.css('opacity');
            nextButton.css('opacity');
            container.mouseover(() => (prevButton.css('opacity', 1), nextButton.css('opacity', 1)));
            container.mouseout(() => (prevButton.css('opacity', 0), nextButton.css('opacity', 0)));
            prevButton.css('transition-duration', data.navigation.hover + 'ms');
            nextButton.css('transition-duration', data.navigation.hover + 'ms');
        })();
    }

    // 初始化滑块点击事件
    function initClick() {
        slide.click(function (e) {
            moveType == 0 && (() => {
                let key = $(this).data('index');

                // 移动到自己
                if (data.clickToThis) {
                    // 移动类型分页
                    moveType = 6;

                    index = key;
                    setPositionByNativeIndex(true, () => (moveType = 0));

                }

                data.click && data.click($(this).data('index'));
            })();
        });
    }

    // 初始化点击放大
    function initClickToEnlarge() {
        if (!data.clickToEnlarge) return false;

        let name = 'lightbox-' + data.selector.substr(1)

        slide.each((index, value) => {
            let e = slide.eq(index), href = $(data.clickToEnlarge.selector, e).attr('src') || '';
            e.html('<a data-lightbox="' + name + '" href="' + href + '">' + e.html() + '</a>');
        });

        // a标签禁止拖动
        $('a[data-lightbox]', container).css('-webkit-user-drag', 'none');
    }

    // 初始化缩略图
    function initThumbs() {
        if (!data.thumbs) return false;

        let old = data.thumbs.click || null;

        data.thumbs.click = function (key) {
            old && old(key);

            // 移动类型分页
            moveType = 6;

            index = key;
            setPositionByNativeIndex(true, () => (moveType = 0));
        }

        data.thumbs.parent = container;

        let selector = $(data.thumbs.selector, container);
        if (selector.length == 0) {
            selector = $(data.thumbs.selector);
        }
        selector = selector.filter(':visible');
        if (selector.length == 0) {
            return false;
        }
        data.thumbs.selector = selector;
        thumbs = new Swiper(data.thumbs);
        thumbs.bind(self);
        thumbs.queue(() => thumbs.setThumbsIndex(data.initialSlide));
    }

    // 初始化监听窗口大小变化
    function initWindowResize() {
        windowResize = true;

        let timer;

        $(window).resize(e => {
            // if (enable) {
            clearTimeout(timer);
            timer = setTimeout(() => resize(), 500)
            // }
        });
    }

    // 初始化定时器
    function initTimer() {
        // 如果开启自动切换 创建定时器
        data.autoplay && (() => {
            timer && clearTimeout(timer);
            timer = setTimeout(next, data.delay)
        })();

        if (data.pauseOnMouseEnter) {
            // 鼠标移入 清空定时器
            container.mouseover(() => timer && (clearInterval(timer), timer = null));

            // 鼠标移出 重新定义定时器
            container.mouseout(() => timer || (timer = data.autoplay ? setTimeout(next, data.delay) : null));
        }
    }

    // 初始化拖动事件
    function initDrag() {
        var status = true, disabled = () => {
            $('img', slide).css('-webkit-user-drag', 'auto');
            $('a', slide).css('-webkit-user-drag', 'auto');
            container.css('user-select', 'auto');
        };

        // 全部内容小于一屏时
        if ((!data.vertical && nativeWidth <= width) || (data.vertical && nativeHeight <= height)) {
            // 如果居中 可以拖动
            if (data.centeredSlides) {
                // 如果无缺口 则不能拖动 因为左侧要贴边
                if (data.centeredSlidesBounds) status = false;
            } else {
                // status = false;
            }
        }

        // 无限循环时可以拖动
        if (status && data.loop) status = true;

        // 禁止拖动
        if (!data.allowTouchMove) status = false;

        if (!status) return disabled();

        // 内容不足一屏幕且不居中 或关闭拖动时 没有拖动事件
        if (data.vertical) {
            if ((nativeHeight <= height && !data.centeredSlides) || !data.allowTouchMove) return disabled();
        } else {
            if ((nativeWidth <= width && !data.centeredSlides) || !data.allowTouchMove) return disabled();
        }

        // 鼠标悬停设置成手
        data.grabCursor && wrapper.css('cursor', 'grab');

        //  正在移动       元素位置     开始时鼠标坐标  开始时间    上次鼠标坐标   第一次移动方向   上次移动方向    整体移动距离 整体移动方向 惯性
        var move = false, elPosition, startPosition, startTime, lastPosition, firstDirection, lastDirection, distance, direction, momentum;

        // 被拖动元素
        var target = null, otherTime = 0, otherPosition = [0, 0];

        // 鼠标按下了开始了 点击的唯一标识
        var start = false, id = Math.random();

        // 鼠标按下
        let startFunction = e => {
            if (e.pointerType == 'mouse' && e.button != 0) return false;
            // if (!((e.pointerType == 'mouse' && e.button == 0) || e.pointerType == 'touch')) return false;

            target = e.target;

            start = id;

            // 贴边时不能拖动
            // if (moveType == 5) return false;

            // 鼠标按下后鼠标悬停设置成抓手
            data.grabCursor && wrapper.css('cursor', 'grabbing');

            // 此时元素可能正在移动 停止移动
            stop();

            // 停止自动播放
            autoplay = false;

            // 开始拖动 元素位置 开始时鼠标坐标 开始时间 上次鼠标坐标 整体移动距离 整体移动方向 惯性
            move = true;
            elPosition = getPosition();
            startPosition = data.vertical ? typeof e.clientY == 'number' ? e.clientY : e.touches[0].clientY : typeof e.clientX == 'number' ? e.clientX : e.touches[0].clientX;
            startTime = time();
            lastPosition = data.vertical ? typeof e.clientY == 'number' ? e.clientY : e.touches[0].clientY : typeof e.clientX == 'number' ? e.clientX : e.touches[0].clientX;
            firstDirection = null;
            lastDirection = null;
            distance = 0;
            direction = null;
            momentum = !!data.momentum;
        }

        // 鼠标移动 获取移动距离 设置滑动元素移动距离
        let moveFunction = e => {
            // 没有开始事件不执行
            if (!move) return false

            let x;

            if (data.vertical) {
                x = typeof e.clientY == 'number' ? e.clientY : e.touches[0].clientY;
            } else {
                x = typeof e.clientX == 'number' ? e.clientX : e.touches[0].clientX;
            }

            // 本次移动方向
            let currentDirection = x - lastPosition;
            currentDirection = currentDirection ? (currentDirection < 0 ? 'left' : 'right') : lastDirection;

            if (exchange(currentDirection)) {
                elPosition = getPosition();
                startPosition = x;
                startTime = time();
                lastPosition = x;
                lastDirection = null;
                distance = 0;
                direction = null;
                momentum = !!data.momentum;
            }

            // 第一次移动方向
            firstDirection || (firstDirection = currentDirection);

            // 如果两次方向不一样 重新开始计算
            lastDirection && currentDirection && lastDirection != currentDirection && (() => {
                elPosition = getPosition();
                startPosition = x;
                startTime = time();
                lastPosition = x;
                lastDirection = null;
                distance = 0;
                direction = null;
                momentum = !!data.momentum;
            })();

            // 该方向上的移动距离
            distance = Math.abs(x - startPosition);

            if (distance) {
                // 移动类型鼠标拖动
                moveType = 2;
            }

            // 上次鼠标坐标 上次移动方向 整体移动方向
            lastPosition = x;
            lastDirection = currentDirection;
            direction = lastDirection;

            // 设置轮播图位置 根据鼠标移动距离计算轮播图位置
            setPosition(getWrapperPositionByDrag(elPosition, distance, currentDirection));
        }

        // 鼠标松开
        let endFunction = e => {
            if (start === false || start !== id) {
                return;
            }

            autoplay = true;

            // 鼠标松开后鼠标悬停设置成松手
            data.grabCursor && wrapper.css('cursor', 'grab');

            // if (time() - startTime < 200 && distance == 0 && move) {
            //     move = false;
            //     moveType = 0;
            //     setPositionByNativeIndex(true);

            //     return false;
            // }

            // 单击 点击的同时拖拽 时间差是0 不知道为什么
            const currentTime = time()
            if (currentTime - startTime < 200 && distance == 0 && start === id) {
                if (target.tagName === 'A') {
                    window.location.href = $(target).attr('href');
                } else if ($(target).parents('a').length) {
                    window.location.href = $(target).parents('a').attr('href');
                } else {
                    isMobile() && $(target).click();
                }

                move = false;
                moveType = 0;
                target = null;
                return dragCallback(direction);
            }

            start = false;

            if (!target) {
                if (time() - otherTime < 200 && otherPosition[0] == e.changedTouches[0].clientX && otherPosition[1] == e.changedTouches[0].clientY) {
                    isMobile() && $(e.target).click();
                }
                return false;
            }

            if (target.tagName === 'A') {
                aClickDisabled(target);
            } else if ($(target).parents('a').length) {
                aClickDisabled($(target).parents('a')[0]);
            }

            if (move) {
                // 松开后关闭状态
                move = false;

                // 移动类型静止
                moveType = 0;

                // 没有移动 贴边
                // if (distance == 0) {
                //     dragCallback(null);
                //     return false;
                // }

                // 倒带模式且没有无限循环
                if (data.rewind && !data.loop) {
                    let position = getPosition();
                    if (position > wrapperPositionRange[1] && distance > data.fitDistance) {
                        return moveType = 5, index = 0, setPositionByNativeIndex(true, () => moveType = 0);
                    }
                    if (position < wrapperPositionRange[0] && distance > data.fitDistance) {
                        return moveType = 5, index = pageTotal - 1, setPositionByNativeIndex(true, () => moveType = 0);
                    }
                }

                // 超出范围后不进行惯性运动 直接反弹
                checkBeyond() && (momentum = false);

                // 惯性运动持续时间 默认0
                let duration = 0;

                // 反方向
                data.touchRatio < 0 && (direction = direction == 'left' ? 'right' : 'left');

                // 记录拖拽方向
                dragDirection = direction;

                // 如果有惯性 执行并完成拖拽回调 否则直接执行拖拽回调
                data.freeMode && momentum ? (() => {
                    // 拖动距离 单位px
                    let s1 = startPosition - e.clientX;

                    // 拖动时间 单位ms
                    let t1 = time() - startTime;

                    // 末速度 单位px/ms
                    let v1 = s1 * 2 / t1;

                    // 平均速度达到要求才能触发 否则直接执行拖拽回调
                    Math.abs(v1 / 2) >= data.momentum.minVelocity ? (() => {
                        // 滑动加速度 px/ms
                        let a = -0.001;

                        // 滑动时间 单位ms
                        let t2 = Math.abs(v1 * data.momentum.velocityRatio / a * -1) * 0.2;

                        // 滑动距离 单位px
                        let s2 = v1 * t2 * 0.2;

                        // 滑动完距离左侧的距离
                        let left = parseFloat(getPosition() + s2);

                        // 计算反弹强度
                        let val = data.momentum.bounce ? data.momentum.bounceRatio * 100 : 0;
                        let min = wrapperPositionRange[0] - val;
                        let max = wrapperPositionRange[1] + val;

                        // 判断是否超出反弹范围
                        left < min && (left = min);
                        left > max && (left = max);

                        // 是否反弹
                        let bounce = left < wrapperPositionRange[0] || left > wrapperPositionRange[1];

                        // 移动类型惯性运动
                        moveType = 3;

                        // 移动到这里 记录运动持续时间
                        setPosition(left, duration = data.momentum.ratio, () => dragCallback(direction), 3);
                    })() : dragCallback(direction);
                })() : dragCallback(direction);
            } else {
                moveType = 0;
            }

            move = false;
            moveType = 0;
            target = null;
        }

        // 鼠标按下 开始处理移动 获取鼠标当前坐标 获取滑动元素当前移动距离
        if (isMobile()) {
            wrapper.on('touchstart', startFunction);
            $('html').on('touchstart', e => {
                otherTime = time();
                otherPosition = [
                    typeof e.clientX == 'number' ? e.clientX : e.touches[0].clientX,
                    typeof e.clientY == 'number' ? e.clientY : e.touches[0].clientY
                ];
            })
            $('html').on('touchmove', moveFunction);
            $('html').on('touchend', endFunction);
        } else {
            wrapper.on('pointerdown', startFunction);
            $('html').on('pointermove', moveFunction);
            $('html').on('pointerup', endFunction);
        }
    }

    // 阻止默认事件
    function aClickHandler(e) {
        e.preventDefault();
    }

    // 禁用
    function aClickDisabled(el) {
        el.addEventListener('click', aClickHandler);
    }

    // 启用
    function aClickEnabled(el) {
        el.removeEventListener('click', aClickHandler);
    }

    // 拖动事件的回调函数 如果存在惯性运动 则在运动完成后执行
    function dragCallback(direction) {
        // 移动类型静止
        moveType = 0;

        // 当前位置 是否超出范围
        let position = getPosition(), beyond = checkBeyond();

        // 回调 如果超出需要反弹 弹完执行 否则直接执行
        function callback() {
            // 移动类型静止
            moveType = 0;

            // 自由模式 根据当前位置设置分页
            if (data.freeMode) {
                // 根据位置获取全部滑块索引 根据全部滑块索引获取原生滑块索引
                index = getNativeIndexBuSlideIndex(getSlideIndexByPosition(position, direction));

                // 使用索引设置分页
                setPaginationByNativeIndex();

                // 首尾互换
                exchange(direction);
            }

            // 不是自由模式 贴边 根据方向找出切换到哪个滑块 根据滑块设置位置
            else {
                // 根据位置获取全部滑块中当前滑块的索引
                index = getSlideIndexByPosition(position, direction);

                // 移动类型贴边
                moveType = 5;

                if (data.centeredSlides && data.centeredSlidesBounds) {
                    if (beyond == 'left') index = 0;
                    if (beyond == 'right') index = native.length - 1;
                }

                // 根据全部滑块的索引设置位置
                setPositionBySlideIndex(true, () => (moveType = 0, exchange(direction)));
            }
        }

        // 超出最左侧 反弹回来 回来后继续执行回调
        if (beyond == 'left') {
            // 移动类型超出两侧反弹
            moveType = 4;
            setPosition(wrapperPositionRange[0], true, callback, 1);
        }

        // 超出最右侧 反弹回来 回来后继续执行回调
        else if (beyond == 'right') {
            // 移动类型超出两侧反弹
            moveType = 4;
            setPosition(wrapperPositionRange[1], true, callback, 1);
        }

        // 直接执行回调
        else {
            callback();
        }
    }

    // 切换到上一个滑块
    function prev(e) {
        // 按钮被禁用
        if (prevButton.hasClass('disabled')) return false;

        // 移动类型前进后退
        moveType = 8;
        index = getTargetIndex('prev');
        setPositionByNativeIndex(true, callback = () => (moveType = 0));
    }

    // 切换到下一个滑块
    function next(e) {
        // this 自动播放时为window 点击按钮时为按钮元素
        let type = this != window, callback = () => {
            moveType = 0;
            type || (() => {
                clearTimeout(timer);
                timer = setTimeout(next, data.delay);
            })();
        };

        if (!type && !autoplay) {
            return false;
        }

        // 点击按钮且按钮被禁用
        if (nextButton.hasClass('disabled')) return false;

        // 倒带和移动类型前进后退
        let rewind = this == window ? !data.stopOnLastSlide : data.rewind;
        moveType = this == window ? 1 : 7;

        // 下一个的滑块索引
        index = getTargetIndex('next', 0, this == window);

        // 执行
        setPositionByNativeIndex(true, callback);
    }

    // 根据全部滑块的索引设置位置
    function setPositionBySlideIndex(animate = true, callback = null) {
        if (data.loop) {
            // 如果是无限循环 获取当前滑块相对原生滑块的下标 -2 -1 | 0 1 | 2 3
            index -= prevAll.length;
        } else {
            // 否则根据全部滑块的索引获取原生滑块的索引
            index = getNativeIndexBuSlideIndex(index);
        }

        // 根据原生滑块的索引设置位置
        setPositionByNativeIndex(animate, callback);
    }

    // 根据原生滑块的索引设置位置
    function setPositionByNativeIndex(animate = true, callback = null) {
        // 如果不循环且索引不存在 取近似值
        data.loop || (() => {
            if (index < 0) index = 0;
            if (index > slide.length - 1) index = slide.length - 1;
        })();

        // 滑块 滑块的目标位置
        let el, x;

        // 如果没有开启无限循环或切换到原生滑块
        if (!data.loop || between(index, 0, pageTotal * data.slidesPerGroup - 1) === true) {
            // 滑块的目标位置
            x = getPositionBySlide(el = native.eq(index));

            // 两侧贴边
            x > wrapperPositionRange[1] && (x = wrapperPositionRange[1]);
            x < wrapperPositionRange[0] && (x = wrapperPositionRange[0]);
        }

        // 无限循环时切换到了左侧的复制滑块
        else if (data.loop && between(index, 0 - prevAll.length, -1) === true) {
            // 设置原生滑块索引
            index = (el = prevAll.eq(prevAll.length - index * -1)).data('index');

            // 滑块的目标位置 回调函数
            x = getPositionBySlide(el);

            // let func = callback;
            // callback = () => (func && func(), setPositionByNativeIndex(false));
        }

        // 无限循环时切换到了右侧的复制滑块
        else if (data.loop && between(index, pageTotal * data.slidesPerGroup, pageTotal * data.slidesPerGroup + nextAll.length - 1) === true) {
            // 设置原生滑块索引
            index = (el = nextAll.eq(index - pageTotal * data.slidesPerGroup)).data('index');

            // 滑块的目标位置 回调函数
            x = getPositionBySlide(el);

            // let func = callback;
            // callback = () => (func && func(), setPositionByNativeIndex(false));
        }


        // 设置位置
        setPosition(x, animate, callback);

        // 根据原生滑块的索引设置分页
        setPaginationByNativeIndex();
    }

    // 根据原生滑块的索引设置分页
    function setPaginationByNativeIndex(key = null) {
        key = key == null ? index : key;

        // 不循环不倒带时到头就不能点了
        !data.loop && !data.rewind && (() => {
            prevButton.removeClass('disabled');
            nextButton.removeClass('disabled');
            key == 0 && prevButton.addClass('disabled');
            getPageByIndex(key) == pageTotal - 1 && nextButton.addClass('disabled');
        })();

        if (!data.pagination) return false;

        // 根据滑块索引获取页码
        key /= data.slidesPerGroup;

        // 一组多个时页码可能会超出
        key = key > pageTotal - 1 ? pageTotal - 1 : key;

        // 圆点
        if (data.pagination.type == 'bullets') {
            pagination.each(i => {
                $('li', pagination.eq(i)).removeClass('active');
                $('li', pagination.eq(i)).eq(key).addClass('active');
            });
        }

        // 分式
        else if (data.pagination.type == 'fraction') {
            let text = data.pagination.renderFraction(key + 1, pageTotal)

            pagination.each(i => {
                let span = $('span', pagination.eq(i));
                span.eq(0).html(text[0]);
                span.eq(1).html(text[1]);
                span.eq(2).html(text[2]);
            });
        }

        // 分式
        else if (data.pagination.type == 'progressbar') {
            pagination.each(i => $('.swiper-pagination-progressbar', pagination.eq(i)).width(pagination.eq(i).width() / pageTotal * ((key) + 1)));
        }
    }

    // 根据位置获取全部滑块中当前滑块的索引
    function getSlideIndexByPosition(x, direction = null) {
        // 小于最小值说明左侧没有滑块了 应该定义为在最左侧 右侧 同理
        if (x < wrapperPositionRange[0]) return 0;
        if (x > wrapperPositionRange[1]) return slide.length - 1;

        // 根据方向获取边缘坐标
        let list = direction ? border[direction] : border.center;

        return data.centeredSlides ? getSlideIndexByPositionInCenter(x, list, direction) : getSlideIndexByPositionInLeft(x, list, direction);
    }

    // 根据位置获取全部滑块中当前滑块的索引 居左
    function getSlideIndexByPositionInLeft(x, list, direction) {
        // 滑块位置 切换阈值
        var position = null, fit = data.fitDistance;

        // 循环每个坐标
        for (let i = 0; i < list.length; i++) {
            if (x < list[i]) {
                continue;
            }

            // 当前的开头 下一个的开头
            let screen = list[i], next = list[i + 1];

            if (direction) {
                if (x >= screen && x < next) {
                    if (direction == 'left') {
                        if (x >= screen + fit && x < next) {
                            position = next;
                        } else {
                            position = screen;
                        }
                    } else {
                        if (x >= next - fit && x < next) {
                            position = next + data.spaceBetween;
                        } else {
                            position = screen + data.spaceBetween;
                        }
                    }

                    break;
                }
            } else {
                // 找到当前组后 获取当前组的最左侧
                if (x >= screen && x < next) {
                    position = screen + data.spaceBetween / 2;
                }
            }
        }

        list = slide.get();
        for (var i = 0; i < list.length; i++) {
            let all = i == list.length - 1 ? $(list[i]).prevAll() : $(list[i]).next().prevAll();

            if (!data.vertical && getWidthByElement(all, 3) + all.length * data.spaceBetween == position) {
                return $(list[i + 1]).index();
            }

            if (data.vertical && getHeightByElement(all, 3) + all.length * data.spaceBetween == position) {
                return $(list[i + 1]).index();
            }
        }

        return 0;
    }

    // 根据位置获取全部滑块中当前滑块的索引 居中
    function getSlideIndexByPositionInCenter(x, list, direction = null) {
        // 滑块位置 切换阈值 间隔 中间
        var position = null, fit = data.fitDistance, spaceBetween = data.spaceBetween, y = data.vertical ? Math.round(x + height / 2) : Math.round(x + width / 2);

        for (var i = 0; i < list.length - 1; i++) {
            let center = 0;
            for (var j = 0; j < i * data.slidesPerGroup; j++) {
                center += (data.vertical ? slideHeight[j] : slideWidth[j]) + spaceBetween;
            }

            center += (data.vertical ? slideHeight[i] : slideWidth[i]) / 2;
            center = Math.round(center);

            if (direction == 'left') {
                if (y <= center + fit) {
                    position = list[i];
                    break;
                } else {
                    if (i == list.length - 2) {
                        position = list[i];
                        break;
                    }
                }
            } else if (direction == 'right') {
                if (y >= center - fit) {
                    continue;
                } else {
                    position = list[i - 1] + spaceBetween;
                    break;
                }
            } else {
                if (center == y) {
                    position = list[i];
                    break;
                }
            }
        }

        list = slide.get();
        for (var i = 0; i < list.length; i++) {
            let all = i == list.length - 1 ? $(list[i]).prevAll() : $(list[i]).next().prevAll();

            if (!data.vertical && getWidthByElement(all, 3) + all.length * data.spaceBetween == position) {
                return $(list[i + 1]).index();
            }

            if (data.vertical && getHeightByElement(all, 3) + all.length * data.spaceBetween == position) {
                return $(list[i + 1]).index();
            }
        }

        return 0;
    }

    // 根据位置获取原生滑块中当前滑块的索引
    function getNativeIndexByPosition(x, direction) {
        return getNativeIndexBuSlideIndex(getSlideIndexByPosition(x, direction));
    }

    // 根据全部滑块的索引获取原生滑块的索引
    function getNativeIndexBuSlideIndex(index) {
        return slide.eq(index).data('index');
    }

    // 根据滑块获取位置
    function getPositionBySlide(el) {
        let position = 0;

        if (data.vertical) {
            el.prevAll().each((index, value) => position += el.prevAll().eq(index).outerHeight() + data.spaceBetween);
            return data.centeredSlides ? position - height / 2 + slideHeight[el.index()] / 2 : position;
        } else {
            el.prevAll().each((index, value) => position += el.prevAll().eq(index).outerWidth() + data.spaceBetween);
            return data.centeredSlides ? position - width / 2 + slideWidth[el.index()] / 2 : position;
        }
    }

    // 根据鼠标拖动距离计算轮播图当前位置
    function getWrapperPositionByDrag(wrapperPosition, distance, direction) {
        // 拖动比例
        let touchRatio = data.touchRatio, resistanceRatio = data.resistanceRatio;

        // 为0时表示拖不动
        if (touchRatio == 0) return wrapperPosition;

        // 负数表示反方向拖动
        if (touchRatio < 0) direction = direction == 'left' ? 'right' : 'left';

        // 取绝对值
        touchRatio = Math.abs(touchRatio);

        // 根据比例计算移动距离
        distance *= touchRatio;

        // 没有判断是否是无限循环，如果无限循环，到边缘时不应该降低拖拽效率
        if (data.loop) {
            resistanceRatio = 1
        }

        // 向左拖动
        if (direction == 'left') {
            // 如果当前左侧已经超出
            if (wrapperPosition < wrapperPositionRange[0]) {
                // 超出部分的距离
                let beyond = wrapperPositionRange[0] - wrapperPosition;

                // // 如果当前拖动距离不足以让元素回来
                // if (distance * resistanceRatio <= beyond) {
                //     // 拖动部分始终用比例计算
                //     distance *= resistanceRatio;
                // } else {
                //     // 否则只有超出部分用比例算
                //     // 拖动距离减去超出部分的比例计算结果 指的是拖回来后正常移动部分 再加上超出的距离
                //     distance = distance - beyond / resistanceRatio + beyond
                // }

                // 同上
                distance = (distance * resistanceRatio <= beyond) ? (distance * resistanceRatio) : (distance - beyond / resistanceRatio + beyond);
            }

            // 正常位置或右侧超出
            else {
                // 获取最大拖动距离
                let max = wrapperPositionRange[1] - wrapperPosition;

                // // 如果超出了最大拖动距离 超出部分计算比例
                // if (distance > max) {
                //     // 小于0表示此时右侧已经超出 直接计算比例 否则只有超出部分计算比例
                //     if (max < 0) {
                //         distance *= resistanceRatio;
                //     } else {
                //         distance = max + (distance - max) * resistanceRatio;
                //     }
                // }

                // 同上
                distance > max && (distance = (max < 0) ? (distance * resistanceRatio) : (distance * resistanceRatio + max * (1 - resistanceRatio)));
            }

            wrapperPosition += distance;
        }

        // 向右拖动
        if (direction == 'right') {
            // 如果当前右侧已经超出
            if (wrapperPosition > wrapperPositionRange[1]) {
                // 超出部分的距离
                let beyond = wrapperPosition - wrapperPositionRange[1];

                // // 如果当前拖动距离不足以让元素回来
                // if (distance * resistanceRatio <= beyond) {
                //     // 拖动部分始终用比例计算
                //     distance *= resistanceRatio;
                // } else {
                //     // 否则只有超出部分用比例算
                //     // 拖动距离减去超出部分的比例计算结果 指的是拖回来后正常移动部分 再加上超出的距离
                //     distance = distance - beyond / resistanceRatio + beyond
                // }

                // 同上
                distance = (distance * resistanceRatio <= beyond) ? (distance * resistanceRatio) : (distance - beyond / resistanceRatio + beyond);
            }

            // 正常位置或右侧超出
            else {
                // 获取最大拖动距离
                let max = wrapperPosition - wrapperPositionRange[0];

                // // 如果超出了最大拖动距离 超出部分计算比例
                // if (distance > max) {
                //     // 小于0表示此时右侧已经超出 直接计算比例 否则只有超出部分计算比例
                //     if (max < 0) {
                //         distance *= resistanceRatio;
                //     } else {
                //         distance = max + (distance - max) * resistanceRatio;
                //     }
                // }

                // 同上
                distance > max && (distance = (max < 0) ? (distance * resistanceRatio) : (distance * resistanceRatio + max * (1 - resistanceRatio)));
            }

            wrapperPosition -= distance;
        }

        return wrapperPosition;
    }

    // 获取目标索引
    function getTargetIndex(type, page, autoplay) {
        let list = [];
        slide.each(i => {
            if (slide.eq(i).data('index') % data.slidesPerGroup == 0) {
                list.push(i - (prevAll ? prevAll.length : 0));
            }
        });

        if (type == 'prev') {
            return data.loop ? list[list.indexOf(index) - 1] : (index == 0 ? (data.rewind ? pageTotal - 1 : index) : list[list.indexOf(index) - 1]);
        } else if (type == 'next') {
            // 倒带和移动类型前进后退
            let rewind = autoplay ? !data.stopOnLastSlide : data.rewind;

            // return data.loop ? list[list.indexOf(index) + 1] : (index == list[list.length - 1] ? (rewind ? 0 : index) : list[list.indexOf(index) + 1]);
            return data.loop ? list[list.indexOf(index) + 1] : (index == list[list.length - 1] ? (rewind ? 0 : index) : list[list.indexOf(index) + 1]);
        } else if (type == 'pagination') {
            return page * data.slidesPerGroup;
        } else if (type == 'freeMode') {

        }
    }

    // 根据滑块索引获取滑块所在页码
    function getPageByIndex(i) {
        return roundUp(i / data.slidesPerGroup);
    }

    // 获取元素的宽的和
    function getWidthByElement(el, type = 1) {
        let total = 0;
        if (type == 1) el.each(i => total += el.eq(i).width());
        if (type == 2) el.each(i => total += el.eq(i).innerWidth());
        if (type == 3) el.each(i => total += el.eq(i).outerWidth());
        if (type == 4) el.each(i => total += el.eq(i).outerWidth(true));
        return total;
    }

    // 获取元素的高的和
    function getHeightByElement(el, type = 1) {
        let total = 0;
        if (type == 1) el.each(i => total += el.eq(i).height());
        if (type == 2) el.each(i => total += el.eq(i).innerHeight());
        if (type == 3) el.each(i => total += el.eq(i).outerHeight());
        if (type == 4) el.each(i => total += el.eq(i).outerHeight(true));
        return total;
    }

    // 头和尾互换 从前面的复制元素移动到后面的复制元素 从后面的复制元素移动到前面的复制元素
    function exchange(direction) {
        // 没开启无限循环不执行
        if (!data.loop) return false;

        if (data.vertical) {
            // 当前位置
            let position = getPosition();

            // 当前在头部 移动到尾部
            if (toFixed(position, 0) < toFixed(prevHeight, 0) && direction == 'right') return setPosition(position + nativeHeight + data.spaceBetween) || true;

            // 当前在尾部 移动到头部
            // if (position > prevHeight + nativeHeight - height && direction == 'left') return setPosition(position - nativeHeight - data.spaceBetween) || true;
            if (position > toFixed(prevHeight + nativeHeight - slideHeight[slideHeight.length - 1], 2) && direction == 'left') return setPosition(position - nativeHeight - data.spaceBetween) || true;
        } else {
            // 当前位置
            let position = getPosition();

            // 当前在头部 移动到尾部
            if (toFixed(position, 0) < toFixed(prevWidth, 0) && direction == 'right') return setPosition(position + nativeWidth + data.spaceBetween) || true;

            // 当前在尾部 移动到头部
            // if (position > prevWidth + nativeWidth - width && direction == 'left') return setPosition(position - nativeWidth - data.spaceBetween) || true;
            if (position > toFixed(prevWidth + nativeWidth - slideWidth[slideWidth.length - 1], 2) && direction == 'left') return setPosition(position - nativeWidth - data.spaceBetween) || true;
        }


        return false;
    }

    // 获取当前轮播图位置
    function getPosition() {
        if (data.vertical) {
            return parseFloat(wrapper.css('transform').replace('matrix(', '').replace(')', '').split(', ')[5]) * -1;
        } else {
            return parseFloat(wrapper.css('transform').replace('matrix(', '').replace(')', '').split(', ')[4]) * -1;
        }
    }

    // 设置轮播图位置和动画
    function setPosition(x, animation = false, callback = null, type = 3) {
        if (lock) return false;

        // 属性当前的值 动画时长
        let current, duration = animation ? (animation === true ? data.speed : animation) : 0, move = true;
        if (index !== indexOld && [1, 5, 6, 7, 8].indexOf(moveType) > -1) {
            let target = index, direction = null, diff = null;

            if (moveType == 1) direction = 'right';
            if (moveType == 7) direction = 'right';
            if (moveType == 5 && dragDirection == 'left') direction = 'right';
            if (moveType == 8) direction = 'left';
            if (moveType == 5 && dragDirection == 'right') direction = 'left';

            if (direction == 'right') {
                if (index < indexOld) {
                    diff = index + native.length - indexOld;
                    target = indexOld + diff;
                } else {
                    target = index;
                }
            }

            if (direction == 'left') {
                if (index > indexOld) {
                    diff = index - native.length - indexOld;
                    target = indexOld + diff;
                } else {
                    target = index;
                }
            }

            data.change && data.change(index);
            bind && work && (() => {
                for (let i = 0; i < bind.length; i++) {
                    move = bind[i].setIndex(target, index, false);
                }

                if (move) {
                    for (let i = 0; i < bind.length; i++) {
                        bind[i].setIndex(target, index, true);
                    }
                }
            })();
        }

        if (!move) {
            callback && callback();
            return index = indexOld;
        }

        native.removeClass('swiper-active');
        native.eq(index).addClass('swiper-active');

        indexOld = index;

        callback && callback();

        moveType != 9 && thumbs && index !== thumbs.index() && thumbs.setThumbsIndex(index);

        // 位置没有变化不执行
        if (x == (current = getPosition())) return false;

        // 移动
        // wrapper.stop().animation({ marginLeft: x }, animation ? (animation === true ? data.speed : animation) : 0, easing, callback);

        // 移动
        if (data.vertical) {
            animate(wrapper, 'transform', 'translate3d(0px, { x }px, 0px)', x * -1, duration, current * -1, type, callback);
        } else {
            animate(wrapper, 'transform', 'translate3d({ x }px, 0px, 0px)', x * -1, duration, current * -1, type, callback);
        }
    }

    // 判断滑动元素是否超出范围
    function checkBeyond() {
        // 当前位置
        let current = getPosition();

        // 超出左侧返回左 超出右侧返回右
        if (current < wrapperPositionRange[0]) return 'left';
        if (current > wrapperPositionRange[1]) return 'right';

        // 没超出
        return false;
    }

    // 根据滑块元素判断是否显示出来
    function checkShowByElement(el) {
        let left = 0, right = 0, position = getPosition();

        if (data.vertical) {
            el.prevAll().each(function () {
                left += $(this).outerHeight(true);
            });
            left += parseFloat(el.css('margin-left')) || 0;
            right = left + el.outerHeight();

            return between(left, position, position + height) === true || between(right, position, position + height) === true;
        } else {
            el.prevAll().each(function () {
                left += $(this).outerWidth(true);
            });
            left += parseFloat(el.css('margin-left')) || 0;
            right = left + el.outerWidth();

            return between(left, position, position + width) === true || between(right, position, position + width) === true;
        }
    }

    /**
     * 动画效果
     * @param  {Object}   el       元素
     * @param  {String}   attr     属性名
     * @param  {String}   value    属性值模板 变量用x 如 'translate3d({ x }px, 0px, 0px)'
     * @param  {Number}   target   属性最终达到的值
     * @param  {Number}   duration 动画时长
     * @param  {Number}   current  当前属性值 没有则用 parseFloat(el.css(attr)) 有些属性获取不是数字则必须配置
     * @param  {Number}   type     动画效果 1直线运动 2开头速度慢 3结尾速度慢 4开头结尾速度慢
     * @param  {Function} callback 执行完的回调函数
     */
    function animate(el, attr, value, target, duration, current = null, type = 1, callback = null) {
        // 如果没有当前属性值 获取
        current == null && (current = parseFloat(el.css(attr)));

        // 执行间隔 开始时间 结束时间 变化的值 动画效果
        let interval = 1, startTime = time(), endTime = startTime + duration, change = target - current, easing = {
            // 匀速运动
            '1': e => { return e; },
            // 开头慢 后面快
            '2': (x, t, b, c, d) => { return c * (t /= d) * t * t * t * t + b; },
            // 结尾慢 前面快
            '3': (x, t, b, c, d) => { return c * ((t = t / d - 1) * t * t * t * t + 1) + b; },
            // 开头结尾慢 中间快
            '4': e => { return 0.5 - Math.cos(e * Math.PI) / 2; },
        };

        // 动画类型
        easing = easing[type.toString()] || easing['1'];

        // 没有时长则直接执行
        if (duration == 0) return set();

        // 记录id
        let id = animateId = createAnimateId();

        function run() {
            // id不对说明动画已经停止或开启了新动画 终止执行
            if (id != animateId) return false;

            // 剩余时长
            let surplusDuration = endTime - time();

            // 剩余比例 如果剩余时长是负数 表示时间已经过了 则比例时0 否则用剩余时长除以总时长
            let surplusProportion = surplusDuration < 0 ? 0 : surplusDuration / duration;

            // 完成比例
            let completeProportion = 1 - surplusProportion;

            // 根据动画效果处理比例
            completeProportion = easing(completeProportion, duration * completeProportion, 0, 1, duration);

            // 设置样式
            set(completeProportion);

            // 收尾切换
            exchange('left') || exchange('right');

            // 如果完成了 执行回调 清空id并终止调用 否则继续运行
            if (completeProportion == 1) {
                animateId = null;
                initTimer();
                callback && callback();
                return true;
            } else {
                setTimeout(run, interval);
            }
        }

        // 如果设置了值的格式 用字符串替换 否则直接用传值
        function set(proportion = 1) {
            let attrValue = current + change * proportion;
            return el.css(attr, value ? value.replace('{ x }', attrValue) : attrValue);
        }

        run();
    }

    // 停止动画效果
    function stop() {
        animateId = null;
    }

    // 判断数字在不在区间里
    function between(x, min, max) {
        if (x < min) return '-';
        if (x > max) return '+';
        return true;
    }

    // 获取毫秒时间戳
    function time() {
        return (new Date()).getTime();
    }

    // 小数四舍五入保留指定长度
    function toFixed(number, length) {
        number = parseFloat(number);
        if (isNaN(number)) return false;

        number = number.toString();
        var point = number.indexOf('.');
        if (point === -1) return parseInt(number);

        var decima = number.substr(point + 1);
        if (decima.length <= length) return parseFloat(number);

        var multiply = Math.pow(10, length)

        // 小数部分
        var retain = parseInt(parseFloat('0.' + decima.substr(0, length)) * multiply);

        // 小数后一位判断
        parseInt(decima.substr(length, 1)) >= 5 && (retain += 1)

        return parseInt(number) + retain / multiply;
    }

    // 数组求和
    function sum(data) {
        let value = 0;
        for (let i = 0; i < data.length; i++) {
            value += parseFloat(data[i]);
        }
        return value;
    }

    // 创建动画id并返回
    function createAnimateId() {
        return number = parseInt(Math.random() * 10000);
    }

    // 向上取整
    function roundUp(number) {
        number = parseFloat(number);

        if (isNaN(number)) return false;

        let decimal = number % 1;

        if (decimal == 0) {
            return parseInt(number);
        } else {
            return parseInt(number) + 1;
        }
    }

    // 判断是不是手机
    function isMobile() {
        const userAgentInfo = navigator.userAgent, agents = ['Android', 'iPhone', 'SymbianOS', 'Windows Phone', 'iPad', 'iPod'];

        for (let i = 0; i < agents.length; i++) {
            if (userAgentInfo.indexOf(agents[i]) >= 0) {
                return true;
            }
        }

        return false;
    }

    function deepClone(value) {
        if (typeof value !== 'object' || value === null) {
            return value;
        }

        if ('nodeType' in value && value.nodeType === 1) {
            return value;
        }

        let data = {};

        for (let key in value) {
            data[key] = deepClone(value[key]);
        }

        return data;
    }
}