<?php

$markdown  = require 'markdown.php';
$kirbytext = array_replace_recursive($markdown, [
    'marks' => [
        'link' => function ($text, $attrs) {

            if ($text === $attrs['href']) {
                return '<' . $attrs['href'] . '>';
            } else {
                return '(link: ' . $attrs['href'] . ' text: ' . $text . ')';
            }

        },
    ]
]);

return $kirbytext;
