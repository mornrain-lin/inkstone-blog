<?php
/**
 * Inkstone Blog 主题函数入口。
 *
 * 装配入口：主题支持、模块加载、Widget、前端资源、过滤器。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'INKSTONE_VERSION' ) ) {
	define( 'INKSTONE_VERSION', '1.0.0' );
}

if ( ! defined( 'INKSTONE_TEXT_DOMAIN' ) ) {
	define( 'INKSTONE_TEXT_DOMAIN', 'inkstone-blog' );
}

/**
 * 读取主题资源的版本号，用于前端缓存 busting。
 *
 * 优先使用文件的最后修改时间，文件不可读时回落到主题版本号。
 *
 * @param string $relative_path 相对主题根目录的路径。
 * @return string
 */
function inkstone_asset_version( $relative_path ) {
	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file ) ) {
		$mtime = filemtime( $file );

		if ( $mtime ) {
			return (string) $mtime;
		}
	}

	return INKSTONE_VERSION;
}

require_once get_template_directory() . '/inc/reading.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/code-highlight.php';
require_once get_template_directory() . '/inc/customizer.php';


if ( ! function_exists( 'inkstone_setup' ) ) {
	/**
	 * 声明主题基础能力。
	 *
	 * @return void
	 */
	function inkstone_setup() {
		load_theme_textdomain( 'inkstone-blog', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-font-sizes' );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 60,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );

		// 区块编辑器字号档位，与前台变量呼应。
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( '小', 'inkstone-blog' ),
					'shortName' => esc_html__( 'S', 'inkstone-blog' ),
					'size'      => 15,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( '常规', 'inkstone-blog' ),
					'shortName' => esc_html__( 'M', 'inkstone-blog' ),
					'size'      => 17,
					'slug'      => 'medium',
				),
				array(
					'name'      => esc_html__( '大', 'inkstone-blog' ),
					'shortName' => esc_html__( 'L', 'inkstone-blog' ),
					'size'      => 20,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( '特大', 'inkstone-blog' ),
					'shortName' => esc_html__( 'XL', 'inkstone-blog' ),
					'size'      => 24,
					'slug'      => 'x-large',
				),
			)
		);

		add_image_size( 'inkstone-card', 1080, 608, true );
		add_image_size( 'inkstone-wide', 1600, 640, true );

		register_nav_menus(
			array(
				'primary' => esc_html__( '主导航', 'inkstone-blog' ),
				'footer'  => esc_html__( '页脚导航', 'inkstone-blog' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'inkstone_setup' );

if ( ! function_exists( 'inkstone_content_width' ) ) {
	/**
	 * 设置正文内容宽度。
	 *
	 * @global int $content_width
	 * @return void
	 */
	function inkstone_content_width() {
		$width = (int) apply_filters( 'inkstone_content_width', 720 );

		$GLOBALS['content_width'] = $width;
	}
}
add_action( 'after_setup_theme', 'inkstone_content_width', 0 );


if ( ! function_exists( 'inkstone_widgets_init' ) ) {
	/**
	 * 注册侧栏 2 个 + 页脚 2 个 Widget 区域。
	 *
	 * @return void
	 */
	function inkstone_widgets_init() {
		$defaults = array(
			'before_widget' => '<section id="%1$s" class="ink-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="ink-widget__title">',
			'after_title'   => '</h2>',
		);

		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( '侧栏 1（主侧栏）', 'inkstone-blog' ),
					'id'          => 'sidebar-1',
					'description' => esc_html__( '显示在文章、归档与搜索页右侧。', 'inkstone-blog' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( '侧栏 2（次要）', 'inkstone-blog' ),
					'id'          => 'sidebar-2',
					'description' => esc_html__( '备用侧栏，可通过 inkstone_sidebar( \'sidebar-2\' ) 调用。', 'inkstone-blog' ),
				)
			)
		);

		for ( $i = 1; $i <= 2; $i++ ) {
			register_sidebar(
				array_merge(
					$defaults,
					array(
						/* translators: %d：页脚栏序号。 */
						'name'        => sprintf( esc_html__( '页脚 %d', 'inkstone-blog' ), $i ),
						'id'          => 'footer-' . $i,
						/* translators: %d：页脚栏序号。 */
						'description' => sprintf( esc_html__( '页脚第 %d 栏。', 'inkstone-blog' ), $i ),
					)
				)
			);
		}
	}
}
add_action( 'widgets_init', 'inkstone_widgets_init' );


