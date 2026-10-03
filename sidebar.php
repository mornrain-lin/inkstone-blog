<?php
/**
 * 侧栏模板。
 *
 * 侧栏 1 没有 Widget 时直接返回，不产生空标记。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

inkstone_sidebar( 'sidebar-1' );
