<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database/factories',
        __DIR__.'/database/seeders',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR1' => true,
        '@PSR12' => true,
        'declare_strict_types' => false,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder);
