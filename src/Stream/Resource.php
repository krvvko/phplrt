<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Stream;

/**
 * Class Resource
 */
final class Resource
{
    /**
     * @var string
     */
    public const RESOURCE_NAME = 'resource (closed)';

    /**
     * @var string
     */
    public const RESOURCE_NAME_LTE_PHP71 = 'unknown type';

    /**
     * @param resource|mixed $resource
     * @return bool
     */
    public static function match($resource): bool
    {
        switch (true) {
            case \is_resource($resource):
                return true;

            // PHP 7.2 or greater
            case \version_compare(\PHP_VERSION, '7.2') >= 1:
                return \gettype($resource) === self::RESOURCE_NAME;

            // PHP 7.1 or lower
            default:
                return \gettype($resource) === self::RESOURCE_NAME_LTE_PHP71;
        }
    }
}
