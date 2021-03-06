<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech FunBox and is licensed by the Copyright holder
 * under the 3-clause BSD License.	The full text of the license can be found in
 * the LICENSE.txt file included in the root directory of this distribution or
 * at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\fn {

/**
 * Static interface for interacting with a git repository.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GitUtil {

	const ADD_ALL = 'git add -A .';
	const CLONE_CMD_TMPL = "git clone -v %s %s";
	const COMMIT_CMD = 'git commit -am "{0}"';
	const EXPORT_CMD_TMPL = "git archive --remote={src} --prefix={pre}/ {ver} | tar -x -C {tgt}";
	const FETCH_CMD_TMPL = "git fetch %s";
	const INIT_CMD = 'git init';
	const INIT_SUBMODULES_CMD = "git submodule update --init --rebase";
	const MERGE_CMD_TMPL = "git merge %s/%s";
	const MOVE_TAG_CMD = 'git tag -f {tag} {version}';
	const TAG_CMD = 'git tag -a {tag} -m "{msg}" {version}';
	const UPDATE_SUBMODULES_CMD = 'git submodule update --rebase';

	/**
	 * Clone a git repository.
	 *
	 * @param $repo
	 * @param $path
	 */
	public static function cloneRepo($repo, $path, $initSubModules = false) {
		$cloneCmd = sprintf(self::CLONE_CMD_TMPL,
			$repo,
			$path);
		passthru($cloneCmd);

		if ($initSubModules) {
			self::initSubmodules($path);
		}
	}

	/**
	 * Commit all pending changes in a repository.
	 *
	 * @param string $repo
	 *   Path to the repository.
	 * @param string $message
	 *   The commit message.
	 */
	public static function commit($repo, $msg) {
		$cmds =[
			String(self::ADD_ALL),
			String(self::COMMIT_CMD)->format($msg)
		];

		return self::doInDir($repo, $cmds);
	}

	/**
	 * Export a git repository.
	 *
	 * @param string $source
	 * @param string $target
	 * @param string $prefix
	 * @param string $version
	 */
	public static function export($source, $target, $prefix, $version = 'HEAD') {
		$exportCmd = String(self::EXPORT_CMD_TMPL)->format([
			'src' => $source,
			'tgt' => $target,
			'pre' => $prefix,
			'ver' => $version
		]);
		passthru($exportCmd);
	}

	/**
	 * Return an array containing the status of any modified files in the
	 * repository.
	 *
	 * @param string $path Path of the repository for which to retrieve status
	 *   information
	 * @return array
	 */
	public static function getStatus($path) {
		if (!file_exists($path)) {
			throw new Exception(
				"Unable to get git status of $path. Path does not exist.");
		}

		if (!is_dir($path)) {
			throw new Exception(
				"Unable to get git status of $path. Path is not a directory.");
		}

		if (!self::isRepo($path)) {
			throw new Exception(
				"Unable to get git status of $path. Path is not a git repository.");
		}

		$origCwd = getcwd();
		chdir($path);

		$output = array();
		exec("git status --porcelain", $output);
		if (count($output) === 0) {
			return $output;
		}

		$files = array();
		foreach ($output AS $status) {
			$flag = substr($status, 0, 2);
			$file = trim(substr($status, 2));

			$idx = null;
			$fileInfo = null;
			switch ($flag) {

				case ' M': // Modified, not updated
				$idx = 'changed';
				$fileInfo = $file;
				break;

				case 'A ': // New file
				$idx = 'new';
				$fileInfo = $file;
				break;

				case 'D ': // Deleted file
				$idx = 'removed';
				$fileInfo = $file;
				break;

				case 'M ': // Modified, updated
				$idx = 'modified';
				$fileInfo = $file;
				break;

				case 'R ': // Renamed
				$idx = 'moved';
				$fileInfo = array_map(function ($a) {
					return trim($a);
				}, explode('->', $file));
				break;

				case '??': // Untracked
				$idx = 'untracked';
				$fileInfo = $file;
				break;

				default:
				assert("false /*Unrecognized git status flag $flag*/");
			}

			if ($idx !== null) {
				if (!isset($files[$idx])) {
					$files[$idx] = array();
				}
				$files[$idx][] = $fileInfo;
			}
		}

		chdir($origCwd);

		return $files;
	}

	/**
	 * Initialize a repository.
	 *
	 * @param string $path
	 */
	public static function initRepo($path) {
		return self::doInDir($path, self::INIT_CMD);
	}

	/**
	 * Initialize the submodules of the repo at the specified path.
	 *
	 * @param string $path
	 */
	public static function initSubmodules($path) {
		$oldDir = getcwd();
		chdir($path);
		passthru(self::INIT_SUBMODULES_CMD);
		chdir($oldDir);
	}

	/**
	 * Indicates whether or not the given path contains a Git repository.
	 *
	 * @param string $path
	 * @return boolean
	 */
	public static function isRepo($path) {
		$dotGitPath = $path . DIRECTORY_SEPARATOR . '.git';
		$dotGitExists = file_exists($dotGitPath);
		$dotGitIsDir = is_dir($dotGitPath);

		return $dotGitExists && $dotGitIsDir;
	}

	/**
	 * Move or create an existing lightweight tag.
	 *
	 * @param string $path
	 *   The path to the repository to tag
	 * @param string $tag
	 *   The name of the tag to create or update
	 * @param string $version
	 *   Commit spec for the version of the repository to tag. Defaults to HEAD
	 */
	public static function moveTag($repo, $tag, $version = 'HEAD') {
		$cmd = String(self::MOVE_TAG_CMD)->format([
			'tag' => $tag,
			'version' => $version
		]);
		return self::doInDir($repo, $cmd);
	}

	/**
	 * Tag a repository.
	 *
	 * @param string $path
	 *   The path to the repository
	 * @param string $tag
	 *   The name of the tag to create.
	 * @param string $msg
	 *   Commit message for annotated tag
	 * @param string $version
	 *   Commit spec for the version of the repository to tag. Defaults to HEAD
	 */
	public static function tag($repo, $tag, $msg = null, $version = 'HEAD') {
		if ($msg === null) {
			$msg = "Tagging $tag";
		}

		$cmd = String(self::TAG_CMD)->format([
			'tag' => $tag,
			'msg' => $msg,
			'version' => $version
		]);
		return self::doInDir($repo, $cmd);
	}

	/**
	 * Updates the given repository by performing a fetch from the specified
	 * remote and then a merge with the specified remote branch.
	 *
	 * @param string $path The path to the repository to update.
	 * @param string $remote The name of the remote to pull from. Default: origin
	 * @param string $branch The name of the branch to merge with. Default: master
	 */
	public static function update($path, $remote = 'origin', $branch = 'master') {
		if (!file_exists($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path does not exist.");
		}

		if (!is_dir($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path is not a directory.");
		}

		if (!self::isRepo($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path is not a git repository.");
		}

		$origCwd = getcwd();
		chdir($path);

		$fetchCmd = sprintf(self::FETCH_CMD_TMPL, $remote);
		passthru($fetchCmd);

		$mergeCmd = sprintf(self::MERGE_CMD_TMPL, $remote, $branch);
		passthru($mergeCmd);

		chdir($origCwd);
	}

	/**
	 * Updates the given repository's submodules.
	 *
	 * @param string $path The path to the repository to update.
	 */
	public static function updateExternals($path) {
		if (!file_exists($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path does not exist.");
		}

		if (!is_dir($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path is not a directory.");
		}

		if (!self::isRepo($path)) {
			throw new Exception(
				"Unable to update git repo $path. Path is not a git repository.");
		}

		$origCwd = getcwd();
		chdir($path);

		$updateCmd = sprintf(self::UPDATE_SUBMODULES_CMD);
		passthru($updateCmd);

		chdir($origCwd);
	}

	private static function doInDir($path, $cmds, array $output = []) {
		if (!is_array($cmds)) {
			$cmds = [ $cmds ];
		}

		$result = null;

		$oldDir = getcwd();
		chdir($path);
		foreach ($cmds as $cmd) {
			exec($cmd, $output, $result);

			if ($result) {
				break;
			}

		}
		chdir($oldDir);

		return !$result;
	}
}

} // End zpt\fn namespace

/*
 * =============================================================================
 * Shortcut functions
 * =============================================================================
 */

namespace { // Global namespace

	use zpt\fn\GitUtil;

/**
 * Clone a git repository.
 *
 * @param string $repo Path to the repository to clone.
 * @param string $path Path where the repository is to be cloned.
 */
function git_clone($repo, $path, $initSubModules = false) {
	GitUtil::cloneRepo($repo, $path, $initSubModules);
}

/**
 * Export a git repository.
 *
 * @param string $repo Path to the repository to export.
 * @param string $output Path to the base output directory.
 * @param string $prefix Subdirectory of output directory into which repository
 *	 will be exported
 */
function git_export($path, $output, $prefix = null, $version = 'HEAD') {
	if ($prefix === null) {
		$prefix = basename($output);
		$output = dirname($output);
	}
	GitUtil::export($path, $output, $prefix, $version);
}

/**
 * Initialize any submodules for the repo at the given path.
 *
 * @param string $path
 */
function git_init_submodules($path) {
	GitUtil::initSubmodules($path);
}

/**
 * Determine whether or not a given path is a git repository.
 *
 * @param string $path The path to the repository.
 * @return boolean
 */
function git_is_repo($path) {
	return GitUtil::isRepo($path);
}

/**
 * Determine whether or not a given git repository is clean.
 *
 * @param string $path The path to the repository.
 * @return boolean
 */
function git_is_clean($path) {
	$status = GitUtil::getStatus($path);
	return count($status) === 0;
}

/**
 * Create or move a lightweight tag
 *
 * @param string $repo
 * @param string $tag
 * @param string $version
 */
function git_move_tag($path, $tag, $version = 'HEAD') {
	GitUtil::moveTag($path, $tag, $version);
}

/**
 * Tag a git repository.
 *
 * @param string $repo
 * @param string $tag
 * @param string $version
 */
function git_tag($path, $tag, $version = 'HEAD') {
	return GitUtil::tag($path, $tag, null, $version);
}

/**
 * Update the repo at the given path to the lastest source in remote 'origin' on
 * branch 'master'.
 *
 * @param string $path The path to the repository to update.
 * @return boolean
 */
function git_update($path) {
	$status = GitUtil::update($path);
}

/**
 * Update the specified git repo's submodule's.
 *
 * @param string $path The path to the repository to update.
 * @return boolean
 */
function git_update_submodules($path) {
	$status = GitUtil::updateExternals($path);
}

} // End global namespace
