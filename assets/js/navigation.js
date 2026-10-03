/**
 * Inkstone Blog 导航交互脚本。
 *
 * 负责：移动端菜单、二级菜单折叠、搜索抽屉开合、404 页搜索聚焦。
 */
( function () {
	'use strict';

	function initNavToggle() {
		var toggle = document.querySelector( '[data-ink-nav-toggle]' );

		if ( ! toggle ) {
			return;
		}

		var nav = document.getElementById( toggle.getAttribute( 'aria-controls' ) );

		if ( ! nav ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';

			toggle.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
			nav.classList.toggle( 'is-open', ! expanded );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! nav.classList.contains( 'is-open' ) ) {
				return;
			}

			if ( nav.contains( event.target ) || toggle.contains( event.target ) ) {
				return;
			}

			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	/**
	 * 移动端二级菜单：点击父项展开/收起。
	 */
	function initSubmenuToggle() {
		var nav = document.getElementById( 'ink-primary-nav' );

		if ( ! nav ) {
			return;
		}

		var parents = nav.querySelectorAll( 'li:has(> .sub-menu)' );

		Array.prototype.forEach.call( parents, function ( item ) {
			var link = item.querySelector( ':scope > a' );

			if ( ! link ) {
				return;
			}

			link.addEventListener( 'click', function ( event ) {
				if ( window.matchMedia( '(min-width: 783px)' ).matches ) {
					return;
				}

				var expanded = item.classList.toggle( 'is-expanded' );

				event.preventDefault();
				link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
			} );
		} );
	}

	/**
	 * 头部搜索抽屉。
	 */
	function initSearchDrawer() {
		var toggle = document.querySelector( '[data-ink-search-toggle]' );

		if ( ! toggle ) {
			return;
		}

		var drawer = document.getElementById( toggle.getAttribute( 'aria-controls' ) );

		if ( ! drawer ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';

			toggle.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
			drawer.classList.toggle( 'is-open', ! expanded );

			if ( ! expanded ) {
				var field = drawer.querySelector( '.ink-search-form__field' );

				if ( field ) {
					field.focus();
				}
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key ) {
				return;
			}

			drawer.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	/**
	 * 快捷键：按 / 聚焦搜索框。
	 */
	function initSearchHotkey() {
		document.addEventListener( 'keydown', function ( event ) {
			if ( '/' !== event.key ) {
				return;
			}

			var active = document.activeElement;
			var tag = active ? active.tagName : '';

			if ( 'INPUT' === tag || 'TEXTAREA' === tag || 'SELECT' === tag || active.isContentEditable ) {
				return;
			}

			var field = document.querySelector( '.ink-search-form__field' );

			if ( ! field ) {
				return;
			}

			event.preventDefault();
			field.focus();
			field.select();
		} );
	}

	/**
	 * 404 页：点击按钮聚焦搜索框。
	 */
	function initSearchFocus() {
		var button = document.querySelector( '[data-ink-focus-search]' );

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var field = document.querySelector( '.ink-404 .ink-search-form__field' );

			if ( field ) {
				field.focus();
				return;
			}

			// 打开头部抽屉后再聚焦。
			var toggle = document.querySelector( '[data-ink-search-toggle]' );

			if ( toggle ) {
				toggle.click();
			}
		} );
	}

	function boot() {
		initNavToggle();
		initSubmenuToggle();
		initSearchDrawer();
		initSearchHotkey();
		initSearchFocus();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
