<?php
/**
 * Inkstone Blog 代码高亮模块。
 *
 * 不引入任何第三方库：自带轻量词法分析器，把代码转成
 * `<span class="ink-token-xxx">` 结构，样式完全由 CSS 承担。
 * 作者也可以在文章中直接手写 `<span class="token keyword">`，样式同样生效。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'inkstone_supported_languages' ) ) {
	/**
	 * 主题内置的语法定义表。
	 *
	 * 每个语言包含：行注释、块注释起始/结束、字符串定界符、关键字、转义符。
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function inkstone_supported_languages() {
		return array(
			'php'       => array(
				'line_comment'    => array( '//', '#' ),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( "'", '"' ),
				'escape'          => '\\',
				'keywords'        => 'abstract and array as break callable case catch class clone const continue declare default do echo else elseif empty enddeclare endfor endforeach endif endswitch endwhile enum extends final finally fn for foreach function global goto if implements include include_once instanceof insteadof interface isset list match namespace new or print private protected public readonly require require_once return static switch throw trait try unset use var while xor yield yield_from',
				'constants_prefix'=> 'true false null TRUE FALSE NULL __LINE__ __FILE__ __DIR__ __FUNCTION__ __CLASS__ __METHOD__ __NAMESPACE__',
			),
			'javascript' => array(
				'line_comment'    => array( '//' ),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( '"', "'", '`' ),
				'escape'          => '\\',
				'keywords'        => 'async await break case catch class const continue debugger default delete do else export extends finally for from function get if import in instanceof let new of return set static super switch this throw try typeof var void while with yield',
				'constants_prefix'=> 'true false null undefined NaN Infinity',
			),
			'css'        => array(
				'line_comment'    => array(),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( '"', "'" ),
				'escape'          => '\\',
				'keywords'        => 'important media supports keyframes import charset font-face layer container property',
				'constants_prefix'=> 'inherit initial unset revert auto none',
			),
			'html'       => array(
				'line_comment'    => array(),
				'block_comment'   => array( '<!--', '-->' ),
				'strings'         => array( '"', "'" ),
				'escape'          => '\\',
				'keywords'        => 'html head body meta title link script style div span a p img ul ol li table tr td th form input button label section article header footer nav main aside header h1 h2 h3 h4 h5 h6',
				'constants_prefix'=> '',
			),
			'python'     => array(
				'line_comment'    => array( '#' ),
				'block_comment'   => array( '"""', '"""' ),
				'strings'         => array( '"', "'" ),
				'escape'          => '\\',
				'keywords'        => 'and as assert async await break class continue def del elif else except finally for from global if import in is lambda match nonlocal not or pass raise return try while with yield',
				'constants_prefix'=> 'True False None self cls',
			),
			'bash'       => array(
				'line_comment'    => array( '#' ),
				'block_comment'   => array(),
				'strings'         => array( '"', "'" ),
				'escape'          => '\\',
				'keywords'        => 'if then else elif fi for while do done case esac function in return export local readonly declare set unset shift trap source alias',
				'constants_prefix'=> '',
			),
			'sql'        => array(
				'line_comment'    => array( '--' ),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( "'", '"' ),
				'escape'          => '\\',
				'keywords'        => 'SELECT FROM WHERE INSERT INTO VALUES UPDATE SET DELETE CREATE TABLE ALTER DROP INDEX PRIMARY KEY FOREIGN REFERENCES JOIN INNER LEFT RIGHT OUTER ON GROUP BY ORDER HAVING LIMIT OFFSET UNION ALL DISTINCT AS AND OR NOT IN EXISTS BETWEEN LIKE IS AND NULL AND COUNT SUM AVG MIN MAX',
				'constants_prefix'=> '',
			),
			'json'       => array(
				'line_comment'    => array(),
				'block_comment'   => array(),
				'strings'         => array( '"' ),
				'escape'          => '\\',
				'keywords'        => '',
				'constants_prefix'=> 'true false null',
			),
			'yaml'       => array(
				'line_comment'    => array( '#' ),
				'block_comment'   => array(),
				'strings'         => array( '"', "'" ),
				'escape'          => '\\',
				'keywords'        => '',
				'constants_prefix'=> 'true false null yes no on off',
			),
			'go'         => array(
				'line_comment'    => array( '//' ),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( '"', '`' ),
				'escape'          => '\\',
				'keywords'        => 'break case chan const continue default defer else fallthrough for func go goto if import interface map package range return select struct switch type var',
				'constants_prefix'=> 'true false nil iota',
			),
			'rust'       => array(
				'line_comment'    => array( '//' ),
				'block_comment'   => array( '/*', '*/' ),
				'strings'         => array( '"' ),
				'escape'          => '\\',
				'keywords'        => 'as async await break const continue crate dyn else enum extern fn for if impl in let loop match mod move mut pub ref return self Self static struct super trait type unsafe use where while',
				'constants_prefix'=> 'true false None Some Ok Err',
			),
			'plain'      => array(
				'line_comment'    => array(),
				'block_comment'   => array(),
				'strings'         => array(),
				'escape'          => '',
				'keywords'        => '',
				'constants_prefix'=> '',
			),
		);
	}
}

