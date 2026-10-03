<?php
/**
 * 单篇文章模板（专注长文阅读排版）。
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
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'ink-entry' ); ?>>

					<header class="ink-entry__header">
						<h1 class="ink-entry__title"><?php the_title(); ?></h1>

						<?php
						if ( has_excerpt() ) {
							echo '<p class="ink-entry__lead">' . esc_html( get_the_excerpt() ) . '</p>';
						}

						inkstone_post_meta();
						?>
					</header>

					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<figure class="ink-entry__thumbnail">
							<?php the_post_thumbnail( 'inkstone-wide', array( 'decoding' => 'async' ) ); ?>
						</figure>
					<?php endif; ?>

					<?php
					// 目录：正文顶部内嵌模式。
					$ink_toc_position = (string) inkstone_get_option( 'toc_position' );

					if ( 'inline' === $ink_toc_position ) {
						inkstone_toc_html();
					}
					?>

					<div class="ink-entry__content">
						<?php
						the_content(
							sprintf(
								/* translators: %s：文章标题。 */
								esc_html__( '继续阅读「%s」', 'inkstone-blog' ),
								esc_html( get_the_title() )
							)
						);

						wp_link_pages(
							array(
								'before'      => '<nav class="ink-page-links">' . esc_html__( '页面：', 'inkstone-blog' ),
								'after'       => '</nav>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							)
						);
						?>
					</div>

					<footer class="ink-entry__footer">
						<?php
						$ink_categories = get_the_category_list( ', ' );
						$ink_tags       = get_the_tag_list( '', '' );

						if ( $ink_categories ) {
							printf(
								'<div class="ink-entry__tax"><span class="ink-entry__tax-label">%s</span>%s</div>',
								esc_html__( '分类', 'inkstone-blog' ),
								wp_kses_post( $ink_categories )
							);
						}

						if ( $ink_tags && ! is_wp_error( $ink_tags ) ) {
							printf(
								'<div class="ink-entry__tax"><span class="ink-entry__tax-label">%s</span>%s</div>',
								esc_html__( '标签', 'inkstone-blog' ),
								wp_kses_post( $ink_tags )
							);
						}

						inkstone_post_navigation();
						inkstone_author_box();
						?>
					</footer>

				</article>

				<?php
				inkstone_related_html( (int) get_the_ID() );

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>

			<?php
			endwhile;
			?>
		</div>

		<?php
		// 侧栏：悬浮目录优先，其次是 Widget 侧栏。
		if ( 'floating' === (string) inkstone_get_option( 'toc_position' ) ) {
			inkstone_toc_html();
		}

		inkstone_sidebar( 'sidebar-1' );
		?>
	</div>
</div>

<?php
get_footer();
