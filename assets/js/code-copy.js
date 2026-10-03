/**
 * Inkstone Blog 代码复制按钮脚本。
 *
 * 优先使用 Clipboard API；不支持时降级到 execCommand。
 * 复制内容取自代码块的文本内容（行号 span 只是装饰，不影响 textContent）。
 */
( function () {
	'use strict';

	/**
	 * 从按钮所在的代码块中取出纯代码文本。
	 *
	 * @param {HTMLElement} button 复制按钮。
	 * @return {string} 代码文本。
	 */
	function readCode( button ) {
		var block = button.closest( '.ink-code-block' );
		var code = block ? block.querySelector( 'pre code' ) : null;

		return code ? code.textContent : '';
	}

	/**
	 * 降级方案：把文本放入临时 textarea 再执行 execCommand('copy')。
	 *
	 * @param {string} text 待复制文本。
	 * @return {boolean} 是否成功。
	 */
	function fallbackCopy( text ) {
		var textarea = document.createElement( 'textarea' );

		textarea.value = text;
		textarea.setAttribute( 'readonly', '' );
		textarea.style.position = 'fixed';
		textarea.style.top = '-1000px';
		textarea.style.opacity = '0';

		document.body.appendChild( textarea );
		textarea.select();

		var ok = false;

		try {
			ok = document.execCommand( 'copy' );
		} catch ( e ) {
			ok = false;
		}

		document.body.removeChild( textarea );

		return ok;
	}

	/**
	 * 执行复制。
	 *
	 * @param {string} text 代码文本。
	 * @return {Promise<boolean>} 是否成功。
	 */
	function copyText( text ) {
		if ( navigator.clipboard && window.isSecureContext ) {
			return navigator.clipboard.writeText( text ).then(
				function () {
					return true;
				},
				function () {
					return fallbackCopy( text );
				}
			);
		}

		return Promise.resolve( fallbackCopy( text ) );
	}

	function initCopy() {
		var buttons = document.querySelectorAll( '[data-ink-copy]' );

		if ( ! buttons.length ) {
			return;
		}

		var resetTimer = null;

		Array.prototype.forEach.call( buttons, function ( button ) {
			button.addEventListener( 'click', function () {
				var text = readCode( button );

				if ( ! text ) {
					return;
				}

				var label = button.querySelector( '.ink-copy-btn__text' );
				var l10n = window.inkstoneL10n || {};
				var copiedText = l10n.copiedText || '已复制';
				var copyTextLabel = l10n.copyText || '复制';

				copyText( text ).then( function ( ok ) {
					if ( ! ok ) {
						return;
					}

					button.classList.add( 'is-copied' );

					if ( label ) {
						label.textContent = copiedText;
					}

					if ( resetTimer ) {
						window.clearTimeout( resetTimer );
					}

					resetTimer = window.setTimeout( function () {
						button.classList.remove( 'is-copied' );

						if ( label ) {
							label.textContent = copyTextLabel;
						}
					}, 2000 );
				} );
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initCopy );
	} else {
		initCopy();
	}
} )();
