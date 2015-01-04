#!/usr/bin/env php
<?php

/**
 * .git/hooks/pre-commit
 *
 * This pre-commit hooks will check for PHP error (lint), and make sure the code
 * is PSR compliant.
 *
 * Dependency: PHP-CS-Fixer (https://github.com/fabpot/PHP-CS-Fixer)
 *
 * @author  tacone http://github.com/tacone
 * @since   Dec 31 2014
 *
 * @author  Mardix  http://github.com/mardix
 * @since   Sept 4 2012
 *
 */

/**
 * Check php-cs-fixer is available and throw a warning if it's not
 */

exec('which php-cs-fixer', $unused, $return);

$phpCsFixerIsAvailable = !$return;

if (!$phpCsFixerIsAvailable) {
    $redir = ' 1>&2';
    passthru('echo Warning: php-cs-fixer is not installed or reachable by PATH.' . $redir);
    passthru('echo ""' . $redir);
    passthru('echo for installation instructions, please see:' . $redir);
    passthru('echo \'http://cs.sensiolabs.org/#installation\'' . $redir);
    passthru('echo ' . $redir);
    passthru('echo this commit will now continue without psr2 fixing.' . $redir);
    passthru('echo ' . $redir);
}

$whitelist = [
    'php' => '*.php',
    'twig' => '*.twig',
];

$blacklist = [
    'blade' => '*.blade.php',
];

/**
 * collect all files which have been added, copied or
 * modified and store them in an array called output
 */
exec('git diff --cached --name-status --diff-filter=ACM', $output);

foreach ($output as $file) {
    $fileName = trim(substr($file, 1));
    /**
     * Only PHP file
     */
//    if (pathinfo($fileName, PATHINFO_EXTENSION) == "php") {
    if (needsFixing($fileName)) {
        /**
         * Check for error
         */
        $lint_output = array();
        exec("php -l " . escapeshellarg($fileName), $lint_output, $return);

        if ($return == 0) {
            /**
             * PHP-CS-Fixer && add it back
             */
            if (!$phpCsFixerIsAvailable) {
                continue;
            }
            exec("php-cs-fixer fix {$fileName}; git add {$fileName}");
        } else {
            echo implode("\n", $lint_output), "\n";
            exit(1);
        }
    }
}

exit(0);

// ---------- functions

/**
 * Determine if a given string matches a given pattern.
 * (credit for this function goes to Laravel authors)
 *
 * @param  string $pattern
 * @param  string $value
 * @return bool
 */

function is($pattern, $value)
{
    if ($pattern == $value) {
        return true;
    }

    $pattern = preg_quote($pattern, '#');

    // Asterisks are translated into zero-or-more regular expression wildcards
    // to make it convenient to check if the strings starts with the given
    // pattern such as "library/*", making any string check convenient.
    $pattern = str_replace('\*', '.*', $pattern) . '\z';

    return (bool) preg_match('#^' . $pattern . '#', $value);
}

function needsFixing($file)
{
    return whitelisted($file) && !blacklisted($file);
}

function whitelisted($file)
{
    global $whitelist;
    foreach ($whitelist as $pattern) {
        if (is($pattern, $file)) {
            return true;
        }
    }

    return false;
}

function blacklisted($file)
{
    global $blacklist;
    foreach ($blacklist as $pattern) {
        if (is($pattern, $file)) {
            return true;
        }
    }

    return false;
}
