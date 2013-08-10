<?php

namespace Translate\Functions;

class en {
	/**
	 * Calculate type of plural.
	 *
	 * @param int|float|double $number
	 * @return string
	 */
	public static function plural($number) {
		switch ($number) {
			case 0 : return 'zero';
			case 1 : return 'one';
			default : return 'other';
		}
	}
}