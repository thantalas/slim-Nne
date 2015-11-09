<?php
namespace Nne\Libs;
/**
 * String handling methods.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Utility
 * @since         CakePHP(tm) v 1.2.0.5551
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * String handling methods.
 *
 * @package       Cake.Utility
 */
class String {

/**
 * Generate a random UUID
 *
 * @see http://www.ietf.org/rfc/rfc4122.txt
 * @return RFC 4122 UUID
 */
	public static function uuid() {
		$node = env('SERVER_ADDR');

		if (strpos($node, ':') !== false) {
			if (substr_count($node, '::')) {
				$node = str_replace(
					'::', str_repeat(':0000', 8 - substr_count($node, ':')) . ':', $node
				);
			}
			$node = explode(':', $node);
			$ipSix = '';

			foreach ($node as $id) {
				$ipSix .= str_pad(base_convert($id, 16, 2), 16, 0, STR_PAD_LEFT);
			}
			$node = base_convert($ipSix, 2, 10);

			if (strlen($node) < 38) {
				$node = null;
			} else {
				$node = crc32($node);
			}
		} elseif (empty($node)) {
			$host = env('HOSTNAME');

			if (empty($host)) {
				$host = env('HOST');
			}

			if (!empty($host)) {
				$ip = gethostbyname($host);

				if ($ip === $host) {
					$node = crc32($host);
				} else {
					$node = ip2long($ip);
				}
			}
		} elseif ($node !== '127.0.0.1') {
			$node = ip2long($node);
		} else {
			$node = null;
		}

		if (empty($node)) {
			$node = crc32('mnbsaddfflgkdflgkrie342kg fdkgdfgdpfretierotigkbcbxcvb');
		}

		if (function_exists('hphp_get_thread_id')) {
			$pid = hphp_get_thread_id();
		} elseif (function_exists('zend_thread_id')) {
			$pid = zend_thread_id();
		} else {
			$pid = getmypid();
		}

		if (!$pid || $pid > 65535) {
			$pid = mt_rand(0, 0xfff) | 0x4000;
		}

		list($timeMid, $timeLow) = explode(' ', microtime());
		return sprintf(
			"%08x-%04x-%04x-%02x%02x-%04x%08x", (int)$timeLow, (int)substr($timeMid, 2) & 0xffff,
			mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3f) | 0x80, mt_rand(0, 0xff), $pid, $node
		);
	}

/**
 * Tokenizes a string using $separator, ignoring any instance of $separator that appears between
 * $leftBound and $rightBound.
 *
 * @param string $data The data to tokenize.
 * @param string $separator The token to split the data on.
 * @param string $leftBound The left boundary to ignore separators in.
 * @param string $rightBound The right boundary to ignore separators in.
 * @return mixed Array of tokens in $data or original input if empty.
 */
	public static function tokenize($data, $separator = ',', $leftBound = '(', $rightBound = ')') {
		if (empty($data)) {
			return array();
		}

		$depth = 0;
		$offset = 0;
		$buffer = '';
		$results = array();
		$length = strlen($data);
		$open = false;

		while ($offset <= $length) {
			$tmpOffset = -1;
			$offsets = array(
				strpos($data, $separator, $offset),
				strpos($data, $leftBound, $offset),
				strpos($data, $rightBound, $offset)
			);
			for ($i = 0; $i < 3; $i++) {
				if ($offsets[$i] !== false && ($offsets[$i] < $tmpOffset || $tmpOffset == -1)) {
					$tmpOffset = $offsets[$i];
				}
			}
			if ($tmpOffset !== -1) {
				$buffer .= substr($data, $offset, ($tmpOffset - $offset));
				if (!$depth && $data{$tmpOffset} === $separator) {
					$results[] = $buffer;
					$buffer = '';
				} else {
					$buffer .= $data{$tmpOffset};
				}
				if ($leftBound !== $rightBound) {
					if ($data{$tmpOffset} === $leftBound) {
						$depth++;
					}
					if ($data{$tmpOffset} === $rightBound) {
						$depth--;
					}
				} else {
					if ($data{$tmpOffset} === $leftBound) {
						if (!$open) {
							$depth++;
							$open = true;
						} else {
							$depth--;
						}
					}
				}
				$offset = ++$tmpOffset;
			} else {
				$results[] = $buffer . substr($data, $offset);
				$offset = $length + 1;
			}
		}
		if (empty($results) && !empty($buffer)) {
			$results[] = $buffer;
		}

		if (!empty($results)) {
			return array_map('trim', $results);
		}

		return array();
	}

/**
 * Replaces variable placeholders inside a $str with any given $data. Each key in the $data array
 * corresponds to a variable placeholder name in $str.
 * Example: `String::insert(':name is :age years old.', array('name' => 'Bob', '65'));`
 * Returns: Bob is 65 years old.
 *
 * Available $options are:
 *
 * - before: The character or string in front of the name of the variable placeholder (Defaults to `:`)
 * - after: The character or string after the name of the variable placeholder (Defaults to null)
 * - escape: The character or string used to escape the before character / string (Defaults to `\`)
 * - format: A regex to use for matching variable placeholders. Default is: `/(?<!\\)\:%s/`
 *   (Overwrites before, after, breaks escape / clean)
 * - clean: A boolean or array with instructions for String::cleanInsert
 *
 * @param string $str A string containing variable placeholders
 * @param array $data A key => val array where each key stands for a placeholder variable name
 *     to be replaced with val
 * @param array $options An array of options, see description above
 * @return string
 */
	public static function insert($str, $data, $options = array()) {
		$defaults = array(
			'before' => ':', 'after' => null, 'escape' => '\\', 'format' => null, 'clean' => false
		);
		$options += $defaults;
		$format = $options['format'];
		$data = (array)$data;
		if (empty($data)) {
			return ($options['clean']) ? String::cleanInsert($str, $options) : $str;
		}

		if (!isset($format)) {
			$format = sprintf(
				'/(?<!%s)%s%%s%s/',
				preg_quote($options['escape'], '/'),
				str_replace('%', '%%', preg_quote($options['before'], '/')),
				str_replace('%', '%%', preg_quote($options['after'], '/'))
			);
		}

		if (strpos($str, '?') !== false && is_numeric(key($data))) {
			$offset = 0;
			while (($pos = strpos($str, '?', $offset)) !== false) {
				$val = array_shift($data);
				$offset = $pos + strlen($val);
				$str = substr_replace($str, $val, $pos, 1);
			}
			return ($options['clean']) ? String::cleanInsert($str, $options) : $str;
		}

		asort($data);

		$dataKeys = array_keys($data);
		$hashKeys = array_map('crc32', $dataKeys);
		$tempData = array_combine($dataKeys, $hashKeys);
		krsort($tempData);

		foreach ($tempData as $key => $hashVal) {
			$key = sprintf($format, preg_quote($key, '/'));
			$str = preg_replace($key, $hashVal, $str);
		}
		$dataReplacements = array_combine($hashKeys, array_values($data));
		foreach ($dataReplacements as $tmpHash => $tmpValue) {
			$tmpValue = (is_array($tmpValue)) ? '' : $tmpValue;
			$str = str_replace($tmpHash, $tmpValue, $str);
		}

		if (!isset($options['format']) && isset($options['before'])) {
			$str = str_replace($options['escape'] . $options['before'], $options['before'], $str);
		}
		return ($options['clean']) ? String::cleanInsert($str, $options) : $str;
	}

