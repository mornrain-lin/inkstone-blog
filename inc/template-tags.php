<?php
/**
 * Inkstone Blog 模板标签。
 *
 * 所有输出型辅助函数集中在此，模板中只做调用。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'inkstone_site_branding' ) ) {
	/**
	 * 输出站点标题 / 描述 / 自定义 Logo。
	 *
	 * @return void
	 */
	function inkstone_site_branding() {
		if ( has_custom_logo() ) {
			echo '<div class="ink-custom-logo">';
			the_custom_logo();
			echo '</div>';
			return;
		}

		$title_tag = ( is_front_page() && is_home() ) ? 'h1' : 'p';

		printf(
			'<%1$s class="ink-site-title"><a href="%2$s" rel="home">%3$s</a></%1$s>',
			esc_attr( $title_tag ),
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name', 'display' ) )
		);

		$description = get_bloginfo( 'description', 'display' );

		if ( $description ) {
			echo '<p class="ink-site-description">' . esc_html( $description ) . '</p>';
		}
	}
}

if ( ! function_exists( 'inkstone_theme_toggle' ) ) {
	/**
	 * 输出夜间模式切换按钮。
	 *
	 * 按钮文案通过 CSS 按当前 html class 切换，无需 JS 参与。
	 *
	 * @return void
	 */
	function inkstone_theme_toggle() {
		if ( ! inkstone_get_option( 'scheme_toggle' ) ) {
			return;
		}

		echo '<button type="button" class="ink-theme-toggle" data-ink-theme-toggle aria-live="polite">';
		echo '<span class="ink-theme-toggle__icon ink-theme-toggle__icon--moon" aria-hidden="true">';
		echo '<svg viewBox="0 0 16 16" fill="currentColor" focusable="false"><path d="M13.6 9.9A6.2 6.2 0 0 1 6.1 2.4a6.2 6.2 0 1 0 7.5 7.5Z"/></svg>';
		echo '</span>';
		echo '<span class="ink-theme-toggle__icon ink-theme-toggle__icon--sun" aria-hidden="true">';
		echo '<svg viewBox="0 0 16 16" fill="currentColor" focusable="false"><circle cx="8" cy="8" r="3.1"/><path d="M8 0.6h1.4v2.2H8zM8 13.2h1.4v2.2H8zM0.6 8h2.2v1.4H0.6zM13.2 8h2.2v1.4h-2.2z"/><path d="m2.1 3.3 1-1 1.6 1.6-1 1zM11.3 12.1l1-1 1.6 1.6-1 1zM2.7 13.9l-1 1L.1 13.3l1-1zM12.7 2.1l1 1-1.6 1.6-1-1z"/></svg>';
		echo '</span>';
		echo '<span class="ink-theme-toggle__label" data-ink-theme-label>' . esc_html__( '夜间', 'inkstone-blog' ) . '</span>';
		echo '<span class="ink-screen-reader-text">' . esc_html__( '切换深色 / 浅色模式', 'inkstone-blog' ) . '</span>';
		echo '</button>';
	}
}

