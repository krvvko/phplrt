<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

use Phplrt\Contracts\Io\Readable;

/**
 * Interface RuntimeInterface
 */
interface RuntimeInterface
{
    /**
     * @var int
     */
    public const TOKEN_NAME = 0x00;

    /**
     * @var int
     */
    public const TOKEN_VALUE = 0x01;

    /**
     * @var int
     */
    public const TOKEN_OFFSET = 0x02;

    /**
     * @param \Phplrt\Contracts\Io\Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|array[]
     */
    public function exec(Readable $file, string $content, int $offset = 0): iterable;
}
