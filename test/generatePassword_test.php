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

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit_Framework_TestCase as TestCase;

ensureFn('generatePassword');

/**
 * This class tests the generatePassword function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GeneratePasswordTest extends TestCase {

	public function testGeneratePasswordDefault() {
		$password = generatePassword();

		$this->assertEquals(15, strlen($password));

		$reCharSet = '[^'
		           . preg_quote(zpt\fn\PasswordGenerator::DEFAULT_CHARS, '/')
		           . ']';

		$this->assertEquals(0, preg_match("/$reCharSet/", $password));
	}
}