if ( ! function_exists( 'inkstone_search_toggle' ) ) {
	/**
	 * 输出头部搜索抽屉开关与抽屉容器。
	 *
	 * @return void
	 */
	function inkstone_search_toggle() {
		?>
		<button type="button" class="ink-search-toggle" data-ink-search-toggle aria-expanded="false" aria-controls="ink-search-drawer">
			<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M6.8 1.6a5.2 5.2 0 0 1 4.08 8.4l3.6 3.6-1.06 1.06-3.6-3.6A5.2 5.2 0 1 1 6.8 1.6Zm0 1.46a3.74 3.74 0 1 0 0 7.48 3.74 3.74 0 0 0 0-7.48Z"/></svg>
			<span><?php esc_html_e( '搜索', 'inkstone-blog' ); ?></span>
		</button>
		<div id="ink-search-drawer" class="ink-search-drawer" data-ink-search-drawer>
			<div class="ink-search-drawer__inner">
				<?php get_search_form(); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'inkstone_primary_nav' ) ) {
	/**
	 * 输出主导航。
	 *
	 * @return void
	 */
	function inkstone_primary_nav() {
		?>
		<button type="button" class="ink-nav-toggle" data-ink-nav-toggle aria-expanded="false" aria-controls="ink-primary-nav">
			<span class="ink-nav-toggle__bars" aria-hidden="true"></span>
			<span><?php esc_html_e( '菜单', 'inkstone-blog' ); ?></span>
		</button>
		<nav id="ink-primary-nav" class="ink-primary-nav" aria-label="<?php esc_attr_e( '主导航', 'inkstone-blog' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'ink-primary-menu',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
			} else {
				echo '<ul class="ink-primary-menu">';
				printf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url( home_url( '/' ) ),
					esc_html__( '首页', 'inkstone-blog' )
				);
				wp_list_pages(
					array(
						'title_li' => '',
						'depth'    => 1,
						'number'   => 6,
					)
				);
				echo '</ul>';
			}
			?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'inkstone_post_meta' ) ) {
	/**
	 * 输出文章元信息：日期、作者、字数、阅读时长。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID。
	 * @return void
	 */
	function inkstone_post_meta( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$permalink = get_permalink( $post );

		echo '<ul class="ink-meta">';

		printf(
			'<li><a href="%1$s" rel="bookmark"><time datetime="%2$s">%3$s</time></a></li>',
			esc_url( (string) $permalink ),
			esc_attr( get_the_date( DATE_W3C, $post ) ),
			esc_html( get_the_date( '', $post ) )
		);

		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( (string) get_author_posts_url( (int) $post->post_author ) ),
			esc_html( get_the_author_meta( 'display_name', (int) $post->post_author ) )
		);

		$stats = inkstone_post_stats( $post );

		if ( '' !== $stats ) {
			echo '<li class="ink-meta__reading">' . esc_html( $stats ) . '</li>';
		}

		echo '</ul>';
	}
}

if ( ! function_exists( 'inkstone_post_thumbnail' ) ) {
	/**
	 * 输出文章缩略图。
	 *
	 * @param int|WP_Post|null $post 文章对象或 ID。
	 * @param string           $size 图片尺寸。
	 * @return void
	 */
	function inkstone_post_thumbnail( $post = null, $size = 'inkstone-card' ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post || ! has_post_thumbnail( $post ) ) {
			return;
		}

		if ( post_password_required( $post ) ) {
			return;
		}

		$permalink = get_permalink( $post );

		printf( '<figure class="ink-post-item__thumbnail"><a href="%s" tabindex="-1" aria-hidden="true">', esc_url( (string) $permalink ) );
		echo get_the_post_thumbnail(
			$post,
			$size,
			array(
				'loading'  => 'lazy',
				'decoding' => 'async',
				'alt'      => the_title_attribute( array( 'echo' => false, 'post' => $post ) ),
			)
		);
		echo '</a></figure>';
	}
}

if ( ! function_exists( 'inkstone_post_item' ) ) {
	/**
	 * 输出归档列表中的一篇文章。
	 *
	 * @return void
	 */
	function inkstone_post_item() {
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ink-post-item' ); ?>>
			<?php inkstone_post_thumbnail(); ?>

			<h2 class="ink-post-item__title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>

			<?php
			inkstone_post_meta();

			$excerpt = inkstone_get_excerpt();

			if ( '' !== $excerpt ) {
				echo '<p class="ink-post-item__excerpt">' . esc_html( $excerpt ) . '</p>';
			}
			?>
		</article>
		<?php
	}
}

