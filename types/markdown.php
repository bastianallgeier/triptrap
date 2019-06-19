<?php

return [
    'nodes' => [
        'blockquote' => function ($node, $attrs = []) {
            $output = [];

            // prefix any child
            foreach ($node['content'] as $index => $child) {
                $output[] = '> ' . $this->node($child, $node, $index) . PHP_EOL;
            }

            return implode($output) . PHP_EOL;
        },
        'bullet_list' => function ($node, $attrs = [], $parent) {
            return $this->nodes($node) . PHP_EOL;
        },
        'ordered_list' => function ($node, $attrs = [], $parent) {
            return $this->nodes($node) . PHP_EOL;
        },
        'code_block' => function ($node, $attrs = []) {
            return '```' . PHP_EOL . $this->nodes($node) . PHP_EOL . '```' . PHP_EOL . PHP_EOL;
        },
        'heading' => function ($node, $attrs = []) {
            return str_repeat('#', $attrs['level']) . ' ' . $this->nodes($node) . PHP_EOL . PHP_EOL;
        },
        'horizontal_rule' => function ($node, $attrs = []) {
            return '****' . PHP_EOL . PHP_EOL;
        },
        'image' => function ($node, $attrs = []) {
            return '(image: ' . $attrs['src'] . ')' . PHP_EOL . PHP_EOL;
        },
        'list_item' => function ($node, $attrs = [], $parent, $index) {
            if ($parent['type'] === 'ordered_list') {
                $prefix = ($index+1) . '.';
            } else {
                $prefix = '-';
            }

            return $prefix . ' ' . $this->nodes($node) . PHP_EOL;
        },
        'paragraph' => function ($node, $attrs = [], $parent) {
            if ($parent['type'] === 'doc') {
                return $this->nodes($node) . PHP_EOL . PHP_EOL;
            } else {
                return $this->nodes($node);
            }
        },
        'text' => function ($node, $attrs = []) {
            return $this->marks($node);
        }
    ],
    'marks' => [
        'code' => function ($text) {
            return '`' . $text . '`';
        },
        'bold' => function ($text) {
            return '**' . $text . '**';
        },
        'italic' => function ($text) {
            return '*' . $text . '*';
        },
        'link' => function ($text, $attrs) {
            if ($text === $attrs['href']) {
                return '<' . $attrs['href'] . '>';
            } else {
                return '[' . $text . '](' . $attrs['href'] . ')';
            }
        },
        'strike' => function ($text) {
            return '~~' . $text . '~~';
        },
        'underline' => function ($text) {
            return '__' . $text . '__';
        },
    ]
];