/**
 * Cleans up a String::insert() formatted string with given $options depending on the 'clean' key in
 * $options. The default method used is text but html is also available. The goal of this function
 * is to replace all whitespace and unneeded markup around placeholders that did not get replaced
 * by String::insert().
 *
 * @param string $str String to clean.
 * @param array $options Options list.
 * @return string
 * @see String::insert()
 */
	public static function cleanInsert($str, $options) {
		$clean = $options['clean'];
		if (!$clean) {
			return $str;
		}
		if ($clean === true) {
			$clean = array('method' => 'text');
		}
		if (!is_array($clean)) {
			$clean = array('method' => $options['clean']);
		}
		switch ($clean['method']) {
			case 'html':
				$clean = array_merge(array(
					'word' => '[\w,.]+',
					'andText' => true,
					'replacement' => '',
				), $clean);
				$kleenex = sprintf(
					'/[\s]*[a-z]+=(")(%s%s%s[\s]*)+\\1/i',
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/')
				);
				$str = preg_replace($kleenex, $clean['replacement'], $str);
				if ($clean['andText']) {
					$options['clean'] = array('method' => 'text');
					$str = String::cleanInsert($str, $options);
				}
				break;
			case 'text':
				$clean = array_merge(array(
					'word' => '[\w,.]+',
					'gap' => '[\s]*(?:(?:and|or)[\s]*)?',
					'replacement' => '',
				), $clean);

				$kleenex = sprintf(
					'/(%s%s%s%s|%s%s%s%s)/',
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/'),
					$clean['gap'],
					$clean['gap'],
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/')
				);
				$str = preg_replace($kleenex, $clean['replacement'], $str);
				break;
		}
		return $str;
	}

/**
 * Wraps text to a specific width, can optionally wrap at word breaks.
 *
 * ### Options
 *
 * - `width` The width to wrap to. Defaults to 72.
 * - `wordWrap` Only wrap on words breaks (spaces) Defaults to true.
 * - `indent` String to indent with. Defaults to null.
 * - `indentAt` 0 based index to start indenting at. Defaults to 0.
 *
 * @param string $text The text to format.
 * @param array|int $options Array of options to use, or an integer to wrap the text to.
 * @return string Formatted text.
 */
	public static function wrap($text, $options = array()) {
		if (is_numeric($options)) {
			$options = array('width' => $options);
		}
		$options += array('width' => 72, 'wordWrap' => true, 'indent' => null, 'indentAt' => 0);
		if ($options['wordWrap']) {
			$wrapped = self::wordWrap($text, $options['width'], "\n");
		} else {
			$wrapped = trim(chunk_split($text, $options['width'] - 1, "\n"));
		}
		if (!empty($options['indent'])) {
			$chunks = explode("\n", $wrapped);
			for ($i = $options['indentAt'], $len = count($chunks); $i < $len; $i++) {
				$chunks[$i] = $options['indent'] . $chunks[$i];
			}
			$wrapped = implode("\n", $chunks);
		}
		return $wrapped;
	}

/**
 * Unicode aware version of wordwrap.
 *
 * @param string $text The text to format.
 * @param int $width The width to wrap to. Defaults to 72.
 * @param string $break The line is broken using the optional break parameter. Defaults to '\n'.
 * @param bool $cut If the cut is set to true, the string is always wrapped at the specified width.
 * @return string Formatted text.
 */
	public static function wordWrap($text, $width = 72, $break = "\n", $cut = false) {
		if ($cut) {
			$parts = array();
			while (mb_strlen($text) > 0) {
				$part = mb_substr($text, 0, $width);
				$parts[] = trim($part);
				$text = trim(mb_substr($text, mb_strlen($part)));
			}
			return implode($break, $parts);
		}

		$parts = array();
		while (mb_strlen($text) > 0) {
			if ($width >= mb_strlen($text)) {
				$parts[] = trim($text);
				break;
			}

			$part = mb_substr($text, 0, $width);
			$nextChar = mb_substr($text, $width, 1);
			if ($nextChar !== ' ') {
				$breakAt = mb_strrpos($part, ' ');
				if ($breakAt === false) {
					$breakAt = mb_strpos($text, ' ', $width);
				}
				if ($breakAt === false) {
					$parts[] = trim($text);
					break;
				}
				$part = mb_substr($text, 0, $breakAt);
			}

			$part = trim($part);
			$parts[] = $part;
			$text = trim(mb_substr($text, mb_strlen($part)));
		}

		return implode($break, $parts);
	}

/**
 * Highlights a given phrase in a text. You can specify any expression in highlighter that
 * may include the \1 expression to include the $phrase found.
 *
 * ### Options:
 *
 * - `format` The piece of html with that the phrase will be highlighted
 * - `html` If true, will ignore any HTML tags, ensuring that only the correct text is highlighted
 * - `regex` a custom regex rule that is used to match words, default is '|$tag|iu'
 *
 * @param string $text Text to search the phrase in.
 * @param string|array $phrase The phrase or phrases that will be searched.
 * @param array $options An array of html attributes and options.
 * @return string The highlighted text
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::highlight
 */
	public static function highlight($text, $phrase, $options = array()) {
		if (empty($phrase)) {
			return $text;
		}

		$defaults = array(
			'format' => '<span class="highlight">\1</span>',
			'html' => false,
			'regex' => "|%s|iu"
		);
		$options += $defaults;
		extract($options);

		if (is_array($phrase)) {
			$replace = array();
			$with = array();

			foreach ($phrase as $key => $segment) {
				$segment = '(' . preg_quote($segment, '|') . ')';
				if ($html) {
					$segment = "(?![^<]+>)$segment(?![^<]+>)";
				}

				$with[] = (is_array($format)) ? $format[$key] : $format;
				$replace[] = sprintf($options['regex'], $segment);
			}

			return preg_replace($replace, $with, $text);
		}

		$phrase = '(' . preg_quote($phrase, '|') . ')';
		if ($html) {
			$phrase = "(?![^<]+>)$phrase(?![^<]+>)";
		}

		return preg_replace(sprintf($options['regex'], $phrase), $format, $text);
	}

