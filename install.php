<?php

$redir = ' 1>&2';
$fixer = $baseDir = __DIR__.'/php-cs-fixer.pre-commit.php';
$git = getcwd().'/.git/hooks';
$precommit = $git.'/pre-commit';

if (!file_exists($git))
{
    passthru('echo Git is not initialized'.$redir);
    die;
}

if (file_exists($precommit))
{
    // pre commit already in place, skipping
    die;
}

passthru('ln -s '.escapeshellarg($fixer).' '.escapeshellarg($precommit).$redir);