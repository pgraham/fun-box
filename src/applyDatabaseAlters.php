<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech FunBox and is licensed by the Copyright holder
 * under the 3-clause BSD License. The full text of the license can be found in
 * the LICENSE.txt file included in the root directory of this distribution or
 * at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @author Philip Graham <philip@zeptech.ca>
 */

/**
 * This function applies a series of database alters to a MySQL database.
 *
 *
 * @param string $user The username with which to connect to the database.
 * @param string $pass The password with which to connect to the database.
 * @param string $db The name of the database to which to apply alters.
 * @param integer $version The current version of the database.
 * @param string $alterDir Path to the directory where alters are found.
 */
function applyDatabaseAlters($user, $pass, $db, $version, $alterDir) {
  $user = escapeshellarg($user);
  $pass = escapeshellarg($pass);
  $db = escapeshellarg($db);

  $alterRe = '/^alter-0*([1-9][0-9]*)\.sql$/';
  $preAlterRe = '/^pre-alter-0*([1-9][0-9]*)\.php$/';
  $postAlterRe = '/^post-alter-0*([1-9][0-9]*)\.php$/';

  // Parse alters from alter path that are greater than the given version
  $dir = new DirectoryIterator($alterDir);

  $alters = array();
  foreach ($dir AS $file) {
    $fName = $file->getFilename();
    $matches = array();
    if (preg_match($alterRe, $fName, $matches)) {
      $alterVersion = (int) $matches[1];
      if ($alterVersion > $version) {
        if (!isset($alters[$alterVersion])) {
          $alters[$alterVersion] = array('version' => $alterVersion);
        }
        $alters[$alterVersion]['alter'] = $fName;
      }

    } else if (preg_match($preAlterRe, $fName, $matches)) {
      $alterVersion = (int) $matches[1];
      if ($alterVersion > $version) {
        if (!isset($alters[$alterVersion])) {
          $alters[$alterVersion] = array('version' => $alterVersion);
        }
        $alters[$alterVersion]['pre'] = $fName;
      }

    } else if (preg_match($postAlterRe, $fName, $matches)) {
      $alterVersion = (int) $matches[1];
      if ($alterVersion > $version) {
        if (!isset($alters[$alterVersion])) {
          $alters[$alterVersion] = array('version' => $alterVersion);
        }
        $alters[$alterVersion]['post'] = $fName;
      }
    }
  }
  usort($alters, function ($a, $b) {
    if ($a['version'] == $b['version']) {
      return 0;
    }

    return ($a['version'] < $b['version'])
      ? -1
      : 1;
  });
  echo "Applying alters: " . print_r($alters, true) . "\n";

  foreach ($alters AS $alter) {
    if (isset($alter['pre'])) {
      $prePath = "$alterDir/{$alter['pre']}";
      passthru("/usr/bin/php $prePath $user $pass $db");
    }

    if (isset($alter['alter'])) {
      $alterPath = "$alterDir/{$alter['alter']}";
      passthru("mysql -u$user -p$pass $db < $alterPath");
    }

    if (isset($alter['post'])) {
      $postPath = "$alterDir/{$alter['post']}";
      passthru("/usr/bin/php $postPath $user $pass $db");
    }
  }

}
