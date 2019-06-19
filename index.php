<?php

require_once __DIR__ . '/ProseMirror.php';

ProseMirror::$types = [
    'html'      => require_once __DIR__ . '/types/html.php',
    'markdown'  => require_once __DIR__ . '/types/markdown.php',
    'kirbytext' => require_once __DIR__ . '/types/kirbytext.php'
];

Kirby::plugin('getkirby/editor', [
    'fields' => [
        'editor' => [
            'mixins' => ['filepicker', 'upload'],
            'props' => [
                /**
                 * Sets the options for the files picker
                 */
                'files' => function ($files = []) {
                    if (is_string($files) === true) {
                        return ['query' => $files];
                    }

                    if (is_array($files) === false) {
                        $files = [];
                    }

                    return $files;
                },
                'format' => function (string $format = 'html') {
                    return $format;
                }
            ],
            'computed' => [
                'value' => function () {
                    if (is_array($this->value) === true) {
                        return $this->value;
                    }

                    switch ($this->format) {
                        case 'markdown':
                            return kirbytext($this->value);
                        case 'yaml':
                            return Yaml::decode($this->value);
                        case 'json':
                            return Json::decode($this->value);
                        default:
                            return $this->value;
                    }
                }
            ],
            'save' => function ($value) {
                switch ($this->format) {
                    case 'html':
                        return (new ProseMirror($value, 'html'))->render();
                    case 'markdown':
                        return (new ProseMirror($value, 'kirbytext'))->render();
                    case 'json':
                        return Json::encode($value);
                    default:
                        return $value;
                }
            },
            'api' => function () {
                return [
                    [
                        'pattern' => 'files',
                        'action' => function () {
                            return $this->field()->filepicker($this->field()->files());
                        }
                    ],
                    [
                        'pattern' => 'upload',
                        'action' => function () {
                            return $this->field()->upload($this, $this->field()->uploads(), function ($file) {
                                return [
                                    'filename' => $file->filename(),
                                    'dragText' => $file->dragText(),
                                ];
                            });
                        }
                    ]
                ];
            },
        ]
    ]
]);