/**
 * Strips given text of all links (<a href=....).
 *
 * @param string $text Text
 * @return string The text without links
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::stripLinks
 */
	public static function stripLinks($text) {
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

/**
 * Truncates text starting from the end.
 *
 * Cuts a string to the length of $length and replaces the first characters
 * with the ellipsis if the text is longer than length.
 *
 * ### Options:
 *
 * - `ellipsis` Will be used as Beginning and prepended to the trimmed string
 * - `exact` If false, $text will not be cut mid-word
 *
 * @param string $text String to truncate.
 * @param int $length Length of returned string, including ellipsis.
 * @param array $options An array of options.
 * @return string Trimmed string.
 */
	public static function tail($text, $length = 100, $options = array()) {
		$defaults = array(
			'ellipsis' => '...', 'exact' => true
		);
		$options += $defaults;
		extract($options);

		if (!function_exists('mb_strlen')) {
			class_exists('Multibyte');
		}

		if (mb_strlen($text) <= $length) {
			return $text;
		}

		$truncate = mb_substr($text, mb_strlen($text) - $length + mb_strlen($ellipsis));
		if (!$exact) {
			$spacepos = mb_strpos($truncate, ' ');
			$truncate = $spacepos === false ? '' : trim(mb_substr($truncate, $spacepos));
		}

		return $ellipsis . $truncate;
	}

/**
 * Truncates text.
 *
 * Cuts a string to the length of $length and replaces the last characters
 * with the ellipsis if the text is longer than length.
 *
 * ### Options:
 *
 * - `ellipsis` Will be used as Ending and appended to the trimmed string (`ending` is deprecated)
 * - `exact` If false, $text will not be cut mid-word
 * - `html` If true, HTML tags would be handled correctly
 *
 * @param string $text String to truncate.
 * @param int $length Length of returned string, including ellipsis.
 * @param array $options An array of html attributes and options.
 * @return string Trimmed string.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::truncate
 */
	public static function truncate($text, $length = 100, $options = array()) {
		$defaults = array(
			'ellipsis' => '...', 'exact' => true, 'html' => false
		);
		if (isset($options['ending'])) {
			$defaults['ellipsis'] = $options['ending'];
		} elseif (!empty($options['html']) && Configure::read('App.encoding') === 'UTF-8') {
			$defaults['ellipsis'] = "\xe2\x80\xa6";
		}
		$options += $defaults;
		extract($options);

		if (!function_exists('mb_strlen')) {
			class_exists('Multibyte');
		}

		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen(strip_tags($ellipsis));
			$openTags = array();
			$truncate = '';

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			}
			$truncate = mb_substr($text, 0, $length - mb_strlen($ellipsis));
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if ($html) {
				$truncateCheck = mb_substr($truncate, 0, $spacepos);
				$lastOpenTag = mb_strrpos($truncateCheck, '<');
				$lastCloseTag = mb_strrpos($truncateCheck, '>');
				if ($lastOpenTag > $lastCloseTag) {
					preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
					$lastTag = array_pop($lastTagMatches[0]);
					$spacepos = mb_strrpos($truncate, $lastTag) + mb_strlen($lastTag);
				}
				$bits = mb_substr($truncate, $spacepos);
				preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
				if (!empty($droppedTags)) {
					if (!empty($openTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					} else {
						foreach ($droppedTags as $closingTag) {
							$openTags[] = $closingTag[1];
						}
					}
				}
			}
			$truncate = mb_substr($truncate, 0, $spacepos);
		}
		$truncate .= $ellipsis;

		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}

/**
 * Extracts an excerpt from the text surrounding the phrase with a number of characters on each side
 * determined by radius.
 *
 * @param string $text String to search the phrase in
 * @param string $phrase Phrase that will be searched for
 * @param int $radius The amount of characters that will be returned on each side of the founded phrase
 * @param string $ellipsis Ending that will be appended
 * @return string Modified string
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::excerpt
 */
	public static function excerpt($text, $phrase, $radius = 100, $ellipsis = '...') {
		if (empty($text) || empty($phrase)) {
			return self::truncate($text, $radius * 2, array('ellipsis' => $ellipsis));
		}

		$append = $prepend = $ellipsis;

		$phraseLen = mb_strlen($phrase);
		$textLen = mb_strlen($text);

		$pos = mb_strpos(mb_strtolower($text), mb_strtolower($phrase));
		if ($pos === false) {
			return mb_substr($text, 0, $radius) . $ellipsis;
		}

		$startPos = $pos - $radius;
		if ($startPos <= 0) {
			$startPos = 0;
			$prepend = '';
		}

		$endPos = $pos + $phraseLen + $radius;
		if ($endPos >= $textLen) {
			$endPos = $textLen;
			$append = '';
		}

		$excerpt = mb_substr($text, $startPos, $endPos - $startPos);
		$excerpt = $prepend . $excerpt . $append;

		return $excerpt;
	}

/**
 * Creates a comma separated list where the last two items are joined with 'and', forming natural English
 *
 * @param array $list The list to be joined
 * @param string $and The word used to join the last and second last items together with. Defaults to 'and'
 * @param string $separator The separator used to join all the other items together. Defaults to ', '
 * @return string The glued together string.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::toList
 */
	public static function toList($list, $and = 'and', $separator = ', ') {
		if (count($list) > 1) {
			return implode($separator, array_slice($list, null, -1)) . ' ' . $and . ' ' . array_pop($list);
		}

		return array_pop($list);
	}
}

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Utility
 * @since         CakePHP(tm) v 2.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */



/**
 * Library of array functions for manipulating and extracting data
 * from arrays or 'sets' of data.
 *
 * `Hash` provides an improved interface, more consistent and
 * predictable set of features over `Set`. While it lacks the spotty
 * support for pseudo Xpath, its more fully featured dot notation provides
 * similar features in a more consistent implementation.
 *
 * @package       Cake.Utility
 */
