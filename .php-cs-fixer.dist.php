<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->append([__DIR__ . '/public/index.php'])
    ->exclude(['var', 'vendor'])
    ->name('*.php')
    ->notName('*.blade.php') // au cas où
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRules([
        // --- Sets principaux ---
        '@Symfony' => true,
        '@Symfony:risky' => true,
        // Modernisations liées à PHP 8.x (si erreur de set, commente la ligne concernée)
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHP81Migration' => true,
        '@PHP82Migration' => true,
        '@PHP83Migration' => true,

        // --- Règles “qualité de vie” & style ---
        'array_syntax' => ['syntax' => 'short'],                // [] au lieu de array()
        'ordered_imports' => ['sort_algorithm' => 'alpha'],     // trie les use
        'no_unused_imports' => true,                            // supprime les use inutilisés
        'single_import_per_statement' => true,
        'global_namespace_import' => [                          // import auto des functions/constants globales
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'class_attributes_separation' => [                      // sauts de ligne cohérents entre props/methods
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
                'method' => 'one',
            ],
        ],
        'declare_strict_types' => true,                         // ajoute declare(strict_types=1);
        'native_function_invocation' => [                       // prefixe les fonctions natives avec \
            'include' => ['@all'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'nullable_type_declaration_for_default_null_value' => true, // ?Type $x = null (PHP >=8)
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_to_comment' => false,                           // garde les phpdoc utiles
        'phpdoc_line_span' => ['property' => 'single', 'method' => 'single', 'const' => 'single'],

        // --- Modernisations sûres (PHP ≥ 8) ---
        'modernize_strpos' => true,                             // str_contains au lieu de strpos===false
        'modernize_types_casting' => true,                      // (int) etc. -> casts modernes quand possible
        'use_arrow_functions' => true,                          // fn() => ... si applicable
        'self_static_accessor' => true,                         // remplace self:: par static:: si utile
        'no_unset_on_property' => true,                         // $this->x = null; au lieu de unset()

        // --- Divers utiles ---
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'yoda_style' => false,                                  // pas de yoda
        'simplified_null_return' => true,                       // return; au lieu de return null; (si void)
        'no_useless_return' => true,
        'no_useless_sprintf' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'for', 'foreach', 'while', 'do', 'switch'],
        ],
    ])
    ->setFinder($finder);
