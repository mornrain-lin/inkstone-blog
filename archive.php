<?php
/**
 * 归档模板：分类、标签、日期、作者、自定义分类法归档。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

get_header();
?>

<div class="ink-container">
	<?php inkstone_breadcrumb(); ?>

	<div class="ink-content-area">
		<div class="ink-primary">
			<?php if ( have_posts() ) : ?>

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
					<h2 class="ink-no-results__title"><?php esc_html_e( '该归档暂无内容', 'inkstone-blog' ); ?></h2>
					<p><?php esc_html_e( '试试换个分类，或使用搜索功能查找内容。', 'inkstone-blog' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>
		</div>

		<?php inkstone_sidebar( 'sidebar-1' ); ?>
	</div>
</div>

<?php
get_footer();
