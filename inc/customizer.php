<?php
/**
 * Inkstone Blog 自定义器配置。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'inkstone_customize_register' ) ) {
	/**
	 * 注册 Customizer 面板与设置项。
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer 实例。
	 * @return void
	 */
	function inkstone_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.ink-site-title a',
					'render_callback' => 'inkstone_customize_blogname',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.ink-site-description',
					'render_callback' => 'inkstone_customize_blogdescription',
				)
			);
		}

		$wp_customize->add_panel(
			'inkstone_panel',
			array(
				'title'       => esc_html__( '主题设置', 'inkstone-blog' ),
				'description' => esc_html__( 'Inkstone Blog 的排版与阅读选项。', 'inkstone-blog' ),
				'priority'    => 20,
			)
		);

		/* ------------------------------------------------------------------
		 * 分区一：排版
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'inkstone_typography',
			array(
				'title'       => esc_html__( '排版', 'inkstone-blog' ),
				'description' => esc_html__( '调整正文字号、行高与栏宽，会实时输出为 CSS 变量。', 'inkstone-blog' ),
				'panel'       => 'inkstone_panel',
			)
		);

		$wp_customize->add_setting(
			'inkstone_font_size',
			array(
				'default'           => 18,
				'sanitize_callback' => 'inkstone_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_font_size',
			array(
				'label'   => esc_html__( '正文字号（像素）', 'inkstone-blog' ),
				'section' => 'inkstone_typography',
				'type'    => 'number',
				'input_attrs' => array(
					'min'  => 14,
					'max'  => 26,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_line_height',
			array(
				'default'           => 1.85,
				'sanitize_callback' => 'inkstone_sanitize_float',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_line_height',
			array(
				'label'   => esc_html__( '正文行高', 'inkstone-blog' ),
				'section' => 'inkstone_typography',
				'type'    => 'number',
				'input_attrs' => array(
					'min'  => 1.2,
					'max'  => 2.6,
					'step' => 0.05,
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_content_width',
			array(
				'default'           => 720,
				'sanitize_callback' => 'inkstone_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_content_width',
			array(
				'label'       => esc_html__( '正文栏宽（像素）', 'inkstone-blog' ),
				'description' => esc_html__( '可选范围 480 - 1100。代码块与宽图会按比例突破此宽度。', 'inkstone-blog' ),
				'section'     => 'inkstone_typography',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 480,
					'max'  => 1100,
					'step' => 10,
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_wide_width',
			array(
				'default'           => 1080,
				'sanitize_callback' => 'inkstone_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_wide_width',
			array(
				'label'   => esc_html__( '宽元素宽度（像素）', 'inkstone-blog' ),
				'section' => 'inkstone_typography',
				'type'    => 'number',
				'input_attrs' => array(
					'min'  => 900,
					'max'  => 1800,
					'step' => 10,
				),
			)
		);

		/* ------------------------------------------------------------------
		 * 分区二：夜间模式
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'inkstone_scheme',
			array(
				'title' => esc_html__( '夜间模式', 'inkstone-blog' ),
				'panel' => 'inkstone_panel',
			)
		);

		$wp_customize->add_setting(
			'inkstone_scheme_default',
			array(
				'default'           => 'auto',
				'sanitize_callback' => 'inkstone_sanitize_scheme',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_scheme_default',
			array(
				'label'       => esc_html__( '默认配色方案', 'inkstone-blog' ),
				'description' => esc_html__( '「跟随系统」会读取访客操作系统的深色偏好。选择「浅色」后不再输出无闪烁脚本。', 'inkstone-blog' ),
				'section'     => 'inkstone_scheme',
				'type'        => 'select',
				'choices'     => array(
					'auto'  => esc_html__( '跟随系统', 'inkstone-blog' ),
					'light' => esc_html__( '始终浅色', 'inkstone-blog' ),
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_scheme_toggle',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_scheme_toggle',
			array(
				'label'   => esc_html__( '显示配色切换按钮', 'inkstone-blog' ),
				'section' => 'inkstone_scheme',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区三：阅读增强
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'inkstone_reading',
			array(
				'title' => esc_html__( '阅读增强', 'inkstone-blog' ),
				'panel' => 'inkstone_panel',
			)
		);

		$wp_customize->add_setting(
			'inkstone_reading_progress',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_reading_progress',
			array(
				'label'   => esc_html__( '显示阅读进度条', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_toc_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_toc_enable',
			array(
				'label'   => esc_html__( '自动生成文章目录', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_toc_position',
			array(
				'default'           => 'inline',
				'sanitize_callback' => 'inkstone_sanitize_toc_position',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_toc_position',
			array(
				'label'   => esc_html__( '目录位置', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'select',
				'choices' => array(
					'inline'   => esc_html__( '正文顶部', 'inkstone-blog' ),
					'floating' => esc_html__( '侧栏悬浮', 'inkstone-blog' ),
					'off'      => esc_html__( '不显示', 'inkstone-blog' ),
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_toc_collapsed',
			array(
				'default'           => false,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_toc_collapsed',
			array(
				'label'   => esc_html__( '目录默认折叠', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_code_highlight',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_code_highlight',
			array(
				'label'       => esc_html__( '启用代码高亮', 'inkstone-blog' ),
				'description' => esc_html__( '纯 CSS token 着色，不依赖任何第三方库。', 'inkstone-blog' ),
				'section'     => 'inkstone_reading',
				'type'        => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_code_line_numbers',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_code_line_numbers',
			array(
				'label'   => esc_html__( '代码块显示行号', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_code_copy_button',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_code_copy_button',
			array(
				'label'   => esc_html__( '代码块显示复制按钮', 'inkstone-blog' ),
				'section' => 'inkstone_reading',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区四：统计与摘要
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'inkstone_stats',
			array(
				'title' => esc_html__( '字数统计与摘要', 'inkstone-blog' ),
				'panel' => 'inkstone_panel',
			)
		);

		$wp_customize->add_setting(
			'inkstone_show_word_count',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_show_word_count',
			array(
				'label'   => esc_html__( '显示字数统计', 'inkstone-blog' ),
				'section' => 'inkstone_stats',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_show_reading_time',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_show_reading_time',
			array(
				'label'   => esc_html__( '显示预计阅读时长', 'inkstone-blog' ),
				'section' => 'inkstone_stats',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'inkstone_reading_speed',
			array(
				'default'           => 300,
				'sanitize_callback' => 'inkstone_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_reading_speed',
			array(
				'label'       => esc_html__( '阅读速度（字 / 分钟）', 'inkstone-blog' ),
				'description' => esc_html__( '用于计算预计阅读时长。可选范围 100 - 1200。', 'inkstone-blog' ),
				'section'     => 'inkstone_stats',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 100,
					'max'  => 1200,
					'step' => 10,
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_excerpt_length',
			array(
				'default'           => 55,
				'sanitize_callback' => 'inkstone_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_excerpt_length',
			array(
				'label'   => esc_html__( '摘要长度（词）', 'inkstone-blog' ),
				'section' => 'inkstone_stats',
				'type'    => 'number',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 200,
					'step' => 5,
				),
			)
		);

		$wp_customize->add_setting(
			'inkstone_show_author_box',
			array(
				'default'           => true,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_show_author_box',
			array(
				'label'   => esc_html__( '文章底部显示作者简介', 'inkstone-blog' ),
				'section' => 'inkstone_stats',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区五：布局
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'inkstone_layout',
			array(
				'title' => esc_html__( '布局', 'inkstone-blog' ),
				'panel' => 'inkstone_panel',
			)
		);

		$wp_customize->add_setting(
			'inkstone_sticky_header',
			array(
				'default'           => false,
				'sanitize_callback' => 'inkstone_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'inkstone_sticky_header',
			array(
				'label'   => esc_html__( '吸顶头部', 'inkstone-blog' ),
				'section' => 'inkstone_layout',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'inkstone_customize_register' );

if ( ! function_exists( 'inkstone_customize_blogname' ) ) {
	/**
	 * 站点标题即时刷新。
	 *
	 * @return void
	 */
	function inkstone_customize_blogname() {
		bloginfo( 'name', 'display' );
	}
}

