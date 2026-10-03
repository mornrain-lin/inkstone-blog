<?php
/**
 * 404 未找到页面模板。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

get_header();
?>

<div class="ink-container ink-container--narrow">
	<div class="ink-404">
		<p class="ink-404__code">404</p>
		<h1 class="ink-404__title"><?php esc_html_e( '页面走丢了', 'inkstone-blog' ); ?></h1>
		<p class="ink-404__text"><?php esc_html_e( '你访问的地址不存在，可能已被移动或删除。可以搜索关键词，或从下面的最新文章开始。', 'inkstone-blog' ); ?></p>

		<div class="ink-404__actions">
			<a class="ink-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '返回首页', 'inkstone-blog' ); ?></a>
			<button type="button" class="ink-button ink-button--ghost" data-ink-focus-search><?php esc_html_e( '站内搜索', 'inkstone-blog' ); ?></button>
		</div>

		<?php get_search_form(); ?>

		<?php
		$ink_latest = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 5,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( $ink_latest->have_posts() ) :
			?>
			<section class="ink-404__suggestions">
				<h2><?php esc_html_e( '最新文章', 'inkstone-blog' ); ?></h2>
				<ul class="ink-post-list">
					<?php
					while ( $ink_latest->have_posts() ) {
						$ink_latest->the_post();
						inkstone_post_item();
					}
					?>
				</ul>
			</section>
			<?php
		endif;

		wp_reset_postdata();
		?>
	</div>
</div>

<?php
get_footer();
