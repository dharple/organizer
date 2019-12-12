<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Serializer\Normalizer;

/**
 * How is this not available already?
 */
trait ArrayFlattenTrait
{
    /**
     * Converts a multi-dimensional array into a flat one.
     *
     * [ 'blah' => [ 'dedah' => 'lala' ], 'hi' => 'there' ]
     *
     * becomes:
     *
     * [ 'blah.dedah' => 'lala', 'hi' => 'there' ]
     *
     * @param array  $in        The array to flatten.
     * @param string $separator The character to use.
     */
    protected function flatten(array $in, string $separator = '.'): array
    {
        $out = [];

        foreach ($in as $field => $value) {
            if (is_array($value)) {
                $flatValue = $this->flatten($value, $separator);
                foreach ($flatValue as $innerField => $innerValue) {
                    $out[$field . $separator . $innerField] = $innerValue;
                }
            } else {
                $out[$field] = $value;
            }
        }

        return $out;
    }
}
