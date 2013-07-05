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
ensureFn('getcmdln');

/**
 * This class tests the getcmdln function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GetcmdlnTest extends TestCase {

	public function testLongOptions() {
		$argv = array(
			'script.php',
			'--longoption=myvalue',
			'--anotheroption=myvalue',
		);
		$cmdln = getcmdln($argv, array('longoption', 'anotheroption'));

		$this->assertInstanceOf('StdClass', $cmdln);
		$this->assertObjectHasAttribute('opt', $cmdln);
		$this->assertInternalType('array', $cmdln->opt);

		$this->assertArrayHasKey('longoption', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['longoption']);
		$this->assertArrayHasKey('anotheroption', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['anotheroption']);
	}

	public function testLongOptionsHyphenated() {
		$argv = array(
			'script.php',
			'--long-option=myvalue',
			'--another-option=myvalue',
		);
		$cmdln = getcmdln($argv, array('long-option', 'another-option'));

		$this->assertInstanceOf('StdClass', $cmdln);
		$this->assertObjectHasAttribute('opt', $cmdln);
		$this->assertInternalType('array', $cmdln->opt);

		$this->assertArrayHasKey('long-option', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['long-option']);
		$this->assertArrayHasKey('another-option', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['another-option']);

	}

	public function testArgs() {
		$argv = array(
			'script.php',
			'arg1',
			'arg2'
		);
		$cmdln = getcmdln($argv);

		$this->assertInstanceOf('StdClass', $cmdln);
		$this->assertObjectHasAttribute('argv', $cmdln);
		$this->assertInternalType('array', $cmdln->argv);

		$this->assertObjectHasAttribute('argc', $cmdln);
		$this->assertInternalType('int', $cmdln->argc);
		$this->assertEquals(3, $cmdln->argc);

		$this->assertCount(3, $cmdln->argv);
		$this->assertEquals(array('script.php', 'arg1', 'arg2'), $cmdln->argv);
	}

	public function testLongOptionsWithArgsExpectedOrder() {
		$argv = array(
			'script.php',
			'--longoption=myvalue',
			'--anotheroption=myvalue',
			'arg1',
			'arg2'
		);
		$cmdln = getcmdln($argv, array('longoption', 'anotheroption'));

		$this->assertInstanceOf('StdClass', $cmdln);
		$this->assertObjectHasAttribute('opt', $cmdln);
		$this->assertInternalType('array', $cmdln->opt);

		$this->assertArrayHasKey('longoption', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['longoption']);
		$this->assertArrayHasKey('anotheroption', $cmdln->opt);
		$this->assertEquals('myvalue', $cmdln->opt['anotheroption']);

		$this->assertObjectHasAttribute('argv', $cmdln);
		$this->assertInternalType('array', $cmdln->argv);

		$this->assertObjectHasAttribute('argc', $cmdln);
		$this->assertInternalType('int', $cmdln->argc);
		$this->assertEquals(3, $cmdln->argc);

		$this->assertCount(3, $cmdln->argv);
		$this->assertEquals(array('script.php', 'arg1', 'arg2'), $cmdln->argv);
	}
}
