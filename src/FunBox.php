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
 * Initialization class for FunBox.
 */
class FunBox {

	/**
	 * Initialization function. This function does nothing. A side effect of
	 * calling this function is that if it is the first time called and the FunBox
	 * class is autoloaded then the ensureFn definition will be loaded.
	 *
	 * **NOTE** If FunBox is installed VIA composer there is no need to invoke
	 * zpt\fn\FunBox::init() as ensureFn.php will be added to the composer
	 * autoloader.
	 */
	public static function init() {}
}

require_once __DIR__ . '/ensureFn.php';