if ( ! function_exists( 'inkstone_scripts' ) ) {
	/**
	 * 加载前端样式与脚本。
	 *
	 * @return void
	 */
	function inkstone_scripts() {
		wp_enqueue_style(
			'inkstone-style',
			get_stylesheet_uri(),
			array(),
			inkstone_asset_version( 'style.css' )
		);

		wp_style_add_data( 'inkstone-style', 'rtl', 'replace' );

		wp_enqueue_script(
			'inkstone-night-mode',
			get_template_directory_uri() . '/assets/js/night-mode.js',
			array(),
			inkstone_asset_version( 'assets/js/night-mode.js' ),
			true
		);

		// 只在单篇文章页加载 TOC 与代码复制。
		if ( is_singular( 'post' ) ) {
			wp_enqueue_script(
				'inkstone-toc',
				get_template_directory_uri() . '/assets/js/toc.js',
				array(),
				inkstone_asset_version( 'assets/js/toc.js' ),
				true
			);

			wp_enqueue_script(
				'inkstone-code-copy',
				get_template_directory_uri() . '/assets/js/code-copy.js',
				array(),
				inkstone_asset_version( 'assets/js/code-copy.js' ),
				true
			);
		}

		// 只在搜索结果页做关键词高亮。
		if ( is_search() ) {
			wp_enqueue_script(
				'inkstone-search-highlight',
				get_template_directory_uri() . '/assets/js/search-highlight.js',
				array(),
				inkstone_asset_version( 'assets/js/search-highlight.js' ),
				true
			);

			wp_add_inline_script(
				'inkstone-search-highlight',
				'window.inkstoneSearchQuery = ' . wp_json_encode( get_search_query() ) . ';',
				'before'
			);
		}

		wp_enqueue_script(
			'inkstone-navigation',
			get_template_directory_uri() . '/assets/js/navigation.js',
			array(),
			inkstone_asset_version( 'assets/js/navigation.js' ),
			true
		);

		wp_localize_script(
			'inkstone-night-mode',
			'inkstoneL10n',
			array(
				'schemeKey'  => inkstone_scheme_storage_key(),
				'darkLabel'  => esc_html__( '夜间', 'inkstone-blog' ),
				'lightLabel' => esc_html__( '浅色', 'inkstone-blog' ),
				'copiedText' => esc_html__( '已复制', 'inkstone-blog' ),
				'copyText'   => esc_html__( '复制', 'inkstone-blog' ),
				'toggleNav'  => esc_html__( '切换导航菜单', 'inkstone-blog' ),
				'expandText' => esc_html__( '展开目录', 'inkstone-blog' ),
				'collapseText' => esc_html__( '收起目录', 'inkstone-blog' ),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'inkstone_scripts' );

if ( ! function_exists( 'inkstone_no_flash_script' ) ) {
	/**
	 * 在 <head> 中内联输出「无闪烁」夜间模式引导脚本。
	 *
	 * 必须在页面渲染前于 <html> 上打上 dark 类，否则用户会看到一瞬间的亮色闪屏。
	 * 脚本极短，不阻塞渲染。
	 *
	 * @return void
	 */
	function inkstone_no_flash_script() {
		$default = inkstone_get_option( 'scheme_default' );

		if ( 'light' === $default ) {
			// 用户强制亮色时不需要引导脚本。
			return;
		}

		$key = inkstone_scheme_storage_key();

		printf(
			'<script>(function(){try{var k=%1$s;var s=localStorage.getItem(k);var d=document.documentElement;if(s==="dark"||(s==="auto"&&window.matchMedia("(prefers-color-scheme: dark)").matches)){d.classList.add("ink-dark");}else if(s==="light"){d.classList.add("ink-light-forced");}}catch(e){}})();</script>' . "\n",
			wp_json_encode( $key )
		);
	}
}
add_action( 'wp_head', 'inkstone_no_flash_script', 1 );

if ( ! function_exists( 'inkstone_body_classes' ) ) {
	/**
	 * 为 <body> 追加状态类。
	 *
	 * @param array $classes 现有类名。
	 * @return array
	 */
	function inkstone_body_classes( $classes ) {
		$classes[] = 'ink-site';

		if ( is_active_sidebar( 'sidebar-1' ) ) {
			$classes[] = 'ink-has-sidebar';
		} else {
			$classes[] = 'ink-no-sidebar';
		}

		if ( is_singular( 'post' ) ) {
			$classes[] = 'ink-single-post';
		}

		return $classes;
	}
}
add_filter( 'body_class', 'inkstone_body_classes' );

if ( ! function_exists( 'inkstone_excerpt_length' ) ) {
	/**
	 * 摘要长度。
	 *
	 * @param int $length 默认长度。
	 * @return int
	 */
	function inkstone_excerpt_length( $length ) {
		$custom = (int) inkstone_get_option( 'excerpt_length' );

		return $custom > 0 ? $custom : (int) $length;
	}
}
add_filter( 'excerpt_length', 'inkstone_excerpt_length' );

if ( ! function_exists( 'inkstone_excerpt_more' ) ) {
	/**
	 * 摘要省略符。
	 *
	 * @param string $more 默认省略符。
	 * @return string
	 */
	function inkstone_excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}

		return ' …';
	}
}
add_filter( 'excerpt_more', 'inkstone_excerpt_more' );

if ( ! function_exists( 'inkstone_pingback_header' ) ) {
	/**
	 * 输出 pingback 链接。
	 *
	 * @return void
	 */
	function inkstone_pingback_header() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}
}
add_action( 'wp_head', 'inkstone_pingback_header' );

