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

/**
 * This class tests the ensureFn() function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EnsureFnTest extends TestCase {

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testFunctionNameAsString() {
		ensureFn('announceWait');
		$this->assertTrue(function_exists('announceWait'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testFunctionNamesInArray() {
		ensureFn(array('applyDatabaseAlters', 'getcmdln'));

		$this->assertTrue(function_exists('applyDatabaseAlters'));
		$this->assertTrue(function_exists('getcmdln'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testFunctionNamesInNestedArray() {
		ensureFn(
			'announceWait',
			array( 'applyDatabaseAlters', array( 'getcmdln' ) )
		);

		$this->assertTrue(function_exists('announceWait'));
		$this->assertTrue(function_exists('applyDatabaseAlters'));
		$this->assertTrue(function_exists('getcmdln'));
	}
}
