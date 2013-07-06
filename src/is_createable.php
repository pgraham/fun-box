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

/**
 * Function which determines if it is possible to create the specified file or
 * directory by checking if the closest existing ancestor path component is
 * writeable
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
function is_createable($path) {
	$dir = dirname($path);

	if (!file_exists($dir)) {
		$dir = dirname($dir);
	}

	return is_writeable($dir);
}
