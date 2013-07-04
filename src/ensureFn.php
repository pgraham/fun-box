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

/**
 * Simple framework for including the functions in this directory.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class EnsureFns {

  public static $fns = array();

  public static function ensureFn($fn) {
    if (in_array($fn, self::$fns)) {
      return;
    }

    $fnPath = __DIR__ . "/$fn.php";
    if (file_exists($fnPath)) {
      require_once $fnPath;
      self::$fns[] = $fn;
    } else {
      throw new \Exception("Unable to load function $fn, it does not exist.");
    }
  }
}

} // End zpt\fn namespace

namespace {
  function ensureFn($fn) {
    \zpt\fn\EnsureFns::ensureFn($fn);
  }
}
