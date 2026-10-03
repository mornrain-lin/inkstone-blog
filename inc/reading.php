<?php
/**
 * Inkstone Blog 阅读统计模块。
 *
 * 负责：中文字数统计、预计阅读时长、摘要生成、归档辅助。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'inkstone_get_option' ) ) {
	/**
	 * 读取主题设置（带默认值兜底）。
	 *
	 * @param string $key     设置键名（不带主题前缀）。
	 * @param mixed  $default 自定义默认值。
	 * @return mixed
	 */
	function inkstone_get_option( $key, $default = null ) {
		$defaults = inkstone_option_defaults();

		if ( null !== $default ) {
			$defaults[ $key ] = $default;
		}

		if ( ! isset( $defaults[ $key ] ) ) {
			return $default;
		}

		return get_theme_mod( 'inkstone_' . $key, $defaults[ $key ] );
	}
}

if ( ! function_exists( 'inkstone_option_defaults' ) ) {
	/**
	 * 主题全部设置项默认值。
	 *
	 * @return array<string,mixed>
	 */
	function inkstone_option_defaults() {
		return array(
			'font_size'         => 18,
			'line_height'       => 1.85,
			'content_width'     => 720,
			'wide_width'        => 1080,
			'scheme_default'    => 'auto',
			'scheme_toggle'     => true,
			'reading_progress'  => true,
			'toc_enable'        => true,
			'toc_position'      => 'inline',
			'toc_collapsed'     => false,
			'code_highlight'    => true,
			'code_line_numbers' => true,
			'code_copy_button'  => true,
			'excerpt_length'    => 55,
			'reading_speed'     => 300,
			'show_reading_time' => true,
			'show_word_count'   => true,
			'sticky_header'     => false,
			'show_author_box'   => true,
		);
	}
}

if ( ! function_exists( 'inkstone_build_dynamic_css' ) ) {
	/**
	 * 把 Customizer 的排版设置转为 CSS 变量。
	 *
	 * @return string 空字符串表示无需注入。
	 */
	function inkstone_build_dynamic_css() {
		$vars = array();

		$font_size = (int) inkstone_get_option( 'font_size' );

		if ( $font_size < 14 || $font_size > 26 ) {
			$font_size = 18;
		}

		$vars['--ink-font-size'] = $font_size . 'px';

		$line_height = (float) inkstone_get_option( 'line_height' );

		if ( $line_height >= 1.2 && $line_height <= 2.6 ) {
			$vars['--ink-line-height'] = (string) $line_height;
		}

		$content_width = (int) inkstone_get_option( 'content_width' );

		if ( $content_width >= 480 && $content_width <= 1100 ) {
			$vars['--ink-content-width'] = $content_width . 'px';
			// 文字测量宽度约为栏宽的 0.86 倍，保证每行 32-38 个汉字。
			$vars['--ink-measure'] = round( $content_width * 0.86 / $font_size, 2 ) . 'em';
		}

		$wide_width = (int) inkstone_get_option( 'wide_width' );

		if ( $wide_width >= 900 && $wide_width <= 1800 ) {
			$vars['--ink-wide-width'] = $wide_width . 'px';
		}

		if ( empty( $vars ) ) {
			return '';
		}

		$declarations = '';

		foreach ( $vars as $name => $value ) {
			$declarations .= $name . ':' . $value . ';';
		}

		return ':root{' . $declarations . '}';
	}
}

if ( ! function_exists( 'inkstone_count_words' ) ) {
	/**
	 * 统计文章字数。
	 *
	 * 中英混排统计：CJK 字符按字计，连续的拉丁字母/数字按一个「词」计。
	 * 统计前会剥离 HTML 标签、短代码、块级噪声，保证结果贴近真实阅读量。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID，默认当前文章。
	 * @return int 字数，最小为 0。
	 */
	function inkstone_count_words( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return 0;
		}

		if ( '' !== trim( (string) $post->post_excerpt ) ) {
			$content = $post->post_excerpt;
		} else {
			$content = $post->post_password_required() ? '' : $post->post_content;
		}

		$content = strip_shortcodes( $content );
		$content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content );
		$content = wp_strip_all_tags( (string) $content, true );
		$content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

		// 去掉多余空白，避免连续空格被算作字符。
		$content = trim( preg_replace( '/\s+/u', ' ', $content ) );

		if ( '' === $content ) {
			return 0;
		}

		// CJK 字符（中日韩统一表意文字、假名、全角标点）。
		$cjk = preg_match_all(
			'/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{FF00}-\x{FFEF}]/u',
			$content,
			$cjk_matches
		);

		// 拉丁单词与数字。
		$latin = preg_match_all( '/[A-Za-z0-9]+(?:[\'’-][A-Za-z0-9]+)*/', $content, $latin_matches );

		return (int) $cjk + (int) $latin;
	}
}

if ( ! function_exists( 'inkstone_reading_time' ) ) {
	/**
	 * 预计阅读时长（分钟）。
	 *
	 * 中文阅读速度按 Customizer 中的「每分钟字数」计算，默认 300 字/分钟，
	 * 向上取整，最少 1 分钟。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID。
	 * @return int 分钟数。
	 */
	function inkstone_reading_time( $post = null ) {
		$words = inkstone_count_words( $post );

		if ( $words < 1 ) {
			return 0;
		}

		$speed = (int) inkstone_get_option( 'reading_speed' );

		if ( $speed < 100 || $speed > 1200 ) {
			$speed = 300;
		}

		$minutes = (int) ceil( $words / $speed );

		return max( 1, $minutes );
	}
}