class Hash {

/**
 * Get a single value specified by $path out of $data.
 * Does not support the full dot notation feature set,
 * but is faster for simple read operations.
 *
 * @param array $data Array of data to operate on.
 * @param string|array $path The path being searched for. Either a dot
 *   separated string, or an array of path segments.
 * @param mixed $default The return value when the path does not exist
 * @return mixed The value fetched from the array, or null.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::get
 */
	public static function get(array $data, $path, $default = null) {
		if (empty($data)) {
			return $default;
		}
		if (is_string($path) || is_numeric($path)) {
			$parts = explode('.', $path);
		} else {
			$parts = $path;
		}
		foreach ($parts as $key) {
			if (is_array($data) && isset($data[$key])) {
				$data =& $data[$key];
			} else {
				return $default;
			}
		}
		return $data;
	}

/**
 * Gets the values from an array matching the $path expression.
 * The path expression is a dot separated expression, that can contain a set
 * of patterns and expressions:
 *
 * - `{n}` Matches any numeric key, or integer.
 * - `{s}` Matches any string key.
 * - `Foo` Matches any key with the exact same value.
 *
 * There are a number of attribute operators:
 *
 *  - `=`, `!=` Equality.
 *  - `>`, `<`, `>=`, `<=` Value comparison.
 *  - `=/.../` Regular expression pattern match.
 *
 * Given a set of User array data, from a `$User->find('all')` call:
 *
 * - `1.User.name` Get the name of the user at index 1.
 * - `{n}.User.name` Get the name of every user in the set of users.
 * - `{n}.User[id]` Get the name of every user with an id key.
 * - `{n}.User[id>=2]` Get the name of every user with an id key greater than or equal to 2.
 * - `{n}.User[username=/^paul/]` Get User elements with username matching `^paul`.
 *
 * @param array $data The data to extract from.
 * @param string $path The path to extract.
 * @return array An array of the extracted values. Returns an empty array
 *   if there are no matches.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::extract
 */
	public static function extract(array $data, $path) {
		if (empty($path)) {
			return $data;
		}

		// Simple paths.
		if (!preg_match('/[{\[]/', $path)) {
			return (array)self::get($data, $path);
		}

		if (strpos($path, '[') === false) {
			$tokens = explode('.', $path);
		} else {
			$tokens = String::tokenize($path, '.', '[', ']');
		}

		$_key = '__set_item__';

		$context = array($_key => array($data));

		foreach ($tokens as $token) {
			$next = array();

			list($token, $conditions) = self::_splitConditions($token);

			foreach ($context[$_key] as $item) {
				foreach ((array)$item as $k => $v) {
					if (self::_matchToken($k, $token)) {
						$next[] = $v;
					}
				}
			}

			// Filter for attributes.
			if ($conditions) {
				$filter = array();
				foreach ($next as $item) {
					if (is_array($item) && self::_matches($item, $conditions)) {
						$filter[] = $item;
					}
				}
				$next = $filter;
			}
			$context = array($_key => $next);

		}
		return $context[$_key];
	}
/**
 * Split token conditions
 *
 * @param string $token the token being splitted.
 * @return array array(token, conditions) with token splitted
 */
	protected static function _splitConditions($token) {
		$conditions = false;
		$position = strpos($token, '[');
		if ($position !== false) {
			$conditions = substr($token, $position);
			$token = substr($token, 0, $position);
		}

		return array($token, $conditions);
	}

/**
 * Check a key against a token.
 *
 * @param string $key The key in the array being searched.
 * @param string $token The token being matched.
 * @return bool
 */
	protected static function _matchToken($key, $token) {
		if ($token === '{n}') {
			return is_numeric($key);
		}
		if ($token === '{s}') {
			return is_string($key);
		}
		if (is_numeric($token)) {
			return ($key == $token);
		}
		return ($key === $token);
	}

/**
 * Checks whether or not $data matches the attribute patterns
 *
 * @param array $data Array of data to match.
 * @param string $selector The patterns to match.
 * @return bool Fitness of expression.
 */
	protected static function _matches(array $data, $selector) {
		preg_match_all(
			'/(\[ (?P<attr>[^=><!]+?) (\s* (?P<op>[><!]?[=]|[><]) \s* (?P<val>(?:\/.*?\/ | [^\]]+)) )? \])/x',
			$selector,
			$conditions,
			PREG_SET_ORDER
		);

		foreach ($conditions as $cond) {
			$attr = $cond['attr'];
			$op = isset($cond['op']) ? $cond['op'] : null;
			$val = isset($cond['val']) ? $cond['val'] : null;

			// Presence test.
			if (empty($op) && empty($val) && !isset($data[$attr])) {
				return false;
			}

			// Empty attribute = fail.
			if (!(isset($data[$attr]) || array_key_exists($attr, $data))) {
				return false;
			}

			$prop = null;
			if (isset($data[$attr])) {
				$prop = $data[$attr];
			}
			$isBool = is_bool($prop);
			if ($isBool && is_numeric($val)) {
				$prop = $prop ? '1' : '0';
			} elseif ($isBool) {
				$prop = $prop ? 'true' : 'false';
			}

			// Pattern matches and other operators.
			if ($op === '=' && $val && $val[0] === '/') {
				if (!preg_match($val, $prop)) {
					return false;
				}
			} elseif (
				($op === '=' && $prop != $val) ||
				($op === '!=' && $prop == $val) ||
				($op === '>' && $prop <= $val) ||
				($op === '<' && $prop >= $val) ||
				($op === '>=' && $prop < $val) ||
				($op === '<=' && $prop > $val)
			) {
				return false;
			}

		}
		return true;
	}

/**
 * Insert $values into an array with the given $path. You can use
 * `{n}` and `{s}` elements to insert $data multiple times.
 *
 * @param array $data The data to insert into.
 * @param string $path The path to insert at.
 * @param array $values The values to insert.
 * @return array The data with $values inserted.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::insert
 */
	public static function insert(array $data, $path, $values = null) {
		if (strpos($path, '[') === false) {
			$tokens = explode('.', $path);
		} else {
			$tokens = String::tokenize($path, '.', '[', ']');
		}

		if (strpos($path, '{') === false && strpos($path, '[') === false) {
			return self::_simpleOp('insert', $data, $tokens, $values);
		}

		$token = array_shift($tokens);
		$nextPath = implode('.', $tokens);

		list($token, $conditions) = self::_splitConditions($token);

		foreach ($data as $k => $v) {
			if (self::_matchToken($k, $token)) {
				if ($conditions && self::_matches($v, $conditions)) {
					$data[$k] = array_merge($v, $values);
					continue;
				}
				if (!$conditions) {
					$data[$k] = self::insert($v, $nextPath, $values);
				}
			}
		}
		return $data;
	}

/**
 * Perform a simple insert/remove operation.
 *
 * @param string $op The operation to do.
 * @param array $data The data to operate on.
 * @param array $path The path to work on.
 * @param mixed $values The values to insert when doing inserts.
 * @return array data.
 */
	protected static function _simpleOp($op, $data, $path, $values = null) {
		$_list =& $data;

		$count = count($path);
		$last = $count - 1;
		foreach ($path as $i => $key) {
			if ((is_numeric($key) && intval($key) > 0 || $key === '0') && strpos($key, '0') !== 0) {
				$key = (int)$key;
			}
			if ($op === 'insert') {
				if ($i === $last) {
					$_list[$key] = $values;
					return $data;
				}
				if (!isset($_list[$key])) {
					$_list[$key] = array();
				}
				$_list =& $_list[$key];
				if (!is_array($_list)) {
					$_list = array();
				}
			} elseif ($op === 'remove') {
				if ($i === $last) {
					unset($_list[$key]);
					return $data;
				}
				if (!isset($_list[$key])) {
					return $data;
				}
				$_list =& $_list[$key];
			}
		}
	}

/**
 * Remove data matching $path from the $data array.
 * You can use `{n}` and `{s}` to remove multiple elements
 * from $data.
 *
 * @param array $data The data to operate on
 * @param string $path A path expression to use to remove.
 * @return array The modified array.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::remove
 */
	public static function remove(array $data, $path) {
		if (strpos($path, '[') === false) {
			$tokens = explode('.', $path);
		} else {
			$tokens = String::tokenize($path, '.', '[', ']');
		}

		if (strpos($path, '{') === false && strpos($path, '[') === false) {
			return self::_simpleOp('remove', $data, $tokens);
		}

		$token = array_shift($tokens);
		$nextPath = implode('.', $tokens);

		list($token, $conditions) = self::_splitConditions($token);

		foreach ($data as $k => $v) {
			$match = self::_matchToken($k, $token);
			if ($match && is_array($v)) {
				if ($conditions && self::_matches($v, $conditions)) {
					unset($data[$k]);
					continue;
				}
				$data[$k] = self::remove($v, $nextPath);
				if (empty($data[$k])) {
					unset($data[$k]);
				}
			} elseif ($match && empty($nextPath)) {
				unset($data[$k]);
			}
		}
		return $data;
	}

/**
 * Creates an associative array using `$keyPath` as the path to build its keys, and optionally
 * `$valuePath` as path to get the values. If `$valuePath` is not specified, all values will be initialized
 * to null (useful for Hash::merge). You can optionally group the values by what is obtained when
 * following the path specified in `$groupPath`.
 *
 * @param array $data Array from where to extract keys and values
 * @param string $keyPath A dot-separated string.
 * @param string $valuePath A dot-separated string.
 * @param string $groupPath A dot-separated string.
 * @return array Combined array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::combine
 * @throws CakeException CakeException When keys and values count is unequal.
 */
	public static function combine(array $data, $keyPath, $valuePath = null, $groupPath = null) {
		if (empty($data)) {
			return array();
		}

		if (is_array($keyPath)) {
			$format = array_shift($keyPath);
			$keys = self::format($data, $keyPath, $format);
		} else {
			$keys = self::extract($data, $keyPath);
		}
		if (empty($keys)) {
			return array();
		}

		if (!empty($valuePath) && is_array($valuePath)) {
			$format = array_shift($valuePath);
			$vals = self::format($data, $valuePath, $format);
		} elseif (!empty($valuePath)) {
			$vals = self::extract($data, $valuePath);
		}
		if (empty($vals)) {
			$vals = array_fill(0, count($keys), null);
		}

		if (count($keys) !== count($vals)) {
			throw new CakeException(__d(
				'cake_dev',
				'Hash::combine() needs an equal number of keys + values.'
			));
		}

		if ($groupPath !== null) {
			$group = self::extract($data, $groupPath);
			if (!empty($group)) {
				$c = count($keys);
				for ($i = 0; $i < $c; $i++) {
					if (!isset($group[$i])) {
						$group[$i] = 0;
					}
					if (!isset($out[$group[$i]])) {
						$out[$group[$i]] = array();
					}
					$out[$group[$i]][$keys[$i]] = $vals[$i];
				}
				return $out;
			}
		}
		if (empty($vals)) {
			return array();
		}
		return array_combine($keys, $vals);
	}

/**
 * Returns a formatted series of values extracted from `$data`, using
 * `$format` as the format and `$paths` as the values to extract.
 *
 * Usage:
 *
 * {{{
 * $result = Hash::format($users, array('{n}.User.id', '{n}.User.name'), '%s : %s');
 * }}}
 *
 * The `$format` string can use any format options that `vsprintf()` and `sprintf()` do.
 *
 * @param array $data Source array from which to extract the data
 * @param string $paths An array containing one or more Hash::extract()-style key paths
 * @param string $format Format string into which values will be inserted, see sprintf()
 * @return array An array of strings extracted from `$path` and formatted with `$format`
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::format
 * @see sprintf()
 * @see Hash::extract()
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::format
 */
	public static function format(array $data, array $paths, $format) {
		$extracted = array();
		$count = count($paths);

		if (!$count) {
			return;
		}

		for ($i = 0; $i < $count; $i++) {
			$extracted[] = self::extract($data, $paths[$i]);
		}
		$out = array();
		$data = $extracted;
		$count = count($data[0]);

		$countTwo = count($data);
		for ($j = 0; $j < $count; $j++) {
			$args = array();
			for ($i = 0; $i < $countTwo; $i++) {
				if (array_key_exists($j, $data[$i])) {
					$args[] = $data[$i][$j];
				}
			}
			$out[] = vsprintf($format, $args);
		}
		return $out;
	}

/**
 * Determines if one array contains the exact keys and values of another.
 *
 * @param array $data The data to search through.
 * @param array $needle The values to file in $data
 * @return bool true if $data contains $needle, false otherwise
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::contains
 */
	public static function contains(array $data, array $needle) {
		if (empty($data) || empty($needle)) {
			return false;
		}
		$stack = array();

		while (!empty($needle)) {
			$key = key($needle);
			$val = $needle[$key];
			unset($needle[$key]);

			if (array_key_exists($key, $data) && is_array($val)) {
				$next = $data[$key];
				unset($data[$key]);

				if (!empty($val)) {
					$stack[] = array($val, $next);
				}
			} elseif (!array_key_exists($key, $data) || $data[$key] != $val) {
				return false;
			}

			if (empty($needle) && !empty($stack)) {
				list($needle, $data) = array_pop($stack);
			}
		}
		return true;
	}

/**
 * Test whether or not a given path exists in $data.
 * This method uses the same path syntax as Hash::extract()
 *
 * Checking for paths that could target more than one element will
 * make sure that at least one matching element exists.
 *
 * @param array $data The data to check.
 * @param string $path The path to check for.
 * @return bool Existence of path.
 * @see Hash::extract()
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::check
 */
	public static function check(array $data, $path) {
		$results = self::extract($data, $path);
		if (!is_array($results)) {
			return false;
		}
		return count($results) > 0;
	}

/**
 * Recursively filters a data set.
 *
 * @param array $data Either an array to filter, or value when in callback
 * @param callable $callback A function to filter the data with. Defaults to
 *   `self::_filter()` Which strips out all non-zero empty values.
 * @return array Filtered array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::filter
 */
	public static function filter(array $data, $callback = array('self', '_filter')) {
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$data[$k] = self::filter($v, $callback);
			}
		}
		return array_filter($data, $callback);
	}

