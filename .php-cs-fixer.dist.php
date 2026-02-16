<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('public/bundles')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'modernize_types_casting' => true,
        'no_superfluous_phpdoc_tags' => true,
        'phpdoc_summary' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
