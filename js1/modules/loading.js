class Loading {
    constructor() {
        // 创建遮罩层div
        this.overlay = document.createElement('div');
        this.overlay.style.position = 'fixed';
        this.overlay.style.left = '0';
        this.overlay.style.top = '0';
        this.overlay.style.width = '100%';
        this.overlay.style.height = '100%';
        this.overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        this.overlay.style.display = 'flex';
        this.overlay.style.justifyContent = 'center';
        this.overlay.style.alignItems = 'center';
        this.overlay.style.color = '#ffffff';
        this.overlay.style.zIndex = '9999'; // 确保遮罩层在最上层
        this.overlay.textContent = 'Loading...';

        // 将遮罩层添加到body中
        document.body.appendChild(this.overlay);

        // 启动点的动画
        this.animateDots();
    }

    animateDots() {
        const element = this.overlay;
        let dotCount = 0;
        this.interval = setInterval(() => {
            dotCount = (dotCount + 1) % 4; // 循环从0到3
            element.textContent = 'Loading' + '.'.repeat(dotCount);
        }, 500); // 每500毫秒更新一次
    }

    close() {
        clearInterval(this.interval); // 停止动画
        document.body.removeChild(this.overlay); // 从DOM中移除加载提示
    }
}