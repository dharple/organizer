<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support;

/**
 * Generates Gravatar URLs for user avatars.
 */
class Gravatar
{
    /**
     * The Gravatar base URL.
     */
    public const BASE_URL = 'https://www.gravatar.com/avatar';

    /**
     * The default avatar type.
     */
    public const DEFAULT = 'identicon';

    /**
     * The content rating filter.
     */
    public const RATING = 'g';

    /**
     * The avatar size in pixels.
     */
    public const SIZE = 40;

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }

    /**
     * Returns the Gravatar URL for the given email address.
     */
    public static function getAvatarUrl(string $email): string
    {
        return sprintf(
            '%s/%s?d=%s&r=%s&s=%d',
            static::BASE_URL,
            hash('sha256', strtolower(trim($email))),
            urlencode(static::DEFAULT),
            urlencode(static::RATING),
            static::SIZE
        );
    }
}
