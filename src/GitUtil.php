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

/**
 * Static interface for interacting with a git repository.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GitUtil {

	const CLONE_CMD_TMPL = "git clone -v %s %s";
	const EXPORT_CMD_TMPL = "git archive --remote=%s --prefix=%s/ master | tar -x -C %s";
	const FETCH_CMD_TMPL = "git fetch %s";
	const INIT_SUBMODULES_CMD = "git submodule update --init";
	const MERGE_CMD_TMPL = "git merge %s/%s";
	const UPDATE_SUBMODULES_CMD = 'git submodule update --merge';

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
			$oldDir = getcwd();
			chdir($path);
			passthru(self::INIT_SUBMODULES_CMD);
			chdir($oldDir);
		}
	}

	/**
	 * Export a git repository.
	 *
	 * @param string $path
	 * @param string $output
	 * @param string $prefix
	 */
	public static function export($path, $output, $prefix) {
		$exportCmd = sprintf(self::EXPORT_CMD_TMPL,
			$path,
			$prefix,
			$output);
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
}

/*
 * =============================================================================
 * Shortcut functions
 * =============================================================================
 */

/**
 * Clone a git repository.
 *
 * @param string $repo Path to the repository to clone.
 * @param string $path Path where the repository is to be cloned.
 */
function clone_git_repo($repo, $path, $initSubModules = false) {
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
function export_git_repo($path, $output, $prefix = null) {
	if ($prefix === null) {
		$prefix = basename($output);
		$output = dirname($output);
	}
	GitUtil::export($path, $output, $prefix);
}

/**
 * Determine whether or not a given path is a git repository.
 *
 * @param string $path The path to the repository.
 * @return boolean
 */
function is_git_repo($path) {
	return GitUtil::isRepo($path);
}

/**
 * Determine whether or not a given git repository is clean.
 *
 * @param string $path The path to the repository.
 * @return boolean
 */
function is_git_repo_clean($path) {
	$status = GitUtil::getStatus($path);
	return count($status) === 0;
}

/**
 * Update the repo at the given path to the lastest source in remote 'origin' on
 * branch 'master'.
 *
 * @param string $path The path to the repository to update.
 * @return boolean
 */
function update_git_repo($path) {
	$status = GitUtil::update($path);
}

/**
 * Update the specified git repo's submodule's.
 *
 * @param string $path The path to the repository to update.
 * @return boolean
 */
function update_git_repo_submodules($path) {
	$status = GitUtil::updateExternals($path);
}
