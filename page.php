<?php
/**
 * 单页面模板。
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
						?>
					</header>

					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<figure class="ink-entry__thumbnail">
							<?php the_post_thumbnail( 'inkstone-wide', array( 'decoding' => 'async' ) ); ?>
						</figure>
					<?php endif; ?>

					<?php
					if ( 'inline' === (string) inkstone_get_option( 'toc_position' ) ) {
						inkstone_toc_html();
					}
					?>

					<div class="ink-entry__content">
						<?php
						the_content();

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
						edit_post_link(
							sprintf(
								/* translators: %s：页面标题。 */
								esc_html__( '编辑「%s」', 'inkstone-blog' ),
								esc_html( get_the_title() )
							),
							'<span class="ink-edit-link">',
							'</span>'
						);

						inkstone_post_navigation();
						?>
					</footer>

				</article>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>

			<?php
			endwhile;
			?>
		</div>

		<?php
		if ( 'floating' === (string) inkstone_get_option( 'toc_position' ) ) {
			inkstone_toc_html();
		}

		inkstone_sidebar( 'sidebar-1' );
		?>
	</div>
</div>

<?php
get_footer();
