<?php

/*
 * scans and format app directory to psr12 standard
 * */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app') // scans app folder
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => true,
    ])
    ->setFinder($finder);
