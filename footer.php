<?php
/**
 * Inkstone Blog 站点页脚模板。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

?>
	</main><!-- #ink-content -->

	<footer id="colophon" class="ink-site-footer">
		<?php inkstone_footer_widgets(); ?>

		<div class="ink-container">
			<div class="ink-footer-inner">
				<div class="ink-site-info">
					<p>
						<?php
						printf(
							/* translators: 1：年份，2：站点名称。 */
							esc_html__( '© %1$s %2$s', 'inkstone-blog' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( get_bloginfo( 'name', 'display' ) )
						);
						?>
					</p>
				</div>

				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( '页脚导航', 'inkstone-blog' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'ink-footer-menu',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
