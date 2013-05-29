<?php

namespace Translate\Templates;

use Twig_Extension;
use Twig_Function_Method;
use Twig_Environment;

/**
 * TraductionPHP Twig extension
 *
 * @namespace Framework\Services\Templates
 * @package   framework
 */
class Twig extends Twig_Extension {
	/** @var \Translate\Translate */
	protected $translate;

	/**
	 * Get Twig extension name
	 *
	 * @return string
	 */
	public function getName() {
		return 'TraductionPHP';
	}

	/**
	 * Initializes the runtime environment.
	 *
	 * This is where you can load some file that contains filter functions for instance.
	 *
	 * @param Twig_Environment $environment The current Twig_Environment instance
	 * @throws \Exception
	 */
	public function initRuntime(Twig_Environment $environment) {
		require_once dirname(__DIR__).'/Translate.php';
		$this->translate = \Translate\Translate::getLastInstance();
		if (!$this->translate) {
			throw new \Exception('Translate must be instancied before call this extension');
		}
	}

	/**
	 * Get Twig extension functions list
	 *
	 * @return    Twig_Function_Method[]
	 */
	public function getFunctions() {
		return array(
			'translate_key'      => new Twig_Function_Method($this, 'translateKey', array('is_safe' => array('all'))),
			'translate_template' => new Twig_Function_Method($this, 'translateTemplate', array('is_safe' => array('all'))),
			'translate_compiled' => new Twig_Function_Method($this, 'translateCompiled', array('is_safe' => array('all'))),
		);
	}

	/**
	 * @param string $key
	 * @param array  $params associative array or string to json format
	 * @param bool   $protect
	 * @return string
	 */
	public function translateKey($key, $params = array(), $protect = true) {
		$text = $this->translate->parseFromKey($key, $params);
		if ($protect) {
			$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
		}
		return $text;
	}

	/**
	 * @param string       $template
	 * @param array|string $params associative array or string to json format
	 * @param bool         $protect
	 * @return string
	 */
	public function translateTemplate($template, $params = array(), $protect = true) {
		$text = $this->translate->parseFromTemplate($template, $params);
		if ($protect) {
			$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
		}
		return $text;
	}

	/**
	 * @param string $compiled
	 * @param array  $params associative array or string to json format
	 * @param bool   $protect
	 * @return string
	 */
	public function translateCompiled($compiled, $params = array(), $protect = true) {
		$text = $this->translate->parse($compiled, $params);
		if ($protect) {
			$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
		}
		return $text;
	}
}
