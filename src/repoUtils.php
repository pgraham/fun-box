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
require_once __DIR__ . '/GitUtil.php';
require_once __DIR__ . '/SvnUtil.php';

/**
 * This file defines a set of functions for working with VC repositories.
 * Currently supported repos are Git and SVN.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */

/**
 * Determine if the given path is a recogined WC.
 *
 * @param string $path
 * @return boolean
 */
function is_repo($path) {
	return git_is_repo($path) || is_svn_repo($path);
}

/**
 * Determine if the specified repo has any uncommitted changes.
 *
 * @param string $path Path to the repo WC.
 * @return boolean
 */
function repo_is_clean($path) {
	if (git_is_repo($path)) {
		return git_is_repo_clean($path);
	} else if (is_svn_repo($path)) {
		return is_svn_repo_clean($path);
	} else {
		// Return true here because if it is not a recognized repo then there are no
		// uncommitted changes.  To handle this case as a failure use in conjunction
		// with is_repo($path)
		return true;
	}
}

/**
 * Export the specified repo to the specified location.
 *
 * @param string $src
 * @param string $dest
 * @param array $opts - Options specific to the type of repository being
 *   exported.
 * @return boolean
 */
function repo_export($src, $dest, array $opts = null) {
	if (git_is_repo($src)) {
		$prefix = isset($opts['prefix'])
			? $opts['prefix']
			: null;
		git_export($src, $dest, $prefix);
		return true;
	} else if (is_svn_repo($src)) {
		export_svn_repo($src, $dest);
	} else {
		return false;
	}
}
