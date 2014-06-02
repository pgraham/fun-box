<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of Fun-box. For the full copyright and license information
 * please view the LICENSE file that was distributed with this source code.
 */
namespace zpt\fn\test;

use zpt\fn\GitUtil;
use PHPUnit_Framework_TestCase as TestCase;

require __DIR__ . '/test-setup.php';

/**
 * This class tests the GitUtil class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GitUtilTest extends TestCase
{

	private $gitRepo;

	protected function setUp() {
		$this->gitRepo = __DIR__ . '/git-test';
		if (file_exists($this->gitRepo)) {
			exec(String('rm -rf {0}')->format($this->gitRepo));
		}
		mkdir($this->gitRepo);
	}

	public function testInitRepo() {
		$r = GitUtil::initRepo($this->gitRepo);
		$this->assertTrue($r);
		$this->assertFileExists("$this->gitRepo/.git");
	}

	public function testCommit() {
		GitUtil::initRepo($this->gitRepo);

		file_put_contents("$this->gitRepo/README", "A test repo. Clobber me.");
		$r = GitUtil::commit($this->gitRepo, 'Added readme');

		$this->assertTrue($r);
	}

	public function testCreateTag() {
		GitUtil::initRepo($this->gitRepo);

		file_put_contents("$this->gitRepo/README", "A test repo. Clobber me.");
		$r = GitUtil::commit($this->gitRepo, 'Added readme');

		$r = GitUtil::tag($this->gitRepo, 'test-tag');

		$this->assertTrue($r);
	}

	public function testMoveTag() {
		GitUtil::initRepo($this->gitRepo);

		file_put_contents("$this->gitRepo/README", "A test repo. Clobber me.");
		GitUtil::commit($this->gitRepo, 'Added readme');

		GitUtil::tag($this->gitRepo, 'test-tag');

		file_put_contents("$this->gitRepo/README", "A test repo. Clobber moi.");
		GitUtil::commit($this->gitRepo, "Updated readme");

		GitUtil::tag($this->gitRepo, 'test-tag', null, true);
	}
}
