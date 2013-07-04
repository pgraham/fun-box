<?php
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
