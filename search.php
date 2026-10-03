<?php
/**
 * 搜索结果模板。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

get_header();

global $wp_query;
$ink_found = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>

<div class="ink-container">
	<div class="ink-content-area">
		<div class="ink-primary">

			<header class="ink-page-header">
				<h1 class="ink-page-header__title">
					<?php
					printf(
						/* translators: %s：搜索关键词。 */
						esc_html__( '搜索：%s', 'inkstone-blog' ),
						esc_html( get_search_query() )
					);
					?>
				</h1>

				<?php if ( have_posts() ) : ?>
					<p class="ink-page-header__description">
						<?php
						printf(
							/* translators: %s：结果数量。 */
							esc_html( _n( '共找到 %s 条结果。', '共找到 %s 条结果。', $ink_found, 'inkstone-blog' ) ),
							esc_html( number_format_i18n( $ink_found ) )
						);
						?>
					</p>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>

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
					<h2 class="ink-no-results__title"><?php esc_html_e( '没有找到相关内容', 'inkstone-blog' ); ?></h2>
					<p><?php esc_html_e( '请尝试更换关键词，或使用更简短的词组。', 'inkstone-blog' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>

		</div>

		<?php inkstone_sidebar( 'sidebar-1' ); ?>
	</div>
</div>

<?php
get_footer();
