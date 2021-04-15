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
 * Gravatar generator
 */
class Gravatar
{
    /**
     * Base URL.
     *
     * @param string
     */
    const BASE_URL = 'https://www.gravatar.com/avatar';

    /**
     * How to handle missing Gravatars.
     *
     * @param string
     */
    const DEFAULT = 'identicon';

    /**
     * Max rating to allow.
     *
     * @param string
     */
    const RATING = 'g';

    /**
     * Gravatar size in pixels.
     *
     * @param int
     */
    const SIZE = 40;

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }

    /**
     * Simple Gravatar URL generator
     *
     * @return string
     */
    public static function getAvatarUrl(string $email): string
    {
        return sprintf(
            "%s/%s?d=%s&r=%s&s=%d",
            static::BASE_URL,
            md5(strtolower(trim($email))),
            urlencode(static::DEFAULT),
            urlencode(static::RATING),
            static::SIZE
        );
    }
}