if ( ! function_exists( 'inkstone_format_number' ) ) {
	/**
	 * 千分位格式化数字。
	 *
	 * @param int $number 数字。
	 * @return string
	 */
	function inkstone_format_number( $number ) {
		return number_format_i18n( (int) $number );
	}
}

if ( ! function_exists( 'inkstone_format_reading_time' ) ) {
	/**
	 * 格式化为「约 N 分钟」。
	 *
	 * @param int $minutes 分钟数。
	 * @return string 已转义的 HTML 片段。
	 */
	function inkstone_format_reading_time( $minutes ) {
		$minutes = (int) $minutes;

		if ( $minutes < 1 ) {
			return esc_html__( '少于 1 分钟', 'inkstone-blog' );
		}

		return sprintf(
			/* translators: %s：分钟数。 */
			esc_html__( '约 %s 分钟', 'inkstone-blog' ),
			esc_html( inkstone_format_number( $minutes ) )
		);
	}
}

if ( ! function_exists( 'inkstone_post_stats' ) ) {
	/**
	 * 生成「字数 / 阅读时长」的统计 HTML 片段。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID。
	 * @return string 空字符串表示两项均关闭。
	 */
	function inkstone_post_stats( $post = null ) {
		$parts = array();

		if ( inkstone_get_option( 'show_word_count' ) ) {
			$parts[] = sprintf(
				/* translators: %s：字数。 */
				esc_html__( '%s 字', 'inkstone-blog' ),
				esc_html( inkstone_format_number( inkstone_count_words( $post ) ) )
			);
		}

		if ( inkstone_get_option( 'show_reading_time' ) ) {
			$parts[] = inkstone_format_reading_time( inkstone_reading_time( $post ) );
		}

		if ( empty( $parts ) ) {
			return '';
		}

		return esc_html( implode( ' · ', $parts ) );
	}
}

if ( ! function_exists( 'inkstone_get_excerpt' ) ) {
	/**
	 * 获取文章摘要纯文本。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID。
	 * @return string
	 */
	function inkstone_get_excerpt( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$length = (int) inkstone_get_option( 'excerpt_length' );

		if ( $length < 10 ) {
			$length = 55;
		}

		if ( '' !== trim( (string) $post->post_excerpt ) ) {
			return wp_strip_all_tags( strip_shortcodes( $post->post_excerpt ) );
		}

		$content = $post->post_password_required() ? '' : $post->post_content;
		$content = strip_shortcodes( $content );
		$content = excerpt_remove_blocks( $content );

		return wp_trim_words( wp_strip_all_tags( $content ), $length, '…' );
	}
}

if ( ! function_exists( 'inkstone_scheme_storage_key' ) ) {
	/**
	 * 夜间模式偏好的 localStorage 键名（按站点区分）。
	 *
	 * @return string
	 */
	function inkstone_scheme_storage_key() {
		return 'inkstone-scheme-' . substr( md5( home_url( '/' ) ), 0, 8 );
	}
}

if ( ! function_exists( 'inkstone_get_related_posts' ) ) {
	/**
	 * 获取相关文章（同分类，优先同标签，排除当前文章）。
	 *
	 * @param int $post_id   当前文章 ID。
	 * @param int $limit     数量。
	 * @param bool $same_term 是否仅限同分类。
	 * @return WP_Post[]
	 */
	function inkstone_get_related_posts( $post_id, $limit = 3, $same_term = false ) {
		$post_id = (int) $post_id;

		if ( $post_id <= 0 ) {
			return array();
		}

		$limit = max( 1, (int) $limit );

		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( $same_term ) {
			$category_ids = wp_get_post_categories( $post_id );

			if ( empty( $category_ids ) ) {
				return array();
			}

			$args['category__in'] = $category_ids;
		} else {
			$tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );

			if ( ! empty( $tag_ids ) ) {
				$args['tag__in'] = $tag_ids;
			}

			$category_ids = wp_get_post_categories( $post_id );

			if ( ! empty( $category_ids ) ) {
				$args['category__in'] = $category_ids;
			}
		}

		$query = new WP_Query( $args );

		$posts = $query->posts;

		wp_reset_postdata();

		return is_array( $posts ) ? $posts : array();
	}
}

if ( ! function_exists( 'inkstone_related_html' ) ) {
	/**
	 * 渲染相关文章区块。
	 *
	 * @param int $post_id 当前文章 ID。
	 * @return void
	 */
	function inkstone_related_html( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		$related = inkstone_get_related_posts( $post_id, 3, false );

		if ( empty( $related ) ) {
			$related = inkstone_get_related_posts( $post_id, 3, true );
		}

		if ( empty( $related ) ) {
			return;
		}

		echo '<section class="ink-related" aria-labelledby="ink-related-title">';
		echo '<h2 class="ink-related__title" id="ink-related-title">' . esc_html__( '相关文章', 'inkstone-blog' ) . '</h2>';
		echo '<ul class="ink-related__list">';

		foreach ( $related as $related_post ) {
			$permalink = get_permalink( $related_post );

			if ( ! $permalink ) {
				continue;
			}

			echo '<li class="ink-related__item">';
			printf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				esc_html( get_the_title( $related_post ) )
			);

			$stats = inkstone_post_stats( $related_post );

			if ( '' !== $stats ) {
				echo '<div class="ink-meta ink-meta--related"><span>' . esc_html( $stats ) . '</span></div>';
			}

			echo '</li>';
		}

		echo '</ul></section>';
	}
}
