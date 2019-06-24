<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Compiler;

/**
 * Interface CompilerInterface
 */
interface CompilerInterface
{
    /**
     * @param \Closure $each
     * @param string $pattern
     * @return string
     */
    public function compile(\Closure $each, string $pattern): string;

    /**
     * @param string ...$flags
     * @return CompilerInterface|$this
     */
    public function withFlag(string ...$flags): self;

    /**
     * @param string ...$flags
     * @return CompilerInterface|$this
     */
    public function withoutFlag(string ...$flags): self;
}
