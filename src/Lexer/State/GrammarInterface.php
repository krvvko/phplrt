<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

/**
 * Interface GrammarInterface
 */
interface GrammarInterface
{
    /**
     * @return array|string[]
     */
    public function tokens(): array;

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function append(string $token, string $pattern, bool $skip = false): self;

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function prepend(string $token, string $pattern, bool $skip = false): self;

    /**
     * @return array|string[]
     */
    public function skips(): array;

    /**
     * @param string ...$token
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function skip(string ...$token): self;

    /**
     * @return array|string[]
     */
    public function jumps(): array;

    /**
     * @param string $token
     * @param string $state
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function jump(string $token, string $state): self;
}
