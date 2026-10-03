<?php
/**
 * Inkstone Blog 站点头部模板。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	inkstone_body_open_fallback();
}
?>

<a class="ink-skip-link" href="#ink-content"><?php esc_html_e( '跳到正文', 'inkstone-blog' ); ?></a>

<div id="page" class="ink-site">

	<header id="masthead" class="<?php echo esc_attr( 'ink-site-header' . ( inkstone_get_option( 'sticky_header' ) ? ' ink-site-header--sticky' : '' ) ); ?>">
		<div class="ink-container">
			<div class="ink-site-header__inner">

				<div class="ink-site-branding">
					<?php inkstone_site_branding(); ?>
				</div>

				<div class="ink-header-tools">
					<?php
					inkstone_theme_toggle();
					inkstone_search_toggle();
					?>
				</div>

			</div>

			<?php inkstone_primary_nav(); ?>
		</div>
	</header>

	<main id="ink-content" class="ink-main" tabindex="-1">