if ( ! function_exists( 'inkstone_breadcrumb' ) ) {
	/**
	 * 输出面包屑。
	 *
	 * @return void
	 */
	function inkstone_breadcrumb() {
		if ( is_front_page() ) {
			return;
		}

		$items = array(
			array(
				'url'   => home_url( '/' ),
				'label' => esc_html__( '首页', 'inkstone-blog' ),
			),
		);

		if ( is_singular() ) {
			$post_id = (int) get_the_ID();

			foreach ( array_reverse( (array) get_post_ancestors( $post_id ) ) as $ancestor_id ) {
				$items[] = array(
					'url'   => (string) get_permalink( $ancestor_id ),
					'label' => (string) get_the_title( $ancestor_id ),
				);
			}

			$items[] = array(
				'url'   => '',
				'label' => (string) get_the_title( $post_id ),
			);
		} elseif ( is_search() ) {
			$items[] = array(
				'url'   => '',
				/* translators: %s：搜索关键词。 */
				'label' => sprintf( esc_html__( '搜索「%s」', 'inkstone-blog' ), (string) get_search_query() ),
			);
		} elseif ( is_404() ) {
			$items[] = array(
				'url'   => '',
				'label' => esc_html__( '页面未找到', 'inkstone-blog' ),
			);
		} elseif ( is_archive() ) {
			$items[] = array(
				'url'   => '',
				'label' => wp_strip_all_tags( get_the_archive_title() ),
			);
		}

		$last = count( $items ) - 1;

		echo '<nav class="ink-breadcrumb" aria-label="' . esc_attr__( '面包屑导航', 'inkstone-blog' ) . '"><ol>';

		foreach ( $items as $index => $item ) {
			echo '<li>';

			if ( $index === $last || '' === $item['url'] ) {
				printf( '<span aria-current="page">%s</span>', esc_html( $item['label'] ) );
			} else {
				printf( '<a href="%1$s">%2$s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
			}

			echo '</li>';
		}

		echo '</ol></nav>';
	}
}

if ( ! function_exists( 'inkstone_pagination' ) ) {
	/**
	 * 输出一致的分页标记。
	 *
	 * @return void
	 */
	function inkstone_pagination() {
		global $wp_query;

		if ( ! $wp_query instanceof WP_Query || $wp_query->max_num_pages < 2 ) {
			return;
		}

		$links = paginate_links(
			array(
				'total'     => $wp_query->max_num_pages,
				'current'   => max( 1, (int) get_query_var( 'paged' ) ),
				'type'      => 'array',
				'mid_size'  => 1,
				'prev_text' => esc_html__( '上一页', 'inkstone-blog' ),
				'next_text' => esc_html__( '下一页', 'inkstone-blog' ),
			)
		);

		if ( ! $links ) {
			return;
		}

		echo '<nav class="ink-pagination" aria-label="' . esc_attr__( '分页导航', 'inkstone-blog' ) . '"><div class="nav-links">';

		foreach ( $links as $link ) {
			echo wp_kses_post( $link );
		}

		echo '</div></nav>';
	}
}

if ( ! function_exists( 'inkstone_page_header' ) ) {
	/**
	 * 输出归档 / 搜索页标题区。
	 *
	 * @return void
	 */
	function inkstone_page_header() {
		echo '<header class="ink-page-header">';

		if ( is_home() && ! is_front_page() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );
			$title          = $page_for_posts ? (string) get_the_title( $page_for_posts ) : esc_html__( '最新文章', 'inkstone-blog' );

			echo '<h1 class="ink-page-header__title">' . esc_html( $title ) . '</h1>';
		} elseif ( is_search() ) {
			printf(
				'<h1 class="ink-page-header__title">%s</h1>',
				/* translators: %s：搜索关键词。 */
				sprintf( esc_html__( '搜索：%s', 'inkstone-blog' ), esc_html( get_search_query() ) )
			);
		} else {
			echo '<h1 class="ink-page-header__title">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</h1>';
		}

		$description = get_the_archive_description();

		if ( $description ) {
			echo '<div class="ink-page-header__description">' . wp_kses_post( wpautop( $description ) ) . '</div>';
		}

		echo '</header>';
	}
}

if ( ! function_exists( 'inkstone_sidebar' ) ) {
	/**
	 * 输出指定 Widget 区域。
	 *
	 * @param string $sidebar_id Widget 区域 ID。
	 * @return void
	 */
	function inkstone_sidebar( $sidebar_id = 'sidebar-1' ) {
		if ( ! is_active_sidebar( $sidebar_id ) ) {
			return;
		}

		echo '<aside class="ink-sidebar" aria-label="' . esc_attr__( '侧栏', 'inkstone-blog' ) . '">';
		dynamic_sidebar( $sidebar_id );
		echo '</aside>';
	}
}

