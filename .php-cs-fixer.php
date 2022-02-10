<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.DIRECTORY_SEPARATOR.'app')
    ->in(__DIR__.DIRECTORY_SEPARATOR.'database')
    ->in(__DIR__.DIRECTORY_SEPARATOR.'tests')
;

/** @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/rules/index.rst */
$rules = [
    // Array Notation
    'array_syntax' => ['syntax' => 'short'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_whitespace_before_comma_in_array' => true,
    'normalize_index_brace' => true,
    'trim_array_spaces' => true,
    'whitespace_after_comma_in_array' => true,
    // Casing
    'constant_case' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_constant_casing' => true,
    'magic_method_casing' => true,
    'native_function_casing' => true,
    'native_function_type_declaration_casing' => true,
    // Cast Notation
    'cast_spaces' => true,
    'lowercase_cast' => true,
    // Class Notation
    'class_definition' => true,
    'no_blank_lines_after_class_opening' => true,
    'single_class_element_per_statement' => true,
    // Comment
    'no_empty_comment' => true,
    // Control Structure
    'elseif' => true,
    'trailing_comma_in_multiline' => true,
    // Function Notation
    'function_declaration' => true,
    'function_typehint_space' => true,
    'method_argument_space' => true,
    'no_spaces_after_function_name' => true,
    'return_type_declaration' => true,
    // Import
    'fully_qualified_strict_types' => true,
    'no_leading_import_slash' => true,
    'no_unused_imports' => true,
    'single_line_after_imports' => true,
    // Language construct
    'single_space_after_construct' => true,
    // Namespace Notation
    'blank_line_after_namespace' => true,
    'clean_namespace' => true,
    'no_leading_namespace_whitespace' => true,
    'single_blank_line_before_namespace' => true,
    // Operator
    'binary_operator_spaces' => true,
    'concat_space' => ['spacing' => 'none'],
    'increment_style' => true,
    'new_with_braces' => true,
    'object_operator_without_whitespace' => true,
    'operator_linebreak' => true,
    'ternary_operator_spaces' => true,
    'unary_operator_spaces' => true,
    // PHP Tag
    'blank_line_after_opening_tag' => true,
    'full_opening_tag' => true,
    'linebreak_after_opening_tag' => true,
    'no_closing_tag' => true,
    // PHPUnit
    'php_unit_method_casing' => true,
    // PHPDoc
    'align_multiline_comment' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_phpdoc' => true,
    'phpdoc_align' => true,
    'phpdoc_annotation_without_dot' => true,
    'phpdoc_indent' => true,
    'phpdoc_line_span' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_order' => true,
    'phpdoc_return_self_reference' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_tag_type' => ['tags' => ['inheritdoc' => 'inline']],
    'phpdoc_to_comment' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
    'phpdoc_var_annotation_correct_order' => true,
    // Semicolon
    'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
    'no_empty_statement' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'space_after_semicolon' => true,
    // String Notation
    'single_quote' => true,
    // Whitespace
    'blank_line_before_statement' => true,
    'compact_nullable_typehint' => true,
    'no_extra_blank_lines' => true,
    'no_spaces_around_offset' => true,
    'no_spaces_inside_parenthesis' => true,
];

$config = new PhpCsFixer\Config();
return $config->setFinder($finder)
    ->setRules($rules)
    ->setUsingCache(true)
;
