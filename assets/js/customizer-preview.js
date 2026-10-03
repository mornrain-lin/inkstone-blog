/**
 * Inkstone Blog Customizer 实时预览脚本。
 *
 * 只做文本节点替换，不发起任何网络请求。
 */
( function ( $ ) {
	'use strict';

	if ( ! window.wp || ! window.wp.customize ) {
		return;
	}

	var api = window.wp.customize;

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.ink-site-title a' ).text( to );
		} );
	} );

	api( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.ink-site-description' ).text( to );
		} );
	} );
} )( window.jQuery );
