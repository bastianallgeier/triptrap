<?php

class ProseMirror
{

    public static $types = [];

    protected $definitions;
    protected $document;
    protected $type;

    public function __construct($document, $type)
    {
        $this->document    = $document;
        $this->type        = $type;
        $this->definitions = static::$types[$this->type];
    }

    public function mark($text, $mark, $node)
    {
        if ($renderer = ($this->definitions['marks'][$mark['type']] ?? null)) {
            return $renderer->call($this, $text, $mark['attrs'] ?? [], $node);
        }

        return null;
    }

    public function marks($node)
    {
        if (empty($node['marks']) === false) {
            $output = $node['text'];

            foreach ($node['marks'] as $mark) {
                $output = $this->mark($output, $mark, $node);
            }
        } else {
            $output = $node['text'];
        }

        return $output;
    }

    public function node($node, $parent, $index)
    {
        if ($renderer = ($this->definitions['nodes'][$node['type']] ?? null)) {
            return $renderer->call($this, $node, $node['attrs'] ?? [], $parent, $index);
        }

        return null;
    }

    public function nodes($parent)
    {
        $output = [];

        foreach ($parent['content'] ?? [] as $index => $node) {
            $output[] = $this->node($node, $parent, $index);
        }

        return implode($output);
    }

    public function render()
    {
        return $this->nodes($this->document);
    }

}
