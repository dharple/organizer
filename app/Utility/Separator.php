<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utility;

/**
 * Generates a separator for command line usage.
 */
class Separator
{
    public const ALIGN_CENTER = 'center';
    public const ALIGN_LEFT   = 'left';
    public const ALIGN_RIGHT  = 'right';
    public const ALIGN_WIDTH  = 5;
    public const WIDTH        = 78;

    /**
     * Generates a separator
     */
    public static function generate(
        ?string $title = null,
        ?int $width = null,
        ?string $align = null
    ) {
        $width ??= static::WIDTH;
        $align ??= static::ALIGN_CENTER;

        if (empty($title)) {
            return str_repeat('-', $width);
        }

        $title = sprintf('[ %s ]', $title);
        $titleLength = strlen($title);

        if ($titleLength < $width) {
            switch ($align) {
                case 'left':
                    $lineLengthLeft = static::ALIGN_WIDTH;
                    $lineLengthRight = $width - $titleLength - $lineLengthLeft;
                    break;

                case 'right':
                    $lineLengthRight = static::ALIGN_WIDTH;
                    $lineLengthLeft = $width - $titleLength - $lineLengthRight;
                    break;

                default:
                    $lineLengthLeft = ceil(($width - $titleLength) / 2);
                    $lineLengthRight = $width - $titleLength - $lineLengthLeft;
                    break;
            }

            return sprintf(
                '%s%s%s',
                str_repeat('-', $lineLengthLeft),
                $title,
                str_repeat('-', $lineLengthRight)
            );
        }

        return $title;
    }
}
