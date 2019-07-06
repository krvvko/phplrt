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
 * Trait MutableBuilderTrait
 */
trait MutableBuilderTrait
{
    use BuilderTrait;

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function append(string $token, string $pattern, bool $skip = false): MutableBuilderInterface
    {
        $this->tokens[$token] = $pattern;

        return $skip ? $this->skip($token) : $this->keep($token);
    }

    /**
     * @param iterable $tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function add(iterable $tokens): MutableBuilderInterface
    {
        foreach ($tokens as $name => $pattern) {
            $this->append($name, $pattern);
        }

        return $this;
    }

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function prepend(string $token, string $pattern, bool $skip = false): MutableBuilderInterface
    {
        $this->tokens = \array_merge([$token => $pattern], $this->tokens);

        return $skip ? $this->skip($token) : $this->keep($token);
    }

    /**
     * @param string ...$tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function skip(string ...$tokens): MutableBuilderInterface
    {
        foreach ($tokens as $token) {
            if (! \in_array($token, $this->skip, true)) {
                $this->skip[] = $token;
            }
        }

        return $this;
    }

    /**
     * @param string ...$tokens
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function keep(string ...$tokens): MutableBuilderInterface
    {
        $this->skip = \array_filter($this->skip, static function (string $token) use ($tokens): bool {
            return ! \in_array($token, $tokens, true);
        });

        return $this;
    }

    /**
     * @param string $token
     * @param string $state
     * @return \Phplrt\Lexer\Builder\MutableBuilderInterface|$this
     */
    public function jump(string $token, string $state): MutableBuilderInterface
    {
        $this->jumps[$token] = $state;

        return $this;
    }
}