/**
 * Callback function for filtering.
 *
 * @param array $var Array to filter.
 * @return bool
 */
	protected static function _filter($var) {
		if ($var === 0 || $var === '0' || !empty($var)) {
			return true;
		}
		return false;
	}

/**
 * Collapses a multi-dimensional array into a single dimension, using a delimited array path for
 * each array element's key, i.e. array(array('Foo' => array('Bar' => 'Far'))) becomes
 * array('0.Foo.Bar' => 'Far').)
 *
 * @param array $data Array to flatten
 * @param string $separator String used to separate array key elements in a path, defaults to '.'
 * @return array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::flatten
 */
	public static function flatten(array $data, $separator = '.') {
		$result = array();
		$stack = array();
		$path = null;

		reset($data);
		while (!empty($data)) {
			$key = key($data);
			$element = $data[$key];
			unset($data[$key]);

			if (is_array($element) && !empty($element)) {
				if (!empty($data)) {
					$stack[] = array($data, $path);
				}
				$data = $element;
				reset($data);
				$path .= $key . $separator;
			} else {
				$result[$path . $key] = $element;
			}

			if (empty($data) && !empty($stack)) {
				list($data, $path) = array_pop($stack);
				reset($data);
			}
		}
		return $result;
	}

/**
 * Expands a flat array to a nested array.
 *
 * For example, unflattens an array that was collapsed with `Hash::flatten()`
 * into a multi-dimensional array. So, `array('0.Foo.Bar' => 'Far')` becomes
 * `array(array('Foo' => array('Bar' => 'Far')))`.
 *
 * @param array $data Flattened array
 * @param string $separator The delimiter used
 * @return array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::expand
 */
	public static function expand($data, $separator = '.') {
		$result = array();

		$stack = array();

		foreach ($data as $flat => $value) {
			$keys = explode($separator, $flat);
			$keys = array_reverse($keys);
			$child = array(
				$keys[0] => $value
			);
			array_shift($keys);
			foreach ($keys as $k) {
				$child = array(
					$k => $child
				);
			}

			$stack[] = array($child, &$result);

			while (!empty($stack)) {
				foreach ($stack as $curKey => &$curMerge) {
					foreach ($curMerge[0] as $key => &$val) {
						if (!empty($curMerge[1][$key]) && (array)$curMerge[1][$key] === $curMerge[1][$key] && (array)$val === $val) {
							$stack[] = array(&$val, &$curMerge[1][$key]);
						} elseif ((int)$key === $key && isset($curMerge[1][$key])) {
							$curMerge[1][] = $val;
						} else {
							$curMerge[1][$key] = $val;
						}
					}
					unset($stack[$curKey]);
				}
				unset($curMerge);
			}
		}
		return $result;
	}

