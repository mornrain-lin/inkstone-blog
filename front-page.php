<?php
/**
 * 首页模板（front-page.php）。
 *
 * 若指定了静态首页，则渲染页面内容 + 置顶文章；
 * 否则退回普通文章流。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

get_header();

$ink_static_front = is_front_page() && is_page();
?>

<div class="ink-container">
	<?php
	if ( $ink_static_front ) :
		?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ink-entry' ); ?>>
				<div class="ink-entry__content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>

		<?php
		$ink_sticky = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 6,
				'ignore_sticky_posts' => false,
				'no_found_rows'       => true,
			)
		);

		if ( $ink_sticky->have_posts() ) :
			?>
			<section class="ink-related">
				<h2 class="ink-related__title"><?php esc_html_e( '置顶文章', 'inkstone-blog' ); ?></h2>
				<ul class="ink-post-list">
					<?php
					while ( $ink_sticky->have_posts() ) {
						$ink_sticky->the_post();
						inkstone_post_item();
					}
					?>
				</ul>
			</section>
			<?php
		endif;

		wp_reset_postdata();

	elseif ( have_posts() ) :
		?>
		<?php inkstone_page_header(); ?>

		<div class="ink-post-list">
			<?php
			while ( have_posts() ) {
				the_post();
				inkstone_post_item();
			}
			?>
		</div>

		<?php inkstone_pagination(); ?>

	<?php else : ?>

		<div class="ink-no-results">
			<h2 class="ink-no-results__title"><?php esc_html_e( '还没有文章', 'inkstone-blog' ); ?></h2>
			<p><?php esc_html_e( '在后台发布第一篇文章，或先到自定义器里调整排版与夜间模式。', 'inkstone-blog' ); ?></p>
			<?php get_search_form(); ?>
		</div>

	<?php endif; ?>
</div>

<?php
get_footer();