if ( ! function_exists( 'inkstone_normalize_language' ) ) {
	/**
	 * 把语言别名归一到受支持的语言键。
	 *
	 * @param string $language 原始语言标识。
	 * @return string
	 */
	function inkstone_normalize_language( $language ) {
		$language = strtolower( trim( (string) $language ) );

		$aliases = array(
			'js'         => 'javascript',
			'jsx'        => 'javascript',
			'ts'         => 'javascript',
			'typescript' => 'javascript',
			'tsx'        => 'javascript',
			'mjs'        => 'javascript',
			'node'       => 'javascript',
			'py'         => 'python',
			'python3'    => 'python',
			'sh'         => 'bash',
			'shell'      => 'bash',
			'zsh'        => 'bash',
			'console'    => 'bash',
			'tsql'       => 'sql',
			'mysql'      => 'sql',
			'yml'        => 'yaml',
			'golang'     => 'go',
			'rs'         => 'rust',
			'text'       => 'plain',
			'txt'        => 'plain',
			'plaintext'  => 'plain',
			'no'         => 'plain',
			'none'       => 'plain',
		);

		if ( isset( $aliases[ $language ] ) ) {
			$language = $aliases[ $language ];
		}

		$supported = inkstone_supported_languages();

		if ( ! isset( $supported[ $language ] ) ) {
			return 'plain';
		}

		return $language;
	}
}