/**
 * This function can be thought of as a hybrid between PHP's `array_merge` and `array_merge_recursive`.
 *
 * The difference between this method and the built-in ones, is that if an array key contains another array, then
 * Hash::merge() will behave in a recursive fashion (unlike `array_merge`). But it will not act recursively for
 * keys that contain scalar values (unlike `array_merge_recursive`).
 *
 * Note: This function will work with an unlimited amount of arguments and typecasts non-array parameters into arrays.
 *
 * @param array $data Array to be merged
 * @param mixed $merge Array to merge with. The argument and all trailing arguments will be array cast when merged
 * @return array Merged array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::merge
 */
	public static function merge(array $data, $merge) {
		$args = array_slice(func_get_args(), 1);
		$return = $data;

		foreach ($args as &$curArg) {
			$stack[] = array((array)$curArg, &$return);
		}
		unset($curArg);

		while (!empty($stack)) {
			foreach ($stack as $curKey => &$curMerge) {
				foreach ($curMerge[0] as $key => &$val) {
					if (!empty($curMerge[1][$key]) && (array)$curMerge[1][$key] === $curMerge[1][$key] && (array)$val === $val) {
						$stack[] = array(&$val, &$curMerge[1][$key]);
					} elseif ((int)$key === $key && isset($curMerge[1][$key])) {
						$curMerge[1][] = $val;
					} else {
						$curMerge[1][$key] = $val;
					}
				}
				unset($stack[$curKey]);
			}
			unset($curMerge);
		}
		return $return;
	}

/**
 * Checks to see if all the values in the array are numeric
 *
 * @param array $data The array to check.
 * @return bool true if values are numeric, false otherwise
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::numeric
 */
	public static function numeric(array $data) {
		if (empty($data)) {
			return false;
		}
		return $data === array_filter($data, 'is_numeric');
	}

/**
 * Counts the dimensions of an array.
 * Only considers the dimension of the first element in the array.
 *
 * If you have an un-even or heterogenous array, consider using Hash::maxDimensions()
 * to get the dimensions of the array.
 *
 * @param array $data Array to count dimensions on
 * @return int The number of dimensions in $data
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::dimensions
 */
	public static function dimensions(array $data) {
		if (empty($data)) {
			return 0;
		}
		reset($data);
		$depth = 1;
		while ($elem = array_shift($data)) {
			if (is_array($elem)) {
				$depth += 1;
				$data =& $elem;
			} else {
				break;
			}
		}
		return $depth;
	}

/**
 * Counts the dimensions of *all* array elements. Useful for finding the maximum
 * number of dimensions in a mixed array.
 *
 * @param array $data Array to count dimensions on
 * @return int The maximum number of dimensions in $data
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::maxDimensions
 */
	public static function maxDimensions(array $data) {
		$depth = array();
		if (is_array($data) && reset($data) !== false) {
			foreach ($data as $value) {
				$depth[] = self::dimensions((array)$value) + 1;
			}
		}
		return max($depth);
	}

/**
 * Map a callback across all elements in a set.
 * Can be provided a path to only modify slices of the set.
 *
 * @param array $data The data to map over, and extract data out of.
 * @param string $path The path to extract for mapping over.
 * @param callable $function The function to call on each extracted value.
 * @return array An array of the modified values.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::map
 */
	public static function map(array $data, $path, $function) {
		$values = (array)self::extract($data, $path);
		return array_map($function, $values);
	}

/**
 * Reduce a set of extracted values using `$function`.
 *
 * @param array $data The data to reduce.
 * @param string $path The path to extract from $data.
 * @param callable $function The function to call on each extracted value.
 * @return mixed The reduced value.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::reduce
 */
	public static function reduce(array $data, $path, $function) {
		$values = (array)self::extract($data, $path);
		return array_reduce($values, $function);
	}

/**
 * Apply a callback to a set of extracted values using `$function`.
 * The function will get the extracted values as the first argument.
 *
 * ### Example
 *
 * You can easily count the results of an extract using apply().
 * For example to count the comments on an Article:
 *
 * `$count = Hash::apply($data, 'Article.Comment.{n}', 'count');`
 *
 * You could also use a function like `array_sum` to sum the results.
 *
 * `$total = Hash::apply($data, '{n}.Item.price', 'array_sum');`
 *
 * @param array $data The data to reduce.
 * @param string $path The path to extract from $data.
 * @param callable $function The function to call on each extracted value.
 * @return mixed The results of the applied method.
 */
	public static function apply(array $data, $path, $function) {
		$values = (array)self::extract($data, $path);
		return call_user_func($function, $values);
	}

