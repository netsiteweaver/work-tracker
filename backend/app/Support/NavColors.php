<?php

namespace App\Support;

/**
 * The palette a top menu button can be given. A button is either filled with
 * its colour or outlined in it.
 *
 * The generated class names never appear in a file Tailwind scans, so the list
 * below is mirrored by the `safelist` in tailwind.config.js. Add a colour here
 * and it has to be added there too, or it will not survive `npm run build`.
 */
class NavColors
{
    /** @var list<string> */
    public const COLORS = [
        'slate',
        'gray',
        'zinc',
        'neutral',
        'stone',
        'red',
        'orange',
        'amber',
        'yellow',
        'lime',
        'green',
        'emerald',
        'teal',
        'cyan',
        'sky',
        'blue',
        'indigo',
        'violet',
        'purple',
        'fuchsia',
        'pink',
        'rose',
    ];

    public const DEFAULT = 'blue';

    /**
     * Tailwind classes for a button of this colour.
     */
    public static function classes(string $color, bool $outline = false): string
    {
        if (! in_array($color, self::COLORS, true)) {
            $color = self::DEFAULT;
        }

        if ($outline) {
            return "border-2 border-{$color}-600 text-{$color}-700 hover:bg-{$color}-600 hover:text-white "
                ."dark:border-{$color}-400 dark:text-{$color}-300 dark:hover:bg-{$color}-500 dark:hover:text-white";
        }

        return "bg-{$color}-600 text-white hover:bg-{$color}-700";
    }

    /**
     * The colour and style of an existing raw class string, when it reads as
     * one of ours. Returns null for anything hand-written we cannot place.
     *
     * @return array{0: string, 1: bool}|null
     */
    public static function parse(?string $classes): ?array
    {
        if (! $classes) {
            return null;
        }

        $outline = str_contains($classes, 'border-2');
        $pattern = $outline ? '/border-([a-z]+)-\d{2,3}/' : '/bg-([a-z]+)-\d{2,3}/';

        if (preg_match($pattern, $classes, $match) && in_array($match[1], self::COLORS, true)) {
            return [$match[1], $outline];
        }

        return null;
    }
}
