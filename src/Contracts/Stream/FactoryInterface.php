<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Contracts\Stream;

/**
 * Interface FactoryInterface
 */
interface FactoryInterface
{
    /**
     * @param string $content
     * @return SeekableStreamInterface
     */
    public static function fromContent(string $content): SeekableStreamInterface;

    /**
     * @param string $pathname
     * @param array $options
     * @return SeekableStreamInterface
     */
    public static function fromPathname(string $pathname, array $options = []): SeekableStreamInterface;

    /**
     * @param resource $resource
     * @return StreamInterface
     */
    public static function fromResource($resource): StreamInterface;
}
