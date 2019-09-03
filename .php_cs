<?php

$rules = [
    '@PSR2' => true,
    'trailing_comma_in_multiline_array' => true,
    'ordered_imports' => true,
    'no_unused_imports' => true,
    'blank_line_before_statement' => true,
    'object_operator_without_whitespace' => true,
    'return_type_declaration' => ['space_before' => 'none'],
    'function_declaration' => false,
];

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(false)
    ->setRules($rules)
    ->setCacheFile(__DIR__ . '/.php_cs.cache')
    ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__)
    );
