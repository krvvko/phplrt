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
 * Interface MutableBuilderInterface
 */
interface MutableBuilderInterface extends BuilderInterface
{
    /**
     * @param iterable $tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function add(iterable $tokens): self;

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function append(string $token, string $pattern, bool $skip = false): self;

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function prepend(string $token, string $pattern, bool $skip = false): self;

    /**
     * @param string ...$tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function skip(string ...$tokens): self;

    /**
     * @param string ...$tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function keep(string ...$tokens): self;

    /**
     * @param string $token
     * @param string $state
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function jump(string $token, string $state): self;
}
