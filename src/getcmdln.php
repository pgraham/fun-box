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

use \StdClass;

/**
 * Static class wrapper for the getcmdln function.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class GetCmdln {

	const LONG_OPT_RE = '/--([^=]+)(?:=(\S+))?/';

	/**
	* This function retrieves command line options and arguments.	Return format is 
	* a stdClass instance with three properties:
	*
	*  -  `opt` : array in the format of getopt() but arguments don't need to be 
	*             specified
	*  -  `argv`: $argv - `opt`
	*  -  `argc`: Number of arguments in `argv`
	*
	* @param string $options `options` argument to getopt()
	* @param array $longopts `longopts` argument to getopt()
	*/
	public static function parse($argv, $options, $longopts = array()) {
		array_shift($argv);

		$opts = array();
		$args = array();
		foreach ($argv as $arg) {
			if (preg_match(self::LONG_OPT_RE, $arg, $matches)) {
				$opts[$matches[1]] = $matches[2];
			} else {
				$args[] = $arg;
			}
		}

		$cmdln = new StdClass();
		$cmdln->opt = $opts;
		$cmdln->argv = $args;
		$cmdln->argc = count($args);
		return $cmdln;
	}
}

} // End zpt\fn namespace

namespace {

	/**
	 * Global namespace alias for GetCmdln::parse. Only supports long options.
	 */
	function getcmdln($argv, $longopts = array()) {
		return zpt\fn\GetCmdln::parse($argv, '', $longopts);
	}
}
