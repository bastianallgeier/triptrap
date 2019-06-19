<?php

return [
    'nodes' => [
        'blockquote' => function ($node, $attrs = []) {
            return '<blockquote>' . $this->nodes($node) . '</blockquote>';
        },
        'bullet_list' => function ($node, $attrs = []) {
            return '<ul>' . $this->nodes($node) . '</ul>';
        },
        'ordered_list' => function ($node, $attrs = []) {
            return '<ol>' . $this->nodes($node) . '</ol>';
        },
        'code_block' => function ($node, $attrs = []) {
            return '<pre><code>' . $this->nodes($node) . '</code></pre>';
        },
        'heading' => function ($node, $attrs = []) {
            return '<h' . $attrs['level'] . '>' . $this->nodes($node) . '</h' . $attrs['level'] . '>';
        },
        'horizontal_rule' => function ($node, $attrs = []) {
            return '<hr>';
        },
        'list_item' => function ($node, $attrs = []) {
            return '<li>' . $this->nodes($node) . '</li>';
        },
        'paragraph' => function ($node, $attrs = []) {
            return '<p>' . $this->nodes($node) . '</p>';
        },
        'text' => function ($node, $attrs = []) {
            return $this->marks($node);
        }
    ],
    'marks' => [
        'code' => function ($text) {
            return '<code>' . $text . '</code>';
        },
        'bold' => function ($text) {
            return '<strong>' . $text . '</strong>';
        },
        'italic' => function ($text) {
            return '<i>' . $text . '</i>';
        },
        'link' => function ($text, $attrs) {
            return '<a href="' . $attrs['href'] . '">' . $text . '</a>';
        },
        'strike' => function ($text) {
            return '<del>' . $text . '</del>';
        },
        'underline' => function ($text) {
            return '<u>' . $text . '</u>';
        },
    ]
];
