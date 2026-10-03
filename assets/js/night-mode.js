/**
 * Inkstone Blog 夜间模式脚本。
 *
 * 页面渲染前的无闪烁逻辑在 wp_head 内联输出（见 functions.php），
 * 本文件只负责切换按钮的行为与 localStorage 记忆。
 */
( function () {
	'use strict';

	var root = document.documentElement;

	/**
	 * 读取存储键。键名与 PHP 侧 inkstone_scheme_storage_key() 一致。
	 *
	 * 键名无法在纯静态页面推导时退化为固定值，
	 * 主题始终通过 wp_localize_script 注入真实键名。
	 */
	function storageKey() {
		if ( window.inkstoneL10n && window.inkstoneL10n.schemeKey ) {
			return window.inkstoneL10n.schemeKey;
		}

		return 'inkstone-scheme';
	}

	/**
	 * 读取当前生效的方案字符串。
	 */
	function currentScheme() {
		if ( root.classList.contains( 'ink-dark' ) ) {
			return 'dark';
		}

		if ( root.classList.contains( 'ink-light-forced' ) ) {
			return 'light';
		}

		if ( window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) {
			return 'auto-dark';
		}

		return 'auto-light';
	}

	/**
	 * 应用方案到 <html> 的 class 上。
	 *
	 * @param {string} scheme dark / light / auto。
	 */
	function applyScheme( scheme ) {
		root.classList.remove( 'ink-dark', 'ink-light-forced' );

		if ( scheme === 'dark' ) {
			root.classList.add( 'ink-dark' );
		} else if ( scheme === 'light' ) {
			root.classList.add( 'ink-light-forced' );
		}

		updateLabel();
	}

	/**
	 * 更新切换按钮的可见文案。
	 */
	function updateLabel() {
		var label = document.querySelector( '[data-ink-theme-label]' );

		if ( ! label ) {
			return;
		}

		var isDark = root.classList.contains( 'ink-dark' );

		label.textContent = isDark
			? ( window.inkstoneL10n && window.inkstoneL10n.lightLabel ) || '浅色'
			: ( window.inkstoneL10n && window.inkstoneL10n.darkLabel ) || '夜间';
	}

	/**
	 * 读取系统偏好。
	 */
	function systemPrefersDark() {
		return !! ( window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches );
	}

	function initToggle() {
		var button = document.querySelector( '[data-ink-theme-toggle]' );

		updateLabel();

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var isDark = root.classList.contains( 'ink-dark' );
			var next = isDark ? 'light' : 'dark';

			applyScheme( next );

			try {
				window.localStorage.setItem( storageKey(), next );
			} catch ( e ) {
				// 隐私模式下 localStorage 可能不可写，忽略即可。
			}
		} );

		// 用户没有手动选择过时，跟随系统变化。
		if ( window.matchMedia ) {
			var query = window.matchMedia( '(prefers-color-scheme: dark)' );

			var onChange = function ( event ) {
				var stored = null;

				try {
					stored = window.localStorage.getItem( storageKey() );
				} catch ( e ) {
					stored = null;
				}

				if ( stored ) {
					return;
				}

				applyScheme( event.matches ? 'dark' : 'light' );
			};

			if ( typeof query.addEventListener === 'function' ) {
				query.addEventListener( 'change', onChange );
			} else if ( typeof query.addListener === 'function' ) {
				query.addListener( onChange );
			}
		}
	}

	// 阅读进度条。
	function initProgress() {
		var bar = document.querySelector( '.ink-progress__bar' );
		var container = document.querySelector( '.ink-progress' );

		if ( ! bar || ! container ) {
			return;
		}

		var target = document.querySelector( '.ink-entry__content' );

		if ( ! target ) {
			return;
		}

		function compute() {
			var rect = target.getBoundingClientRect();
			var viewport = window.innerHeight || document.documentElement.clientHeight;

			if ( rect.top >= viewport ) {
				return 0;
			}

			var total = rect.height - viewport;

			if ( total <= 0 ) {
				return 100;
			}

			return Math.min( 100, Math.max( 0, ( -rect.top / total ) * 100 ) );
		}

		function update() {
			var value = compute();
			bar.style.width = value + '%';
			container.setAttribute( 'aria-valuenow', String( Math.round( value ) ) );
		}

		var ticking = false;

		window.addEventListener(
			'scroll',
			function () {
				if ( ticking ) {
					return;
				}

				ticking = true;
				window.requestAnimationFrame( function () {
					update();
					ticking = false;
				} );
			},
			{ passive: true }
		);

		window.addEventListener( 'resize', update, { passive: true } );
		update();
	}

	function boot() {
		initToggle();
		initProgress();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	// 供其他脚本读取当前方案。
	window.inkstoneCurrentScheme = currentScheme;
	window.inkstoneSystemPrefersDark = systemPrefersDark;
} )();
