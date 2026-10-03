# Inkstone Blog

文字优先的长文阅读 WordPress 主题。衬线正文、宽窄栏可调、自动目录、纯 CSS 代码高亮、字数与阅读时长统计、内置夜间模式。零外部资源，为技术文章与深度长文设计。

- 文本域：`inkstone-blog`
- 版本：1.0.0
- 需要 WordPress：6.0 及以上
- 需要 PHP：7.4 及以上
- 测试到：WordPress 6.6
- 许可证：MIT

---

## 简介

市面上的「技术博客主题」普遍有两个问题：一是排版参数写死在 CSS 里，改一个栏宽要动样式文件；二是代码高亮依赖一个几十 KB 的 JS 库，为了几行代码语法付出整包体积。

Inkstone Blog 把这两件事都做进主题本身：

- **排版即配置**。正文字号、行高、栏宽、宽元素宽度全部在 Customizer 中可调，实时输出为 CSS 变量，不需要碰样式文件。
- **高亮零依赖**。主题自带轻量词法分析器（`inc/code-highlight.php`），把代码切成 token 后交给 CSS 着色。没有 Prism、没有 highlight.js、没有任何 CDN 请求。内置 12 种语言，中文注释、模板字符串、heredoc 常见的边界情况都做了处理。
- **夜间模式无闪烁**。`<head>` 里内联一段不到 300 字节的引导脚本，在首次绘制前就把 `ink-dark` 类打到 `<html>` 上，访客不会看到亮色闪一下再变暗。偏好存在 `localStorage`，按站点区分。

## 特性

### 排版

| 能力 | 说明 |
| --- | --- |
| 正文字号 | 14 - 26px，默认 18px |
| 行高 | 1.2 - 2.6，默认 1.85 |
| 栏宽 | 480 - 1100px，默认 720px |
| 宽元素宽度 | 900 - 1800px，默认 1080px |
| 文字测量宽度 | 按栏宽自动推算（约 34 字/行），代码块与宽图可突破此限制 |
| 首字下沉 | `theme.json` 中已开启 `dropCap` |

全部通过 `wp_add_inline_style()` 注入到主样式表末尾，优先级高于静态 CSS，切换即时生效无闪烁。

### 阅读增强

- **自动目录**：扫描正文 h2 - h4 自动生成，支持内嵌顶部 / 侧栏悬浮 / 关闭三种位置，标题少于 2 个时自动隐藏。滚动时高亮当前章节，标题悬停显示 `#` 锚点。
- **代码高亮**：纯 CSS token 着色，支持 PHP / JavaScript / TypeScript / Python / CSS / HTML / Bash / SQL / JSON / YAML / Go / Rust，共 12 种语言。
- **代码块增强**：行号、悬停行高亮、Clipboard API 复制按钮（带 `execCommand` 降级，移动端与非 HTTPS 环境下仍可用）。
- **字数与阅读时长**：CJK 字符按字计、拉丁词按词计，速度可配置。
- **夜间模式**：跟随系统 / 始终浅色两种默认方案，切换按钮记住偏好。
- **Dark Mode 图片适配**：夜间模式下图片轻微降亮（`brightness(0.92)`），已做暗色处理的截图可加 `no-darken` class 跳过。
- **搜索高亮**：搜索结果页的关键词以 `<mark>` 标注，区间合并算法不会破坏已渲染的代码 token。
- **阅读进度条**、**面包屑**、**相关文章**（同标签优先，回退到同分类）、**作者简介框**、**上一篇/下一篇**。
- **快捷键**：按 `/` 直接聚焦搜索框，按 `Esc` 关闭抽屉。
- 打印友好样式、`:focus-visible` 焦点样式、`prefers-reduced-motion` 降级。

### 工程细节

- 完整模板层级，`single.php` 为长文做专注排版。
- 经典 `[caption]` 短代码输出改写为 `figure` + `figcaption` 语义结构。
- `add_theme_support( 'editor-font-sizes' )` 注册 4 档字号，编辑器与前台字号体系呼应。
- PHP 7.4 兼容，未使用 PHP 8 独有语法。

## 截图说明

WordPress 后台的主题截图要求尺寸为 **1200 × 900 像素**（PNG 格式）。

- **文件路径**：`screenshot.png`（放在主题根目录）
- **推荐内容**：以单篇文章页为主视角。左侧是文章标题、日期/作者/字数/阅读时长四段元信息，中间是衬线正文的段落与一段带行号的代码块（token 有多色着色），右侧是悬浮目录与侧栏 Widget，顶部显示夜间模式切换按钮。整体色调为纸白底 + 墨色字 + 单一朱砂色点缀。
- **建议分辨率**：1200 × 900 px，24 位色 PNG，文件体积控制在 250KB 以内。