if ( ! function_exists( 'inkstone_post_navigation' ) ) {
	/**
	 * 输出上一篇 / 下一篇导航。
	 *
	 * @return void
	 */
	function inkstone_post_navigation() {
		$previous = get_previous_post();
		$next     = get_next_post();

		if ( ! $previous && ! $next ) {
			return;
		}

		echo '<nav class="ink-post-nav" aria-label="' . esc_attr__( '文章导航', 'inkstone-blog' ) . '">';

		if ( $previous ) {
			echo '<a class="ink-post-nav__link" href="' . esc_url( (string) get_permalink( $previous ) ) . '" rel="prev">';
			printf( '<span class="ink-post-nav__label">%s</span>', esc_html__( '上一篇', 'inkstone-blog' ) );
			echo esc_html( get_the_title( $previous ) );
			echo '</a>';
		}

		if ( $next ) {
			echo '<a class="ink-post-nav__link" href="' . esc_url( (string) get_permalink( $next ) ) . '" rel="next">';
			printf( '<span class="ink-post-nav__label">%s</span>', esc_html__( '下一篇', 'inkstone-blog' ) );
			echo esc_html( get_the_title( $next ) );
			echo '</a>';
		}

		echo '</nav>';
	}
}

if ( ! function_exists( 'inkstone_author_box' ) ) {
	/**
	 * 输出作者简介框。
	 *
	 * @return void
	 */
	function inkstone_author_box() {
		if ( ! inkstone_get_option( 'show_author_box' ) ) {
			return;
		}

		$author_id = (int) get_the_author_meta( 'ID' );

		if ( ! $author_id ) {
			return;
		}

		$description = get_the_author_meta( 'description', $author_id );

		echo '<div class="ink-author-box">';
		echo '<div class="ink-author-box__avatar">' . get_avatar( $author_id, 56, '', '', array( 'loading' => 'lazy' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar 已自行转义。
		echo '<div class="ink-author-box__body">';
		printf( '<h3 class="ink-author-box__name">%s</h3>', esc_html( get_the_author_meta( 'display_name', $author_id ) ) );

		if ( $description ) {
			echo '<p class="ink-author-box__bio">' . esc_html( $description ) . '</p>';
		}

		printf(
			'<a class="ink-author-box__link" href="%1$s">%2$s</a>',
			esc_url( (string) get_author_posts_url( $author_id ) ),
			esc_html__( '查看全部文章', 'inkstone-blog' )
		);
		echo '</div></div>';
	}
}

if ( ! function_exists( 'inkstone_toc_html' ) ) {
	/**
	 * 输出目录容器。
	 *
	 * 目录内容由 assets/js/toc.js 在客户端扫描标题生成，
	 * 没有 JS 时容器保持隐藏，不影响阅读。
	 *
	 * @return void
	 */
	function inkstone_toc_html() {
		if ( ! inkstone_get_option( 'toc_enable' ) ) {
			return;
		}

		$position = (string) inkstone_get_option( 'toc_position' );

		if ( 'off' === $position ) {
			return;
		}

		$classes = 'ink-toc';

		if ( 'floating' === $position ) {
			$classes .= ' ink-toc--floating';
		}

		if ( inkstone_get_option( 'toc_collapsed' ) ) {
			$classes .= ' ink-toc--collapsed';
		}

		printf(
			'<nav class="%1$s" data-ink-toc hidden aria-labelledby="ink-toc-title"><h2 class="ink-toc__title" id="ink-toc-title">%2$s</h2><div class="ink-toc__nav" data-ink-toc-nav></div><button type="button" class="ink-toc__toggle" data-ink-toc-toggle aria-expanded="true"><span>%3$s</span></button></nav>',
			esc_attr( $classes ),
			esc_html__( '目录', 'inkstone-blog' ),
			esc_html__( '收起目录', 'inkstone-blog' )
		);
	}
}

if ( ! function_exists( 'inkstone_footer_widgets' ) ) {
	/**
	 * 输出页脚 Widget。
	 *
	 * @return void
	 */
	function inkstone_footer_widgets() {
		if ( ! is_active_sidebar( 'footer-1' ) && ! is_active_sidebar( 'footer-2' ) ) {
			return;
		}

		echo '<div class="ink-footer-widgets"><div class="ink-container">';

		for ( $i = 1; $i <= 2; $i++ ) {
			$id = 'footer-' . $i;

			if ( ! is_active_sidebar( $id ) ) {
				continue;
			}

			echo '<div class="ink-footer-widgets__col">';
			dynamic_sidebar( $id );
			echo '</div>';
		}

		echo '</div></div>';
	}
}
