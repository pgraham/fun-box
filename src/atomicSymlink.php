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
 * This function atomically updates a symlink.
 *
 * @param $link The path of the link
 * @param $target The target path of the link
 */
function atomicSymlink($link, $target) {
  $tmp = dirname($link) . PATH_SEPARATOR . "tmp-" . uniqid();

  // ln -s new current_tmp && mv -Tf current_tmp current
  $cmd = sprintf("ln -s %s %s && mv -Tf %s %s",
    $target,
    $tmp,
    $tmp,
    $link);
  exec($cmd);
}