if ( ! function_exists( 'inkstone_tokenize' ) ) {
	/**
	 * 轻量词法分析：把一段代码切成 token。
	 *
	 * 顺序为：块注释 → 行注释 → 字符串 → 数字 → 标识符 → 其余。
	 * 纯正则实现，不做递归解析，速度足够处理文章中的代码片段。
	 *
	 * @param string $code     原始代码。
	 * @param string $language 语言键。
	 * @return array<int,array{type:string,value:string}>
	 */
	function inkstone_tokenize( $code, $language ) {
		$supported = inkstone_supported_languages();
		$spec      = isset( $supported[ $language ] ) ? $supported[ $language ] : $supported['plain'];

		$code = (string) $code;

		if ( '' === $code ) {
			return array();
		}

		$keywords = inkstone_build_keyword_map( (string) $spec['keywords'] );
		$constants = inkstone_build_keyword_map( (string) $spec['constants_prefix'] );

		$line_comments = isset( $spec['line_comment'] ) ? $spec['line_comment'] : array();
		$strings        = isset( $spec['strings'] ) ? $spec['strings'] : array();
		$block_start    = isset( $spec['block_comment'][0] ) ? $spec['block_comment'][0] : '';
		$block_end      = isset( $spec['block_comment'][1] ) ? $spec['block_comment'][1] : '';
		$escape         = isset( $spec['escape'] ) ? $spec['escape'] : '';

		$tokens = array();
		$length = strlen( $code );
		$buffer = '';
		$i      = 0;

		/**
		 * 把缓冲区刷成一个普通文本 token。
		 *
		 * @param string $text  文本。
		 * @param array  $store token 收集数组（按引用）。
		 * @return void
		 */
		$flush = function ( $text, &$store ) {
			if ( '' !== $text ) {
				$store[] = array(
					'type'  => 'text',
					'value' => $text,
				);
			}
		};

		while ( $i < $length ) {
			$char = $code[ $i ];
			$rest = substr( $code, $i );

			// 1. 块注释
			if ( '' !== $block_start && 0 === strpos( $rest, $block_start ) ) {
				$flush( $buffer, $tokens );
				$buffer = '';

				$end_pos = strpos( $rest, $block_end );
				$comment = ( false === $end_pos ) ? $rest : substr( $rest, 0, $end_pos + strlen( $block_end ) );

				$tokens[] = array(
					'type'  => 'comment',
					'value' => $comment,
				);

				$i += strlen( $comment );
				continue;
			}

			// 2. 行注释
			$matched_line_comment = false;

			foreach ( $line_comments as $marker ) {
				if ( '' !== $marker && 0 === strpos( $rest, $marker ) ) {
					$flush( $buffer, $tokens );
					$buffer   = '';
					$end      = strpos( $rest, "\n" );
					$end      = ( false === $end ) ? strlen( $rest ) : $end;
					$comment  = substr( $rest, 0, $end );

					$tokens[] = array(
						'type'  => 'comment',
						'value' => $comment,
					);

					$i            += $end;
					$matched_line_comment = true;
					break;
				}
			}

			if ( $matched_line_comment ) {
				continue;
			}

			// 3. 字符串
			if ( in_array( $char, $strings, true ) ) {
				$flush( $buffer, $tokens );
				$buffer    = '';
				$delimiter = $char;
				$j         = $i + 1;
				$body      = $delimiter;

				while ( $j < $length ) {
					$current = $code[ $j ];

					if ( '' !== $escape && $current === $escape && $j + 1 < $length ) {
						$body .= $current . $code[ $j + 1 ];
						$j    += 2;
						continue;
					}

					$body .= $current;

					if ( $current === $delimiter ) {
						$j++;
						break;
					}

					$j++;
				}

				$tokens[] = array(
					'type'  => 'string',
					'value' => $body,
				);

				$i = $j;
				continue;
			}

			// 4. 数字
			if ( preg_match( '/\G[0-9]+(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?/', $rest, $num_match ) ) {
				$flush( $buffer, $tokens );
				$buffer = '';

				$tokens[] = array(
					'type'  => 'number',
					'value' => $num_match[0],
				);

				$i += strlen( $num_match[0] );
				continue;
			}

			// 5. 标识符 / 关键字 / 常量
			if ( preg_match( '/\G[A-Za-z_\x{7F}-\x{FFFF}][A-Za-z0-9_\x{7F}-\x{FFFF}]*/u', $rest, $word_match ) ) {
				$word = $word_match[0];
				$low  = function_exists( 'mb_strtolower' ) ? mb_strtolower( $word, 'UTF-8' ) : strtolower( $word );

				if ( isset( $keywords[ $low ] ) ) {
					$flush( $buffer, $tokens );
					$buffer   = '';
					$tokens[] = array(
						'type'  => 'keyword',
						'value' => $word,
					);
				} elseif ( isset( $constants[ $low ] ) ) {
					$flush( $buffer, $tokens );
					$buffer   = '';
					$tokens[] = array(
						'type'  => 'constant',
						'value' => $word,
					);
				} else {
					// 标识符后面紧跟 ( 时视为函数调用。
					$peek = substr( $rest, strlen( $word ), 1 );

					if ( '(' === $peek ) {
						$flush( $buffer, $tokens );
						$buffer   = '';
						$tokens[] = array(
							'type'  => 'function',
							'value' => $word,
						);
					} else {
						$buffer .= $word;
					}
				}

				$i += strlen( $word );
				continue;
			}

			// 6. 变量前缀（$var / @var）与其余字符
			if ( preg_match( '/\G[$@][A-Za-z_][A-Za-z0-9_]*/', $rest, $var_match ) ) {
				$flush( $buffer, $tokens );
				$buffer = '';

				$tokens[] = array(
					'type'  => 'variable',
					'value' => $var_match[0],
				);

				$i += strlen( $var_match[0] );
				continue;
			}

			// 标点与运算符单独成 token，便于 CSS 着色。
			if ( false !== strpos( '+-*/%=<>!&|^~?:;,.(){}[]', $char ) ) {
				$flush( $buffer, $tokens );
				$buffer   = '';

				$type   = in_array( $char, array( '=', '+', '-', '*', '/', '%', '<', '>', '!', '&', '|', '^', '~', '?' ), true ) ? 'operator' : 'punctuation';
				$tokens[] = array(
					'type'  => $type,
					'value' => $char,
				);

				$i++;
				continue;
			}

			$buffer .= $char;
			$i++;
		}

		$flush( $buffer, $tokens );

		return $tokens;
	}
}

if ( ! function_exists( 'inkstone_build_keyword_map' ) ) {
	/**
	 * 把空格分隔的关键字字符串转成查找表。
	 *
	 * @param string $list 关键字列表。
	 * @return array<string,bool>
	 */
	function inkstone_build_keyword_map( $list ) {
		$list = trim( (string) $list );

		if ( '' === $list ) {
			return array();
		}

		$map = array();

		foreach ( preg_split( '/\s+/', $list ) as $word ) {
			if ( '' === $word ) {
				continue;
			}

			$map[ strtolower( $word ) ] = true;
		}

		return $map;
	}
}

