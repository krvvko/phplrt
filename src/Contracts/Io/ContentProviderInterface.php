<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Contracts\Io;

use Phplrt\Contracts\Stream\ReadableStreamInterface;

/**
 * Interface ContentProviderInterface
 */
interface ContentProviderInterface
{
    /**
     * Returns the full contents of the source.
     *
     * @return string
     */
    public function getContents(): string;

    /**
     * @return ReadableStreamInterface
     */
    public function getStream(): ReadableStreamInterface;
}
