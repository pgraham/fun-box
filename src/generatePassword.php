<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech FunBox and is licensed by the Copyright holder
 * under the 3-clause BSD License.  The full text of the license can be found in
 * the LICENSE.txt file included in the root directory of this distribution or
 * at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\fn {

	class PasswordGenerator {

		const DEFAULT_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSSTUVWXYZ0123456789~!@#$%^*()-_=+[]{};:,.<>?';

		public static function generate($length, $chars) {
			if ($chars === null) {
				$chars = self::DEFAULT_CHARS;
			}

			if (is_array($chars)) {
				$chars = implode('', $chars);
			}

			$pw = '';
			$maxIdx = strlen($chars) - 1;

			for ($i = 0; $i < $length; $i++) {
				$pw .= substr($chars, rand(0, $maxIdx), 1);
			}

			return $pw;
		}
	}

} // End zpt\fn namespace

namespace {

	/**
	* This function will generate a password. Length and valid characters can be
	* specified.
	*
	* @param int $length [Optional] The length of the password. Defaults to 15
	* @param mixed $chars [Optional] Either a string or a numeric array containing
	* the set of valid characters. Default is:
	*
	*     abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSSTUVWXYZ0123456789~!@#$%^*()-_=+[]{};:,.<>?
	*/
	function generatePassword($length = 15, $chars = null) {
		return zpt\fn\PasswordGenerator::generate($length, $chars);
	}

}