if ( ! function_exists( 'inkstone_render_code_block' ) ) {
	/**
	 * 渲染带高亮的代码块。
	 *
	 * @param string $code     原始代码（已反转义）。
	 * @param string $language 语言标识。
	 * @param array  $args     额外参数：line_numbers / copy_button。
	 * @return string 已转义的 HTML。
	 */
	function inkstone_render_code_block( $code, $language, $args = array() ) {
		$defaults = array(
			'line_numbers' => (bool) inkstone_get_option( 'code_line_numbers' ),
			'copy_button'  => (bool) inkstone_get_option( 'code_copy_button' ),
		);

		$args = wp_parse_args( $args, $defaults );

		$key        = inkstone_normalize_language( $language );
		$tokens     = inkstone_tokenize( $code, $key );
		$line_start = 1;
		$lines_out  = '';
		$line_html  = '';

		foreach ( $tokens as $token ) {
			$value = $token['value'];

			// token 内部可能含换行，需要按行拆分。
			$parts = explode( "\n", $value );

			foreach ( $parts as $index => $part ) {
				if ( $index > 0 ) {
					$lines_out .= $line_html;
					$line_html  = '';
					$line_start++;
				}

				if ( '' === $part ) {
					continue;
				}

				if ( 'text' === $token['type'] ) {
					$line_html .= esc_html( $part );
					continue;
				}

				$line_html .= '<span class="ink-token-' . esc_attr( $token['type'] ) . '">' . esc_html( $part ) . '</span>';
			}
		}

		$lines_out .= $line_html;

		$classes = 'ink-code-block ink-code-block--' . $key;

		if ( $args['line_numbers'] ) {
			$classes .= ' ink-code-block--numbered';
		}

		$line_list = explode( "\n", $lines_out );

		$rendered = '';

		foreach ( $line_list as $offset => $line ) {
			$rendered .= '<span class="ink-code-line" data-line="' . esc_attr( (string) ( $offset + 1 ) ) . '">' . $line . "\n" . '</span>';
		}

		$html  = '<div class="' . esc_attr( $classes ) . '" data-language="' . esc_attr( $key ) . '">';

		if ( $args['copy_button'] ) {
			$html .= '<button type="button" class="ink-copy-btn" data-ink-copy>'
				. '<span class="ink-copy-btn__text">' . esc_html__( '复制', 'inkstone-blog' ) . '</span>'
				. '<span class="ink-screen-reader-text">' . esc_html__( '复制代码', 'inkstone-blog' ) . '</span>'
				. '</button>';
		}

		$html .= '<pre><code>' . $rendered . '</code></pre></div>';

		return $html;
	}
}

if ( ! function_exists( 'inkstone_filter_content' ) ) {
	/**
	 * 接管文章内容里的 pre/code 块，替换为主题的代码块结构。
	 *
	 * 只处理 class 中不含 `language-none` 的 pre 块，
	 * 让作者可以显式关闭高亮。
	 *
	 * @param string $content 文章内容。
	 * @return string
	 */
	function inkstone_filter_content( $content ) {
		if ( ! inkstone_get_option( 'code_highlight' ) ) {
			return $content;
		}

		if ( is_admin() || ! is_singular() ) {
			return $content;
		}

		$pattern = '#<pre[^>]*>\s*<code[^>]*>(.*?)</code>\s*</pre>#is';

		$replaced = preg_replace_callback(
			$pattern,
			'inkstone_replace_pre_block',
			$content
		);

		// 作者显式禁用时保持原样。
		if ( null === $replaced ) {
			return $content;
		}

		return $replaced;
	}
}
add_filter( 'the_content', 'inkstone_filter_content', 20 );

if ( ! function_exists( 'inkstone_replace_pre_block' ) ) {
	/**
	 * the_content 过滤器的回调：替换单个 pre 块。
	 *
	 * @param array $matches 正则匹配结果。
	 * @return string
	 */
	function inkstone_replace_pre_block( $matches ) {
		$full  = $matches[0];
		$inner = isset( $matches[1] ) ? $matches[1] : '';

		if ( false !== stripos( $full, 'language-none' ) || false !== stripos( $full, 'no-highlight' ) ) {
			return $full;
		}

		// 作者已手工上过色的块不再二次处理。
		if ( false !== strpos( $inner, 'class="ink-token-' ) || false !== strpos( $inner, "class='ink-token-" ) ) {
			return $full;
		}

		// 提取语言：优先 <code class="language-xxx">，其次 <pre class="language-xxx">。
		$language = 'plain';

		if ( preg_match( '/class\s*=\s*["\']([^"\']*)["\']/i', $full, $class_match ) ) {
			if ( preg_match( '/(?:^|\s)language-([A-Za-z0-9_+-]+)/i', $class_match[1], $lang_match ) ) {
				$language = $lang_match[1];
			}
		}

		$code = html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES, 'UTF-8' );

		return inkstone_render_code_block( $code, $language );
	}
}
