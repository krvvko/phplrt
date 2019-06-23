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
 * Interface WritableStreamInterface
 */
interface WritableStreamInterface extends SeekableStreamInterface
{
    /**
     * Writes data line to the stream.
     *
     * @param string $string The string that is to be written.
     * @param string $separator Line separator
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function writeLine($string, string $separator = \PHP_EOL): int;

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string): int;
}
