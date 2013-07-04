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
 * Simple function that will output a countdown.  This is useful for giving the
 * user a chance to cancel a pending operation that can have undesired, hard to
 * reverse changes.
 *
 * @param waitTime {integer}
 *            Number of seconds to count down before returning
 * @param message {string} default: 'Commencing'
 *            The message will echoed with the suffix 'in (Ctrl-C to cancel)'
 *            appended.
 * 
 * @author Philip Graham <philip@zeptech.ca>
 */
function announceWait($waitTime, $message = 'Commencing') {
  echo "\n";

  $message .= ' in';
  for ($i = 0; $i < strlen($message); $i++) {
    echo substr($message, $i, 1);
  }
  echo "    (Ctrl-C to cancel)\n";
  sleep(1);

  for ($i = $waitTime; $i > 0; $i--) {
      echo $i.'... ';
      if ($i == 1) {
        echo "\n";
      }
      sleep(1);
  }
  echo "\n";
}
