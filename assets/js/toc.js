/**
 * Inkstone Blog 目录（TOC）生成脚本。
 *
 * 扫描正文中的 h2 - h4 标题，为没有 id 的标题生成锚点，
 * 构建层级目录，并根据滚动位置高亮当前章节。
 * 无标题时保持容器隐藏，不影响正文阅读。
 */
( function () {
	'use strict';

	var MAX_LEVEL = 4;

	/**
	 * 把标题文本转为 URL 友好的 slug。
	 *
	 * @param {string} text 标题文本。
	 * @param {number} index 序号，用于去重。
	 * @return {string} slug。
	 */
	function slugify( text, index ) {
		var slug = text
			.trim()
			.toLowerCase()
			.replace( /[\s\u3000]+/g, '-' )
			.replace( /[^\w\u4e00-\u9fff-]/g, '' )
			.replace( /-+/g, '-' )
			.replace( /^-|-$/g, '' );

		if ( '' === slug ) {
			slug = 'section-' + ( index + 1 );
		}

		return slug;
	}

	/**
	 * 为没有 id 的标题补一个唯一 id。
	 */
	function ensureIds( headings ) {
		var used = Object.create( null );
		var seen = Object.create( null );

		headings.forEach( function ( heading, index ) {
			var id = heading.getAttribute( 'id' );

			if ( ! id ) {
				id = slugify( heading.textContent || '', index );
			}

			// 保证唯一。
			var base = id;
			var count = 1;

			while ( seen[ id ] ) {
				count += 1;
				id = base + '-' + count;
			}

			seen[ id ] = true;
			used[ id ] = true;
			heading.setAttribute( 'id', id );
		} );
	}

	/**
	 * 给标题追加悬停锚点链接。
	 */
	function appendAnchorLinks( headings ) {
		headings.forEach( function ( heading ) {
			var id = heading.getAttribute( 'id' );

			if ( ! id || heading.querySelector( '.ink-heading-anchor' ) ) {
				return;
			}

			var anchor = document.createElement( 'a' );
			anchor.className = 'ink-heading-anchor';
			anchor.href = '#' + id;
			anchor.setAttribute( 'aria-hidden', 'true' );
			anchor.tabIndex = -1;
			anchor.textContent = '#';

			heading.appendChild( anchor );
		} );
	}

	/**
	 * 渲染目录列表。
	 *
	 * 采用扁平渲染 + 缩进，而不是嵌套 <ol>：标题层级本来就是扁平的序列，
	 * 缩进能表达层级，同时避免树结构在跳级标题（如 h2 直接到 h4）时的边界问题。
	 */
	function renderTocList( headings ) {
		var list = document.createElement( 'ol' );
		var minLevel = headings.length ? Number( headings[ 0 ].tagName.charAt( 1 ) ) : 2;

		headings.forEach( function ( heading ) {
			var level = Number( heading.tagName.charAt( 1 ) );
			var item = document.createElement( 'li' );
			var link = document.createElement( 'a' );

			item.className = 'ink-toc__item';
			item.setAttribute( 'data-level', String( level ) );
			link.href = '#' + heading.getAttribute( 'id' );
			link.textContent = ( heading.textContent || '' ).replace( /#$/, '' ).trim();
			link.setAttribute( 'data-ink-toc-link', '' );

			// 缩进：每深一级增加 padding。
			item.style.paddingLeft = ( level - minLevel ) * 14 + 'px';

			item.appendChild( link );
			list.appendChild( item );
		} );

		return list;
	}

	function initToc() {
		var container = document.querySelector( '[data-ink-toc]' );
		var nav = document.querySelector( '[data-ink-toc-nav]' );
		var content = document.querySelector( '.ink-entry__content' );

		if ( ! container || ! nav || ! content ) {
			return;
		}

		var selector = [];
		for ( var level = 2; level <= MAX_LEVEL; level++ ) {
			selector.push( '.ink-entry__content h' + level );
		}

		var headings = Array.prototype.filter.call(
			content.querySelectorAll( selector.join( ',' ) ),
			function ( heading ) {
				// 排除代码块内部的伪标题。
				return ! heading.closest( '.ink-code-block, pre, .ink-toc' );
			}
		);

		if ( headings.length < 2 ) {
			// 标题太少，目录没有意义。
			return;
		}

		ensureIds( headings );
		appendAnchorLinks( headings );

		nav.appendChild( renderTocList( headings ) );
		container.hidden = false;

		// 折叠开关。
		var toggle = document.querySelector( '[data-ink-toc-toggle]' );

		if ( toggle ) {
			toggle.addEventListener( 'click', function () {
				var collapsed = container.classList.toggle( 'ink-toc--collapsed' );
				toggle.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );

				var text = toggle.querySelector( 'span' );

				if ( text ) {
					text.textContent = collapsed
						? ( window.inkstoneL10n && window.inkstoneL10n.expandText ) || '展开目录'
						: ( window.inkstoneL10n && window.inkstoneL10n.collapseText ) || '收起目录';
				}
			} );
		}

		initScrollSpy( headings );
	}

	/**
	 * 滚动高亮当前章节。
	 */
	function initScrollSpy( headings ) {
		var links = document.querySelectorAll( '[data-ink-toc-link]' );

		if ( ! links.length ) {
			return;
		}

		function setActive( active ) {
			Array.prototype.forEach.call( links, function ( link ) {
				link.classList.toggle( 'is-active', link === active );
			} );
		}

		function update() {
			var offset = 100;
			var current = null;

			headings.forEach( function ( heading ) {
				if ( heading.getBoundingClientRect().top - offset <= 0 ) {
					current = heading;
				}
			} );

			if ( ! current ) {
				current = headings[ 0 ];
			}

			Array.prototype.forEach.call( links, function ( link ) {
				if ( link.getAttribute( 'href' ) === '#' + current.getAttribute( 'id' ) ) {
					setActive( link );
				}
			} );
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

		update();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initToc );
	} else {
		initToc();
	}
} )();
