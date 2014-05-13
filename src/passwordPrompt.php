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
 */

/**
 * Simple function that will prompt the user for a password.
 *
 * @param string $prompt
 *          The prompt for the user.  Default = 'password:'
 * @return string The inputted password
 * @author Philip Graham <philip@zeptech.ca>
 */
function passwordPrompt($prompt = 'password: ') {
  $command = "/usr/bin/env bash -c 'read -s -p \""
    . addslashes($prompt)
    . "\" mypassword && echo \$mypassword'";
  $password = trim(shell_exec($command));
  echo "\n";
  return $password;
}
