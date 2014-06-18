<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech Funbox. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit_Framework_TestCase as TestCase;

ensureFn('isHttps');

/**
 * This class tests the isHttps() function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class IsHttpsTest extends TestCase
{

	public function testHttpsNotSet() {
		$this->assertFalse(isHttps());
	}

	public function testEmptyHttps() {
		$_SERVER['HTTPS'] = '';
		$this->assertFalse(isHttps());
	}

	public function testNullHttps() {
		$_SERVER['HTTPS'] = null;
		$this->assertFalse(isHttps());
	}

	public function testFalseHttps() {
		$_SERVER['HTTPS'] = false;
		$this->assertFalse(isHttps());
	}

	public function testHttpsOff() {
		$_SERVER['HTTPS'] = 'off';
		$this->assertFalse(isHttps());
	}

	public function testHttpsOn() {
		$_SERVER['HTTPS'] = 'on';
		$this->assertTrue(isHttps());
	}
}
