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

/*
 * This function copies a MySQL database to another database.  If the target
 * database exists it will first be dropped.
 *
 * @param string $source The name of the database to copy
 * @param string $target The name of the target database
 * @param string $user   A database user with sufficient privileges to dump the
 *   source database and to drop and create the target database.
 * @param string $pwd   The password for the specified database user.
 */
function copyDb($source, $target, $user, $pwd) {
  $mysqlDrop = sprintf('mysql -u%s --password=%s -e "DROP DATABASE IF EXISTS %s"',
    $user,
    escapeshellarg($pwd),
    $target);
  $failure = false;
  passthru($mysqlDrop, $failure);
  if ($failure) {
    echo "Unable to drop existing database\n";
    return 1;
  }

  $mysqlCreate = sprintf('mysql -u%s --password=%s -e' .
      ' "CREATE DATABASE %s CHARACTER SET %s"',
    $user,
    escapeshellarg($pwd),
    $target,
    "'utf8'");
  $failure = false;
  passthru($mysqlCreate, $failure);
  if ($failure) {
    echo "Unable to create target database\n";
    return 1;
  }

  $mysqlCopy = sprintf('mysqldump -u%1$s --password=%2$s %3$s|'
      . ' mysql -u%1$s --password=%2$s %4$s',
    $user,
    escapeshellarg($pwd),
    $source,
    $target);
  $failure = false;
  passthru($mysqlCopy, $failure);
  if ($failure) {
    echo "Unable to copy database\n";
    return 1;
  }
}