> 本仓库为纯代码分发，未附带二进制截图文件；安装后请按上述说明自行补充 `screenshot.png`。

## 安装

### 从后台安装（推荐）

1. 把主题目录打包成 `inkstone-blog.zip`，确保压缩包内层是 `inkstone-blog/` 目录。
2. 进入 **外观 → 主题 → 添加新主题 → 上传主题**。
3. 点击 **启用**。

### 通过 FTP / SSH 上传

1. 将 `inkstone-blog` 目录上传到 `wp-content/themes/`。
2. 进入 **外观 → 主题**，点击 **启用**。

### 本地开发

```bash
git clone https://github.com/mornrain-lin/inkstone-blog.git
cd inkstone-blog
php -l functions.php   # 语法自检
```

## 主题配置项

进入 **外观 → 自定义 → 主题设置**。

### 排版

| 配置项 | 类型 | 默认值 | 范围 | 说明 |
| --- | --- | --- | --- | --- |
| 正文字号 | 数字 | `18` | 14 - 26 | 输出为 `--ink-font-size` |
| 正文行高 | 数字 | `1.85` | 1.2 - 2.6 | 输出为 `--ink-line-height` |
| 正文栏宽 | 数字 | `720` | 480 - 1100 | 输出为 `--ink-content-width`，同时推算文字测量宽度 |
| 宽元素宽度 | 数字 | `1080` | 900 - 1800 | 输出为 `--ink-wide-width`，代码块与宽图使用 |

### 夜间模式

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 默认配色方案 | 下拉 | `跟随系统` | 可选 `跟随系统` / `始终浅色`。选「始终浅色」时不输出引导脚本。 |
| 显示配色切换按钮 | 复选框 | 勾选 | 关闭后访客无法手动切换。 |

### 阅读增强

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 显示阅读进度条 | 复选框 | 勾选 | 仅单篇文章页。 |
| 自动生成文章目录 | 复选框 | 勾选 | 关闭后「目录位置」不生效。 |
| 目录位置 | 下拉 | `正文顶部` | 可选 `正文顶部` / `侧栏悬浮` / `不显示` |
| 目录默认折叠 | 复选框 | 不勾选 | 页面加载时目录处于收起状态 |
| 启用代码高亮 | 复选框 | 勾选 | 关闭后 `<pre><code>` 保持原样输出 |
| 代码块显示行号 | 复选框 | 勾选 | |
| 代码块显示复制按钮 | 复选框 | 勾选 | |

### 字数统计与摘要

| 配置项 | 类型 | 默认值 | 范围 | 说明 |
| --- | --- | --- | --- | --- |
| 显示字数统计 | 复选框 | 勾选 | | |
| 显示预计阅读时长 | 复选框 | 勾选 | | |
| 阅读速度 | 数字 | `300` | 100 - 1200 | 每分钟字数 |
| 摘要长度 | 数字 | `55` | 10 - 200 | 归档列表自动截取的词数 |
| 文章底部显示作者简介 | 复选框 | 勾选 | | |

### 布局

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 吸顶头部 | 复选框 | 不勾选 | |

### 菜单与 Widget

- **菜单位置**：主导航、页脚导航。
- **Widget 区域**：侧栏 1（主侧栏）、侧栏 2（次要）、页脚 1 / 2。

### 图片尺寸

| 名称 | 尺寸 | 用途 |
| --- | --- | --- |
| `inkstone-card` | 1080 × 608 | 归档卡片封面 |
| `inkstone-wide` | 1600 × 640 | 文章页顶部大图 |

## 模板层级

```
inkstone-blog/
├── style.css                    # 主题头注释 + 全部样式（含夜间模式 token 配色）
├── functions.php                # 装配入口
├── theme.json                   # 块编辑器配置（3 个字体栈、4 档字号）
├── index.php                    # 通用回退模板
├── front-page.php               # 首页：静态首页 / 置顶文章 / 文章流
├── single.php                   # 单篇文章（专注长文排版）
├── page.php                     # 单页面
├── archive.php                  # 归档
├── search.php                   # 搜索结果
├── 404.php                      # 未找到
├── comments.php                 # 评论列表 + 评论表单
├── sidebar.php                  # 独立侧栏模板
├── searchform.php               # 搜索表单
├── header.php                   # <head> + 无闪烁引导脚本
├── footer.php                   # 站点页脚 + wp_footer
├── inc/
│   ├── reading.php              # 选项读取、动态 CSS、字数/时长统计、相关文章
│   ├── template-tags.php        # 所有输出型模板标签
│   ├── code-highlight.php       # 词法分析器 + 代码块渲染 + the_content 过滤
│   └── customizer.php           # Customizer 面板与设置项
├── assets/
│   ├── css/editor-style.css     # 区块编辑器内样式
│   └── js/
│       ├── night-mode.js        # 配色切换 + 阅读进度条
│       ├── toc.js               # 目录生成 + 滚动高亮
│       ├── code-copy.js         # 代码复制（含降级）
│       ├── search-highlight.js  # 搜索结果关键词高亮
│       ├── navigation.js        # 移动端导航、搜索抽屉、快捷键
│       └── customizer-preview.js# Customizer 实时预览
```

