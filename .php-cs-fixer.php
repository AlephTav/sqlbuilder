<?php

declare(strict_types=1);

return
    (new PhpCsFixer\Config())
        ->setFinder(
            PhpCsFixer\Finder::create()
                ->in([
                    __DIR__ . '/src',
                    __DIR__ . '/tests',
                ])
                ->append([
                    __FILE__,
                ])
        )
        ->setRules([
            '@PSR12' => true,
            '@PSR12:risky' => true,
            '@PHP74Migration' => true,
            '@PHP74Migration:risky' => true,
            '@PHPUnit84Migration:risky' => true,
        ]);
