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
	*  -  `argv`: non option arguments first element will be the invoked command
	*  -  `argc`: Number of arguments in `argv`
	*
	* @param array $argv
	*   The command line arguments array to parse. The element at array 0 is 
	*   expected to be the command.
	* @param array $expectedOpts
	*   List of supported options. Any not specified on `$argv` will have a value 
	*   set to false rather than being unset in the opt array.
	*/
	public static function parse($argv, array $expectedOpts = []) {
		$opts = array();
		$args = array(array_shift($argv));
		foreach ($argv as $arg) {
			if (preg_match(self::LONG_OPT_RE, $arg, $matches)) {
				$optName = $matches[1];
				if (isset($matches[2])) {
					$optVal = $matches[2];
				} else {
					$optVal = true;
				}
				$opts[$optName] = $optVal;
			} else {
				$args[] = $arg;
			}
		}

		foreach ($expectedOpts as $opt) {
			if (!isset($opts[$opt])) {
				$opts[$opt] = false;
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
	function getcmdln($argv, $expectedOpts = []) {
		return zpt\fn\GetCmdln::parse($argv, $expectedOpts);
	}
}