if ( ! function_exists( 'inkstone_dynamic_css' ) ) {
	/**
	 * 输出 Customizer 生成的排版 CSS 变量。
	 *
	 * @return void
	 */
	function inkstone_dynamic_css() {
		$css = inkstone_build_dynamic_css();

		if ( '' === $css ) {
			return;
		}

		wp_add_inline_style( 'inkstone-style', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'inkstone_dynamic_css', 20 );

if ( ! function_exists( 'inkstone_reading_progress_bar' ) ) {
	/**
	 * 在文章页输出顶部阅读进度细线。
	 *
	 * @return void
	 */
	function inkstone_reading_progress_bar() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		if ( ! inkstone_get_option( 'reading_progress' ) ) {
			return;
		}

		echo '<div class="ink-progress" role="progressbar" aria-label="' . esc_attr__( '阅读进度', 'inkstone-blog' ) . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="ink-progress__bar"></div></div>';
	}
}
add_action( 'wp_body_open', 'inkstone_reading_progress_bar', 10 );

if ( ! function_exists( 'inkstone_body_open_fallback' ) ) {
	/**
	 * wp_body_open 兜底（WordPress 5.2 以前）。
	 *
	 * @return void
	 */
	function inkstone_body_open_fallback() {
		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'inkstone_woocommerce_declared' ) ) {
	/**
	 * 仅声明 WooCommerce 支持，不做模板适配。
	 *
	 * @return void
	 */
	function inkstone_woocommerce_declared() {
		add_theme_support( 'woocommerce' );
	}
}
add_action( 'after_setup_theme', 'inkstone_woocommerce_declared' );

if ( ! function_exists( 'inkstone_figure_caption' ) ) {
	/**
	 * 让经典图片 caption 输出 figure/figcaption 语义结构。
	 *
	 * @return void
	 */
	function inkstone_figure_caption() {
		if ( ! current_theme_supports( 'html5', 'caption' ) ) {
			return;
		}

		add_filter( 'img_caption_shortcode', 'inkstone_filter_caption_shortcode', 10, 3 );
	}
}
add_action( 'wp', 'inkstone_figure_caption' );

if ( ! function_exists( 'inkstone_filter_caption_shortcode' ) ) {
	/**
	 * 把 [caption] 短代码输出改为 figure + figcaption。
	 *
	 * @param string $empty   WordPress 核心的占位输出。
	 * @param array  $attr    短代码属性。
	 * @param string $content 图片 HTML。
	 * @return string
	 */
	function inkstone_filter_caption_shortcode( $empty, $attr, $content ) {
		$atts = shortcode_atts(
			array(
				'id'      => '',
				'align'   => 'alignnone',
				'width'   => '',
				'caption' => '',
				'class'   => '',
			),
			$attr,
			'caption'
		);

		$width = (int) $atts['width'];

		if ( $width < 1 ) {
			$width = '';
		}

		$id      = $atts['id'] ? ' id="' . esc_attr( sanitize_html_class( $atts['id'] ) ) . '"' : '';
		$classes = trim( 'wp-caption ' . sanitize_html_class( $atts['align'] ) . ' ' . sanitize_html_class( $atts['class'] ) );

		return '<figure' . $id . ' class="' . esc_attr( $classes ) . '"' . ( $width ? ' style="width:' . $width . 'px"' : '' ) . '>'
			. do_shortcode( $content )
			. '<figcaption class="wp-caption-text">' . wp_kses_post( $atts['caption'] ) . '</figcaption>'
			. '</figure>';
	}
}
