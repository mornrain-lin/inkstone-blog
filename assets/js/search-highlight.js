/**
 * Inkstone Blog 搜索结果关键词高亮脚本。
 *
 * 关键词由 PHP 侧以 JSON 输出在 window.inkstoneSearchQuery 中。
 * 脚本只在正文可见文本上做替换，不改动 HTML 结构，
 * 避免破坏已渲染的 token span。
 */
( function () {
	'use strict';

	/**
	 * 递归遍历文本节点，跳过代码、脚本与已有 mark。
	 */
	function walk( node, terms, callback ) {
		if ( node.nodeType !== Node.TEXT_NODE ) {
			return;
		}

		var text = node.nodeValue;

		if ( ! text || text.length < 2 ) {
			return;
		}

		var matched = false;
		var lower = text.toLowerCase();

		for ( var i = 0; i < terms.length; i++ ) {
			if ( lower.indexOf( terms[ i ] ) !== -1 ) {
				matched = true;
				break;
			}
		}

		if ( matched ) {
			callback( node );
		}
	}

	/**
	 * 在一个文本节点内插入 <mark>，不破坏其他元素。
	 */
	function highlightNode( node, terms ) {
		var text = node.nodeValue;
		var lower = text.toLowerCase();

		// 收集所有命中区间。
		var ranges = [];

		terms.forEach( function ( term ) {
			var from = 0;
			var index = lower.indexOf( term, from );

			while ( index !== -1 ) {
				ranges.push( { start: index, end: index + term.length } );
				from = index + term.length;
				index = lower.indexOf( term, from );
			}
		} );

		if ( ! ranges.length ) {
			return;
		}

		// 合并重叠区间。
		ranges.sort( function ( a, b ) {
			return a.start - b.start;
		} );

		var merged = [ ranges[ 0 ] ];

		for ( var i = 1; i < ranges.length; i++ ) {
			var last = merged[ merged.length - 1 ];

			if ( ranges[ i ].start <= last.end ) {
				last.end = Math.max( last.end, ranges[ i ].end );
			} else {
				merged.push( ranges[ i ] );
			}
		}

		var fragment = document.createDocumentFragment();
		var cursor = 0;

		merged.forEach( function ( range ) {
			if ( range.start > cursor ) {
				fragment.appendChild( document.createTextNode( text.slice( cursor, range.start ) ) );
			}

			var mark = document.createElement( 'mark' );
			mark.className = 'ink-search-highlight';
			mark.textContent = text.slice( range.start, range.end );
			fragment.appendChild( mark );

			cursor = range.end;
		} );

		if ( cursor < text.length ) {
			fragment.appendChild( document.createTextNode( text.slice( cursor ) ) );
		}

		node.parentNode.replaceChild( fragment, node );
	}

	function initHighlight() {
		if ( ! window.inkstoneSearchQuery ) {
			return;
		}

		var raw = String( window.inkstoneSearchQuery ).trim();

		if ( raw.length < 2 ) {
			return;
		}

		// 拆分为 2 - 20 字的中文片段或完整英文词。
		var terms = raw
			.split( /\s+/ )
			.map( function ( item ) {
				return item.trim().toLowerCase();
			} )
			.filter( function ( item ) {
				return item.length >= 2 && item.length <= 20;
			} );

		if ( ! terms.length ) {
			return;
		}

		var containers = document.querySelectorAll(
			'.ink-post-item__title, .ink-post-item__excerpt, .ink-entry__content p, .ink-entry__content li'
		);

		if ( ! containers.length ) {
			return;
		}

		var skip = { PRE: true, CODE: true, MARK: true, SCRIPT: true, STYLE: true, TEXTAREA: true };

		Array.prototype.forEach.call( containers, function ( container ) {
			var walker = document.createTreeWalker( container, NodeFilter.SHOW_TEXT, null );

			var nodes = [];

			while ( walker.nextNode() ) {
				var node = walker.currentNode;

				if ( node.parentNode && skip[ node.parentNode.nodeName ] ) {
					continue;
				}

				nodes.push( node );
			}

			nodes.forEach( function ( node ) {
				walk( node, terms, highlightNode );
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initHighlight );
	} else {
		initHighlight();
	}
} )();
