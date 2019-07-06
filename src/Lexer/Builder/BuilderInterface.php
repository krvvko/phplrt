<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Builder;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * @return iterable|string[]
     */
    public function tokens(): iterable;

    /**
     * @return iterable|string[]
     */
    public function skips(): iterable;

    /**
     * @return iterable|string[]
     */
    public function jumps(): iterable;

    /**
     * @return array|array[]
     */
    public function build(): array;
}
