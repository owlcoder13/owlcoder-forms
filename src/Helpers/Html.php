<?php

namespace Owlcoder\Forms\Helpers;

class Html
{
    public static $selfTag = [
        ['img', 'meta', 'input'],
    ];

    public static function escape($value, $doubleQuotes = true)
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function tag($tagName, $content, $attributes = [])
    {
        $closeTag = in_array(strtolower($tagName), static::$selfTag);

        $html = "<$tagName" . static::buildAttributes($attributes) . ($closeTag ? '/>' : '>');

        if ( ! $closeTag) {
            $html .= "{$content}</{$tagName}>";
        }

        return $html;
    }

    public static function buildAttributes($attributes)
    {
        if (count($attributes) == 0) {
            return '';
        }

        $out = [];

        foreach ($attributes as $key => $value) {
            $out[] = $key . '="' . static::escape($value) . '"';
        }

        return ' ' . join(' ', $out);
    }
}
