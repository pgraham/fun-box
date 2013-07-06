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
ensureFn('is_createable');

/**
 * This class tests the is_createable function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IsCreateableTest extends TestCase {

	public function testSuccess() {
		$this->assertTrue(is_createable(__DIR__ . '/afile'));
	}

	public function testFailure() {
		// This assumes that the tests are not run as root!
		$this->assertFalse(is_createable('/afile'));
	}

	public function testRelativePath() {
		$this->assertTrue(is_createable(__DIR__ . '/afile/..'));

		// Determine the relative path from the current directory to the root
		// directory
		$dir = __DIR__;
		$distanceFromRoot = 0;
		while($dir !== '/') {
			$distanceFromRoot += 1;
			$dir = dirname($dir);
		}

		$path = __DIR__;
		for ($i = 0; $i < $distanceFromRoot; $i++) {
			$path .= "/..";
		}
		$this->assertFalse(is_createable($path));
	}

	public function testNotAPath() {
		$this->assertTrue(is_createable('afile.php'));
	}
}