/**
 * Sorts an array by any value, determined by a Set-compatible path
 *
 * ### Sort directions
 *
 * - `asc` Sort ascending.
 * - `desc` Sort descending.
 *
 * ## Sort types
 *
 * - `regular` For regular sorting (don't change types)
 * - `numeric` Compare values numerically
 * - `string` Compare values as strings
 * - `natural` Compare items as strings using "natural ordering" in a human friendly way.
 *   Will sort foo10 below foo2 as an example. Requires PHP 5.4 or greater or it will fallback to 'regular'
 *
 * @param array $data An array of data to sort
 * @param string $path A Set-compatible path to the array value
 * @param string $dir See directions above. Defaults to 'asc'.
 * @param string $type See direction types above. Defaults to 'regular'.
 * @return array Sorted array of data
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::sort
 */
	public static function sort(array $data, $path, $dir = 'asc', $type = 'regular') {
		if (empty($data)) {
			return array();
		}
		$originalKeys = array_keys($data);
		$numeric = is_numeric(implode('', $originalKeys));
		if ($numeric) {
			$data = array_values($data);
		}
		$sortValues = self::extract($data, $path);
		$sortCount = count($sortValues);
		$dataCount = count($data);

		// Make sortValues match the data length, as some keys could be missing
		// the sorted value path.
		if ($sortCount < $dataCount) {
			$sortValues = array_pad($sortValues, $dataCount, null);
		}
		$result = self::_squash($sortValues);
		$keys = self::extract($result, '{n}.id');
		$values = self::extract($result, '{n}.value');

		$dir = strtolower($dir);
		$type = strtolower($type);
		if ($type === 'natural' && version_compare(PHP_VERSION, '5.4.0', '<')) {
			$type = 'regular';
		}
		if ($dir === 'asc') {
			$dir = SORT_ASC;
		} else {
			$dir = SORT_DESC;
		}
		if ($type === 'numeric') {
			$type = SORT_NUMERIC;
		} elseif ($type === 'string') {
			$type = SORT_STRING;
		} elseif ($type === 'natural') {
			$type = SORT_NATURAL;
		} else {
			$type = SORT_REGULAR;
		}
		array_multisort($values, $dir, $type, $keys, $dir, $type);
		$sorted = array();
		$keys = array_unique($keys);

		foreach ($keys as $k) {
			if ($numeric) {
				$sorted[] = $data[$k];
				continue;
			}
			if (isset($originalKeys[$k])) {
				$sorted[$originalKeys[$k]] = $data[$originalKeys[$k]];
			} else {
				$sorted[$k] = $data[$k];
			}
		}
		return $sorted;
	}

/**
 * Helper method for sort()
 * Squashes an array to a single hash so it can be sorted.
 *
 * @param array $data The data to squash.
 * @param string $key The key for the data.
 * @return array
 */
	protected static function _squash($data, $key = null) {
		$stack = array();
		foreach ($data as $k => $r) {
			$id = $k;
			if ($key !== null) {
				$id = $key;
			}
			if (is_array($r) && !empty($r)) {
				$stack = array_merge($stack, self::_squash($r, $id));
			} else {
				$stack[] = array('id' => $id, 'value' => $r);
			}
		}
		return $stack;
	}

/**
 * Computes the difference between two complex arrays.
 * This method differs from the built-in array_diff() in that it will preserve keys
 * and work on multi-dimensional arrays.
 *
 * @param array $data First value
 * @param array $compare Second value
 * @return array Returns the key => value pairs that are not common in $data and $compare
 *    The expression for this function is ($data - $compare) + ($compare - ($data - $compare))
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::diff
 */
	public static function diff(array $data, $compare) {
		if (empty($data)) {
			return (array)$compare;
		}
		if (empty($compare)) {
			return (array)$data;
		}
		$intersection = array_intersect_key($data, $compare);
		while (($key = key($intersection)) !== null) {
			if ($data[$key] == $compare[$key]) {
				unset($data[$key]);
				unset($compare[$key]);
			}
			next($intersection);
		}
		return $data + $compare;
	}

/**
 * Merges the difference between $data and $compare onto $data.
 *
 * @param array $data The data to append onto.
 * @param array $compare The data to compare and append onto.
 * @return array The merged array.
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::mergeDiff
 */
	public static function mergeDiff(array $data, $compare) {
		if (empty($data) && !empty($compare)) {
			return $compare;
		}
		if (empty($compare)) {
			return $data;
		}
		foreach ($compare as $key => $value) {
			if (!array_key_exists($key, $data)) {
				$data[$key] = $value;
			} elseif (is_array($value)) {
				$data[$key] = self::mergeDiff($data[$key], $compare[$key]);
			}
		}
		return $data;
	}

/**
 * Normalizes an array, and converts it to a standard format.
 *
 * @param array $data List to normalize
 * @param bool $assoc If true, $data will be converted to an associative array.
 * @return array
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::normalize
 */
	public static function normalize(array $data, $assoc = true) {
		$keys = array_keys($data);
		$count = count($keys);
		$numeric = true;

		if (!$assoc) {
			for ($i = 0; $i < $count; $i++) {
				if (!is_int($keys[$i])) {
					$numeric = false;
					break;
				}
			}
		}
		if (!$numeric || $assoc) {
			$newList = array();
			for ($i = 0; $i < $count; $i++) {
				if (is_int($keys[$i])) {
					$newList[$data[$keys[$i]]] = null;
				} else {
					$newList[$keys[$i]] = $data[$keys[$i]];
				}
			}
			$data = $newList;
		}
		return $data;
	}

/**
 * Takes in a flat array and returns a nested array
 *
 * ### Options:
 *
 * - `children` The key name to use in the resultset for children.
 * - `idPath` The path to a key that identifies each entry. Should be
 *   compatible with Hash::extract(). Defaults to `{n}.$alias.id`
 * - `parentPath` The path to a key that identifies the parent of each entry.
 *   Should be compatible with Hash::extract(). Defaults to `{n}.$alias.parent_id`
 * - `root` The id of the desired top-most result.
 *
 * @param array $data The data to nest.
 * @param array $options Options are:
 * @return array of results, nested
 * @see Hash::extract()
 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html#Hash::nest
 */
	public static function nest(array $data, $options = array()) {
		if (!$data) {
			return $data;
		}

		$alias = key(current($data));
		$options += array(
			'idPath' => "{n}.$alias.id",
			'parentPath' => "{n}.$alias.parent_id",
			'children' => 'children',
			'root' => null
		);

		$return = $idMap = array();
		$ids = self::extract($data, $options['idPath']);

		$idKeys = explode('.', $options['idPath']);
		array_shift($idKeys);

		$parentKeys = explode('.', $options['parentPath']);
		array_shift($parentKeys);

		foreach ($data as $result) {
			$result[$options['children']] = array();

			$id = self::get($result, $idKeys);
			$parentId = self::get($result, $parentKeys);

			if (isset($idMap[$id][$options['children']])) {
				$idMap[$id] = array_merge($result, (array)$idMap[$id]);
			} else {
				$idMap[$id] = array_merge($result, array($options['children'] => array()));
			}
			if (!$parentId || !in_array($parentId, $ids)) {
				$return[] =& $idMap[$id];
			} else {
				$idMap[$parentId][$options['children']][] =& $idMap[$id];
			}
		}

		if ($options['root']) {
			$root = $options['root'];
		} elseif (!$return) {
			return array();
		} else {
			$root = self::get($return[0], $parentKeys);
		}

		foreach ($return as $i => $result) {
			$id = self::get($result, $idKeys);
			$parentId = self::get($result, $parentKeys);
			if ($id !== $root && $parentId != $root) {
				unset($return[$i]);
			}
		}
		return array_values($return);
	}

}