> 翻译文件（`.pot` / `.mo` / `.l10n`）放在 `languages/` 目录，该目录在首次翻译时创建。主题已调用 `load_theme_textdomain()`，放入语言包后即可生效。

### 可覆盖的模板

在子主题中创建同名文件即可覆盖，例如 `single.php`、`archive.php`、`404.php`。

## 子主题制作

1. 在 `wp-content/themes/` 下创建目录，例如 `my-inkstone-child`。
2. 目录内只需两个文件：

```php
<?php
/**
 * My Inkstone 子主题样式。
 */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'my-inkstone-child',
		get_stylesheet_uri(),
		array( 'inkstone-style' ),
		'1.0.0'
	);
} );
```

```css
/*
Theme Name: My Inkstone
Description: Inkstone Blog 的子主题。
Template: inkstone-blog
Version: 1.0.0
*/
```

3. 启用子主题。

### 可用的过滤器与函数

| 名称 | 类型 | 说明 |
| --- | --- | --- |
| `inkstone_get_option( $key, $default )` | 函数 | 读取主题设置，带默认值兜底 |
| `inkstone_content_width` | filter | 覆盖 WordPress `$content_width` |
| `inkstone_count_words( $post )` | 函数 | 统计字数，CJK 按字 + 拉丁按词 |
| `inkstone_reading_time( $post )` | 函数 | 预计阅读分钟数 |
| `inkstone_tokenize( $code, $lang )` | 函数 | 词法分析，返回 token 数组 |
| `inkstone_render_code_block( $code, $lang )` | 函数 | 渲染完整代码块 HTML |
| `inkstone_scheme_storage_key()` | 函数 | 当前站点的配色偏好存储键 |

### 扩展代码高亮语言

往 `inkstone_filter_content` 之后挂自己的过滤器，或者直接复用词法分析器：

```php
add_filter( 'the_content', function ( $content ) {
	return str_replace( '[[MY_LANG]]', '<div class="my-lang-highlight">…</div>', $content );
}, 30 );
```

## FAQ

**Q：代码高亮为什么不用 Prism？**
为了零外部依赖。主题自带的分析器覆盖了 12 种常用语言，输出的是标准 `token-*` class，样式在 CSS 里。代价是不支持「上下文相关的第三种类语言」，比如 PHP 里 `use Foo;` 的 `Foo` 不会特殊着色。

**Q：想让某段代码不高亮怎么办？**
在 `<pre>` 或 `<code>` 上加 `class="language-none"` 或 `class="no-highlight"`，主题会原样输出。

**Q：夜间模式会不会闪一下？**
不会。`functions.php` 在 `wp_head` 的优先级 1 输出引导脚本，早于任何内容渲染执行。

**Q：我的老文章里有很多 `<pre><code class="language-js">`，需要改吗？**
不需要。主题会自动识别 `language-*` class 并选择对应语法。

**Q：搜索高亮会不会把代码搞乱？**
不会。脚本跳过 `PRE` / `CODE` / `MARK` 内的文本节点，只处理正文段落和列表。

**Q：怎么把某篇文章的目录关掉？**
在文章顶部插入短代码 `[no_toc]`，或者用过滤器：

```php
add_filter( 'the_content', function ( $content ) {
	if ( is_singular( 123 ) ) {
		return str_replace( 'data-ink-toc', 'data-ink-toc-ignored', $content );
	}
	return $content;
}, 5 );
```

**Q：支持 WooCommerce 吗？**
声明了 `woocommerce` 支持，启用不会报错，但**没有**做模板适配，商城页面样式不完整。

**Q：screenshot.png 必须吗？**
上架 WordPress.org 目录必须提供，尺寸 1200 × 900。本地自用可以不放。

## License

MIT License

Copyright (c) 2026 mornrain-lin

详细条款见 [LICENSE](LICENSE) 文件。

本主题为 MornRain 独立开发，不捆绑任何第三方库。代码高亮为自研实现，字体全部使用系统字体栈，无任何外部资源请求。
