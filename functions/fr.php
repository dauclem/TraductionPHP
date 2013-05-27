<?php

namespace Translate\Functions;

class fr {
	/**
	 * Calculate type of plural. Zero is specific because can display different text of one.
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