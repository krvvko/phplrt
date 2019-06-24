<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

use Phplrt\Contracts\Io\Readable;

/**
 * Interface StateInterface
 */
interface DriverInterface
{
    /**
     * @param Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable
     */
    public function lex(Readable $file, string $content, int $offset = 0): iterable;

    /**
     * @param string $token
     * @return string|int|null
     */
    public function then(string $token);
}
