<?php

namespace Translate;

class Translate {
	/** @var null|Translate */
	private static $instance = null;
	private $locale = 'en';
	private $json_files_path;
	private $l = array();

	/**
	 * @param string $json_files_path Absolute path to directory with all locale.json files
	 * @param string $locale
	 * @return Translate
	 */
	public function __construct($json_files_path, $locale = 'en') {
		self::$instance = $this;
		$this->set_json_files_path($json_files_path);
		$this->set_locale('en');
	}

	/**
	 * Get last created instance
	 *
	 * @return null|Translate
	 */
	public static function getLastInstance() {
		return self::$instance;
	}

	/**
	 * @param string $locale
	 */
	public function set_locale($locale) {
		$this->locale = $locale;

		$json_file = $this->json_files_path.'/'.$this->locale.'.json';
		if (file_exists($json_file)) {
			$l = json_decode(file_get_contents($json_file));
			if ($l) {
				$this->l = array_merge($this->l, (array)$l);
			}
		}
	}

	/**
	 * @param string $json_files_path
	 * @throws \Exception
	 */
	public function set_json_files_path($json_files_path) {
		$this->json_files_path = $json_files_path;
		if (!file_exists($this->json_files_path) && !mkdir($this->json_files_path, 0755, true)) {
			throw new \Exception('Cannot create json_files_path as "'.$this->json_files_path.'"');
		}
	}

	/**
	 * Generate PHP executable content from a json text template
	 *
	 * @param string $text_template
	 * @return string
	 * @todo can compile properly. Currently, replace when only 1 special char without test all string. Use strpos ?
	 */
	public function compile($text_template) {
		return preg_replace(array(
								 '#\'#',
								 '#(?<!\\\\)(\\$\\w+)#',
								 '#^(.*)$#',
								 '#(?<!\\\\)\\{\'\\.\\$(\\w+)\\.\',\\s*(\\w+),\\s*#',
								 '#\\s*other\\s*\\[#',
								 '#\\s*(\\w+)\\s*\\[#',
								 '#(?<!\\\\)\\]\\s*\\}#',
								 '#(?<!\\\\)\\]#',
								 '#\\\\([\\$\\{\\}\\[\\]])#',
							), array(
									'\\\'',
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
	public function preCompile($input_dir, $output_dir) {
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
	 * @param array|string $params associative array or string to json format
	 * @return array
	 * @throws \Exception
	 */
	private function check_params($params) {
		if (!is_array($params)) {
			$params = json_decode($params);
			if (!is_array($params)) {
				throw new \Exception('Bad params format');
			}
		}
		return $params;
	}

	/**
	 * Parse a compiled text with params for chosen local
	 *
	 * @param string       $compiled_text
	 * @param array|string $params Associative array
	 * @return string
	 * @throws \Exception
	 */
	public function parse($compiled_text, $params = array()) {
		static $initialized = false;

		$class_name = '\\Translate\\Functions\\'.$this->locale;
		if (!$initialized) {
			if (!class_exists($class_name, false)) {
				$file_name = __DIR__.'/functions/'.$this->locale.'.php';
				if (file_exists($file_name)) {
					require $file_name;
				}
				if (!class_exists($class_name, false)) {
					throw new \Exception('Locale methods class for "'.$this->locale.'" does not exists');
				}
			}
			$initialized = true;
		}

		$l      = new $class_name();
		$params = $this->check_params($params);
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

	/**
	 * @param string       $template
	 * @param array|string $params
	 * @return string
	 */
	public function parseFromTemplate($template, $params = array()) {
		return $this->parse($this->compile($template), $params);
	}

	/**
	 * @param string       $key
	 * @param array|string $params
	 * @return string
	 */
	public function parseFromKey($key, $params = array()) {
		return isset($this->l[$key]) ? $this->parseFromTemplate($this->l[$key], $params) : '';
	}
}