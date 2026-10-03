<?php
/**
 * 评论模板。
 *
 * 若文章受密码保护，直接返回，不暴露任何内容结构。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="ink-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="ink-comments__title">
			<?php
			$ink_comment_count = (int) get_comments_number();

			printf(
				/* translators: %s：评论数量。 */
				esc_html( _n( '%s 条评论', '%s 条评论', $ink_comment_count, 'inkstone-blog' ) ),
				esc_html( number_format_i18n( $ink_comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( '上一页', 'inkstone-blog' ),
				'next_text' => esc_html__( '下一页', 'inkstone-blog' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( '评论已关闭。', 'inkstone-blog' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