/**
 * Washes strings from unwanted noise.
*
* Helpful methods to make unsafe strings usable.
*
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @package       Cake.Utility
* @since         CakePHP(tm) v 0.10.0.1076
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/


/**
 * Data Sanitization.
 *
 * Removal of alphanumeric characters, SQL-safe slash-added strings, HTML-friendly strings,
 * and all of the above on arrays.
 *
 * @package       Cake.Utility
 * @deprecated    Deprecated since version 2.4
*/
class Sanitize {

	/**
	 * Removes any non-alphanumeric characters.
	 *
	 * @param string $string String to sanitize
	 * @param array $allowed An array of additional characters that are not to be removed.
	 * @return string Sanitized string
	 */
	public static function paranoid($string, $allowed = array()) {
		$allow = null;
		if (!empty($allowed)) {
			foreach ($allowed as $value) {
				$allow .= "\\$value";
			}
		}

		if (!is_array($string)) {
			return preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $string);
		}

		$cleaned = array();
		foreach ($string as $key => $clean) {
			$cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $clean);
		}

		return $cleaned;
	}


	/**
	 * Returns given string safe for display as HTML. Renders entities.
	 *
	 * strip_tags() does not validating HTML syntax or structure, so it might strip whole passages
	 * with broken HTML.
	 *
	 * ### Options:
	 *
	 * - remove (boolean) if true strips all HTML tags before encoding
	 * - charset (string) the charset used to encode the string
	 * - quotes (int) see http://php.net/manual/en/function.htmlentities.php
	 * - double (boolean) double encode html entities
	 *
	 * @param string $string String from where to strip tags
	 * @param array $options Array of options to use.
	 * @return string Sanitized string
	 */
	public static function html($string, $options = array()) {
		static $defaultCharset = false;
		if ($defaultCharset === false) {
			$defaultCharset = __CONFIG_CHARSET;
			if ($defaultCharset === null) {
				$defaultCharset = 'UTF-8';
			}
		}
		$default = array(
				'remove' => false,
				'charset' => $defaultCharset,
				'quotes' => ENT_QUOTES,
				'double' => true
		);

		$options = array_merge($default, $options);

		if ($options['remove']) {
			$string = strip_tags($string);
		}

		return htmlentities($string, $options['quotes'], $options['charset'], $options['double']);
	}

	/**
	 * Strips extra whitespace from output
	 *
	 * @param string $str String to sanitize
	 * @return string whitespace sanitized string
	 */
	public static function stripWhitespace($str) {
		return preg_replace('/\s{2,}/u', ' ', preg_replace('/[\n\r\t]+/', '', $str));
	}

	/**
	 * Strips image tags from output
	 *
	 * @param string $str String to sanitize
	 * @return string Sting with images stripped.
	 */
	public static function stripImages($str) {
		$preg = array(
				'/(<a[^>]*>)(<img[^>]+alt=")([^"]*)("[^>]*>)(<\/a>)/i' => '$1$3$5<br />',
				'/(<img[^>]+alt=")([^"]*)("[^>]*>)/i' => '$2<br />',
				'/<img[^>]*>/i' => ''
		);

		return preg_replace(array_keys($preg), array_values($preg), $str);
	}

	/**
	 * Strips scripts and stylesheets from output
	 *
	 * @param string $str String to sanitize
	 * @return string String with <link>, <img>, <script>, <style> elements and html comments removed.
	 */
	public static function stripScripts($str) {
		$regex =
		'/(<link[^>]+rel="[^"]*stylesheet"[^>]*>|' .
		'<img[^>]*>|style="[^"]*")|' .
		'<script[^>]*>.*?<\/script>|' .
		'<style[^>]*>.*?<\/style>|' .
		'<!--.*?-->/is';
		return preg_replace($regex, '', $str);
	}

	/**
	 * Strips extra whitespace, images, scripts and stylesheets from output
	 *
	 * @param string $str String to sanitize
	 * @return string sanitized string
	 */
	public static function stripAll($str) {
		return Sanitize::stripScripts(
				Sanitize::stripImages(
						Sanitize::stripWhitespace($str)
				)
		);
	}

	/**
	 * Strips the specified tags from output. First parameter is string from
	 * where to remove tags. All subsequent parameters are tags.
	 *
	 * Ex.`$clean = Sanitize::stripTags($dirty, 'b', 'p', 'div');`
	 *
	 * Will remove all `<b>`, `<p>`, and `<div>` tags from the $dirty string.
	 *
	 * @param string $str,... String to sanitize
	 * @return string sanitized String
	 */
	public static function stripTags($str) {
		$params = func_get_args();

		for ($i = 1, $count = count($params); $i < $count; $i++) {
			$str = preg_replace('/<' . $params[$i] . '\b[^>]*>/i', '', $str);
			$str = preg_replace('/<\/' . $params[$i] . '[^>]*>/i', '', $str);
		}
		return $str;
	}

	/**
	 * Sanitizes given array or value for safe input. Use the options to specify
	 * the connection to use, and what filters should be applied (with a boolean
	 * value). Valid filters:
	 *
	 * - odd_spaces - removes any non space whitespace characters
	 * - encode - Encode any html entities. Encode must be true for the `remove_html` to work.
	 * - dollar - Escape `$` with `\$`
	 * - carriage - Remove `\r`
	 * - unicode -
	 * - escape - Should the string be SQL escaped.
	 * - backslash -
	 * - remove_html - Strip HTML with strip_tags. `encode` must be true for this option to work.
	 *
	 * @param string|array $data Data to sanitize
	 * @param string|array $options If string, DB connection being used, otherwise set of options
	 * @return mixed Sanitized data
	 */
	public static function clean($data, $options = array()) {
		if (empty($data)) {
			return $data;
		}

		if (!is_array($options)) {
			$options = array('connection' => $options);
		}

		$options = array_merge(array(
				'connection' => 'default',
				'odd_spaces' => true,
				'remove_html' => false,
				'encode' => true,
				'dollar' => true,
				'carriage' => true,
				'unicode' => true,
				'escape' => true,
				'backslash' => true
		), $options);

		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$data[$key] = Sanitize::clean($val, $options);
			}
			return $data;
		}

		if ($options['odd_spaces']) {
			$data = str_replace(chr(0xCA), '', $data);
		}
		if ($options['encode']) {
			$data = Sanitize::html($data, array('remove' => $options['remove_html']));
		}
		if ($options['dollar']) {
			$data = str_replace("\\\$", "$", $data);
		}
		if ($options['carriage']) {
			$data = str_replace("\r", "", $data);
		}
		if ($options['unicode']) {
			$data = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $data);
		}

		if ($options['backslash']) {
			$data = preg_replace("/\\\(?!&amp;#|\?#)/", "\\", $data);
		}
		return $data;
	}
}
