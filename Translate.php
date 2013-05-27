<?php

namespace Translate;

class Translate {
	/**
	 * Generate PHP executable content from a json text template
	 *
	 * @param string $text_template
	 * @return string
	 * @todo can compile properly. Currently, replace when only 1 special char without test all string. Use strpos ?
	 */
	public static function compile($text_template) {
		return preg_replace(array(
								 '#(?<!\\\\)(\\$\\w+)#',
								 '#^(.*)$#',
								 '#(?<!\\\\)\\{\'\\.\\$(\\w+)\\.\',\\s*(\\w+),\\s*#',
								 '#\\s*other\\s*\\[#',
								 '#\\s*(\\w+)\\s*\\[#',
								 '#(?<!\\\\)\\]\\s*\\}#',
								 '#(?<!\\\\)\\]#',
								 '#\\\\([\\$\\{\\}\\[\\]])#',
							), array(
									'\'.$1.\'',
									'$r=\'$1\';',
									'\';switch(method_exists($l,\'$2\')?$l->$2($$1):$$1){',
									'default:$r.=\'',
									'case \'$1\':$r.=\'',
									'\';break;}$r.=\'',
									'\';break;',
									'$1',
							   ), $text_template);
	}

	/**
	 * Parse json files with templates to generate php files.
	 *
	 * @param string $input_dir
	 * @param string $output_dir
	 */
	public static function preCompile($input_dir, $output_dir) {
		$input_dir  = preg_replace('#\\/$#', '', $input_dir);
		$output_dir = preg_replace('#\\/$#', '', $output_dir);

		$regexp = '#^'.preg_quote($input_dir, '#').'/([^\\/]+)\\.json$#';
		foreach (glob($input_dir.'/*.json') as $filename) {
			if (preg_match($regexp, $filename, $matches)) {
				$locale = $matches[1];

				$content = '<?php $translate_keys[\''.$locale.'\']=array('."\n";
				foreach (json_decode(file_get_contents($filename)) as $key => $text_template) {
					$content .= '\''.$key.'\'=>\''.str_replace('\'', '\\\'', self::compile($text_template)).'\','."\n";
				}
				$content .= ');';

				file_put_contents($output_dir.'/'.$locale.'.php', $content);
			}
		}
	}

	/**
	 * Parse a compiled text with params for chosen local
	 *
	 * @param string $locale
	 * @param string $compiled_text
	 * @param array  $params Associative array
	 * @return string
	 * @throws \Exception
	 */
	public static function parse($locale, $compiled_text, $params = array()) {
		$class_name = '\\Translate\\Functions\\'.$locale;
		if (!class_exists($class_name)) {
			$file_name = __DIR__.'/functions/'.$locale.'.php';
			if (file_exists($file_name)) {
				require $file_name;
			}
			if (!class_exists($class_name)) {
				throw new \Exception('Locale methods class for "'.$locale.'" does not exists');
			}
		}

		$l = new $class_name();
		foreach ($params as $k => $v) {
			$$k = $v;
		}

		try {
			eval($compiled_text);
		} catch (\Exception $e) {
			throw new \Exception('Syntax error for "'.$compiled_text.'"');
		}

		return $r;
	}

	public static function parseFromKey($locale, $key, $params) {

	}
}