if ( ! function_exists( 'inkstone_customize_blogdescription' ) ) {
	/**
	 * 站点描述即时刷新。
	 *
	 * @return void
	 */
	function inkstone_customize_blogdescription() {
		bloginfo( 'description', 'display' );
	}
}

if ( ! function_exists( 'inkstone_customize_preview_js' ) ) {
	/**
	 * 加载 Customizer 预览脚本。
	 *
	 * @return void
	 */
	function inkstone_customize_preview_js() {
		wp_enqueue_script(
			'inkstone-customizer-preview',
			get_template_directory_uri() . '/assets/js/customizer-preview.js',
			array( 'customize-preview' ),
			inkstone_asset_version( 'assets/js/customizer-preview.js' ),
			true
		);
	}
}
add_action( 'customize_preview_init', 'inkstone_customize_preview_js' );

if ( ! function_exists( 'inkstone_sanitize_checkbox' ) ) {
	/**
	 * 复选框清洗。
	 *
	 * @param mixed $checked 输入值。
	 * @return bool
	 */
	function inkstone_sanitize_checkbox( $checked ) {
		return ( isset( $checked ) && true === (bool) $checked );
	}
}

if ( ! function_exists( 'inkstone_sanitize_number' ) ) {
	/**
	 * 整数清洗。
	 *
	 * @param mixed $value 输入值。
	 * @return int
	 */
	function inkstone_sanitize_number( $value ) {
		return (int) $value;
	}
}

if ( ! function_exists( 'inkstone_sanitize_float' ) ) {
	/**
	 * 浮点数清洗。
	 *
	 * @param mixed $value 输入值。
	 * @return float
	 */
	function inkstone_sanitize_float( $value ) {
		return round( (float) $value, 3 );
	}
}

if ( ! function_exists( 'inkstone_sanitize_scheme' ) ) {
	/**
	 * 配色方案白名单。
	 *
	 * @param mixed $value 输入值。
	 * @return string
	 */
	function inkstone_sanitize_scheme( $value ) {
		$allowed = array( 'auto', 'light' );
		$value   = is_string( $value ) ? $value : 'auto';

		return in_array( $value, $allowed, true ) ? $value : 'auto';
	}
}

if ( ! function_exists( 'inkstone_sanitize_toc_position' ) ) {
	/**
	 * 目录位置白名单。
	 *
	 * @param mixed $value 输入值。
	 * @return string
	 */
	function inkstone_sanitize_toc_position( $value ) {
		$allowed = array( 'inline', 'floating', 'off' );
		$value   = is_string( $value ) ? $value : 'inline';

		return in_array( $value, $allowed, true ) ? $value : 'inline';
	}
}
