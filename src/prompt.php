<?php
/*
 * Copyright (c) 2014, Philip Graham
 * All rights reserved.
 *
 * This file is part of Zeptech FunBox. For the full copyright and license
 * information please view the LICENSE file that was distributed with this
 * source code.
 */

/**
 * Prompt the user for a single line of input.
 *
 * @param string $prompt
 * $return string
 */
function prompt($prompt) {
	echo $prompt;
	$line = fgets(STDIN);
	return $line;
}
