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
 * This function creates a symlink in the given directory with the given name to
 * the given target.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
function symlinkInDir($dir, $target, $link) {
  $curCwd = getcwd();

  chdir($dir);
  symlink($target, $link);
  chdir($curCwd);
}
