# Changelog

本项目遵循 [Semantic Versioning](https://semver.org/lang/zh-CN/) 规范。

## [Unreleased]

### 修复

- `inkstone_site_branding()` 在输出自定义 Logo 后补上 `return`：原实现会同时输出 Logo 与文字站名，站点头部出现两份品牌。
- `inkstone_filter_caption_shortcode()` 的 caption 由裸输出改为 `wp_kses_post()`，class 属性补`sanitize_html_class()`。
- `inkstone_build_dynamic_css()` 修复除零：当正文字号被改成0 或超出 14-26 范围时，`--ink-measure` 会除以 0（PHP 8 下抛 DivisionByZeroError）。
- `header.php` 中 sticky_header 的 class 拼接改为先拼字符串再 `esc_attr()`。
- `--ink-muted` 亮色由 `#8d867c` 调整为 `#79736b`、暗色由 `#7d776f` 调整为 `#8d867c`，两种模式对比度均达到 WCAG AA。
- 资源版本号改用 `filemtime()`。


## [1.0.0] - 2026-10-02

### 新增

- 完整模板层级：`index.php` / `single.php` / `page.php` / `archive.php` / `search.php` / `404.php` / `comments.php` / `sidebar.php` / `front-page.php` / `searchform.php` / `header.php` / `footer.php`。
- `theme.json`：3 个字体栈（衬线正文 / 无衬线界面 / 等宽代码）、4 档字号、7 组配色、开启首字下沉。
- Customizer「主题设置」面板：排版、夜间模式、阅读增强、字数统计与摘要、布局五个分区，共 18 个设置项。
- 排版参数实时输出为 CSS 变量：正文字号（14-26px）、行高（1.2-2.6）、栏宽（480-1100px）、宽元素宽度（900-1800px），文字测量宽度按栏宽自动推算。
- 自研轻量词法分析器（`inc/code-highlight.php`），支持 PHP / JavaScript / TypeScript / Python / CSS / HTML / Bash / SQL / JSON / YAML / Go / Rust 共 12 种语言，输出标准 `token-*` class，样式由纯 CSS 承担，零第三方库。
- 代码块增强：行号、悬停行高亮、Clipboard API 复制按钮（`execCommand` 降级）。
- 自动目录：扫描 h2 - h4 生成，支持内嵌顶部 / 侧栏悬浮 / 关闭三种位置，滚动高亮当前章节，标题悬停显示锚点，标题少于 2 个时自动隐藏。
- 夜间模式：CSS 变量 + `localStorage` 记忆，`<head>` 内联无闪烁引导脚本（优先级 1，早于内容渲染），默认方案可选「跟随系统」或「始终浅色」。
- Dark Mode 图片适配：夜间模式轻微降亮，支持 `no-darken` class 逐图跳过。
- 字数统计（CJK 按字 + 拉丁按词）与预计阅读时长，阅读速度可配置。
- 搜索结果页关键词高亮，区间合并算法不破坏已渲染的代码 token。
- 阅读进度条、面包屑、相关文章（同标签优先回退同分类）、作者简介框、上一篇 / 下一篇。
- 快捷键：`/` 聚焦搜索框，`Esc` 关闭搜索抽屉。
- 经典 `[caption]` 短代码输出改写为 `figure` + `figcaption` 语义结构。
- `add_theme_support( 'editor-font-sizes' )` 注册 4 档字号。
- Widget 区域：侧栏 2 个 + 页脚 2 个，支持 `customize-selective-refresh-widgets`。
- 打印友好样式、`prefers-reduced-motion` 降级、`:focus-visible` 焦点样式。
- 全站转义输出，零外部资源依赖，PHP 7.4 兼容。
