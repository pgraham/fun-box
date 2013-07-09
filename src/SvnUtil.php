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
 * Static interface for interacting with a subversion repository.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class SvnUtil {

  /** Regular expression for parsing a single line of output from svn status. */
  const SVN_STATUS_RE = '/^([ ACDIMRX?!~])([ CM])([ L])([ +])([ SX])([ K])([ C])(.+)$/';

  /** Template command for exporting a repository. */
  const EXPORT_CMD_TMPL = "svn export %s %s";

  /** Template command for updating a repository. */
  const UPDATE_CMD = 'svn up';

  /**
   * Export a subversion repository.
   *
   * @param string $path
   * @param string $output
   */
  public static function export($path, $output) {
    $exportCmd = sprintf(self::EXPORT_CMD_TMPL,
      $path,
      $output);
    passthru($exportCmd);
  }

  /**
   * Return that status of any modified files in the repository.
   *
   * @param string $path
   * @return array
   */
  public static function getStatus($path) {
    if (!file_exists($path)) {
      throw new Exception(
        "Unable to get svn status of $path. Path does not exist.");
    }

    if (!is_dir($path)) {
      throw new Exception(
        "Unable to get svn status of $path. Path is not a directory.");
    }

    if (!self::isRepo($path)) {
      throw new Exception(
        "Unable to get svn status of $path. Path is not a svn repository.");
    }

    $output = array();
    exec("svn status $path", $output);
    if (count($output) === 0) {
      return $output;
    }

    $files = array();
    foreach ($output AS $status) {
      $flags = array();
      if (preg_match(self::SVN_STATUS_RE, $status, $flags)) {
        $flag = $flags[1];
        $file = trim($flags[8]);

        $idx = null;
        $fileInfo = null;
        switch ($flag) {
          case ' ': // No modifications
          case 'I': // Ignored
          case 'X': // Unversioned directory created by externals
          break;

          case 'A': // Added
          $idx = 'new';
          $fileInfo = $file;
          break;

          case 'C': // Conflicted
          $idx = 'conflicted';
          $fileInfo = $file;
          break;

          case 'D': // Deleted
          $idx = 'removed';
          $fileInfo = $file;
          break;

          case 'M': // Modified
          $idx = 'modified';
          $fileInfo = $file;
          break;

          case 'R': // Replaced
          $idx = 'replaced';
          $fileInfo = $file;
          break;

          case '?': // Untracked
          $idx = 'untracked';
          $fileInfo = $file;
          break;

          case '!': // Missing
          $idx = 'missing';
          $fileInfo = $file;
          break;

          case '~': // Obstructed
          $idx = 'obstructed';
          $fileInfo = $file;
          break;

          default:
          assert("false /*Unrecognized svn status flag $flag*/");
        }

        if ($idx !== null) {
          if (!isset($files[$idx])) {
            $files[$idx] = array();
          }
          $files[$idx][] = $fileInfo;
        }
      }
    }

    return $files;
  }

  /**
   * Indicate whether or not the given path is a subversion repository.
   *
   * @param string $path
   * @return boolean
   */
  public static function isRepo($path) {
    $dotSvnPath = $path . DIRECTORY_SEPARATOR . '.svn';
    $dotSvnExists = file_exists($dotSvnPath);
    $dotSvnIsDir = is_dir($dotSvnPath);

    return $dotSvnExists && $dotSvnIsDir;
  }

  /**
   * Update the repo at the given path.
   *
   * @param string $path
   */
  public static function update($path) {
    $origCwd = getcwd();
    chdir($path);

    passthru(self::UPDATE_CMD);

    chdir($origCwd);
  }

}

/*
 * =============================================================================
 * Shortcut functions
 * =============================================================================
 */

/**
 * Export a subversion repository.
 *
 * @param string $path
 * @param string $output
 */
function export_svn_repo($path, $output) {
  SvnUtil::export($path, $output);
}

/**
 * Determine whether or not the given path is a subversion repository.
 *
 * @param string $path
 * @return boolean
 */
function is_svn_repo($path) {
  return SvnUtil::isRepo($path);
}

/**
 * Determine whether or not a given subversion repository is clean.
 *
 * @param string $path
 * @return boolean
 */
function is_svn_repo_clean($path) {
  $status = SvnUtil::getStatus($path);
  return count($status) === 0;
}

/**
 * Update the repo at the given path.
 *
 * @param string $path
 */
function update_svn_repo($path) {
  SvnUtil::update($path);
}
