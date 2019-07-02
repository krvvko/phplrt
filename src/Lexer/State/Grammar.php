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
 * Class Grammar
 */
class Grammar implements GrammarInterface
{
    /**
     * @var array|string[]
     */
    protected $tokens;

    /**
     * @var array|string[]
     */
    protected $jumps;

    /**
     * @var array|string[]
     */
    protected $skip;

    /**
     * Grammar constructor.
     *
     * @param array|string[] $tokens
     * @param array|string[] $skip
     * @param array|string[] $jumps
     */
    public function __construct(array $tokens = [], array $skip = [], array $jumps = [])
    {
        $this->tokens = $tokens;
        $this->skip = $skip;
        $this->jumps = $jumps;
    }

    /**
     * @return array|string[]
     */
    public function tokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return array|string[]
     */
    public function skips(): array
    {
        return $this->skip;
    }

    /**
     * @return array|string[]
     */
    public function jumps(): array
    {
        return $this->jumps;
    }

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function append(string $token, string $pattern, bool $skip = false): GrammarInterface
    {
        $this->tokens[$token] = $pattern;

        if ($skip) {
            $this->skip($token);
        }

        return $this;
    }

    /**
     * @param string $token
     * @param string $pattern
     * @param bool $skip
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function prepend(string $token, string $pattern, bool $skip = false): GrammarInterface
    {
        $this->tokens = \array_merge([$token => $pattern], $this->tokens);

        if ($skip) {
            $this->skip($token);
        }

        return $this;
    }

    /**
     * @param string ...$token
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function skip(string ...$token): GrammarInterface
    {
        $this->skip = \array_merge($this->skip, $token);

        return $this;
    }

    /**
     * @param string $token
     * @param string $state
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function jump(string $token, string $state): GrammarInterface
    {
        $this->jumps[$token] = $state;

        return $this;
    }
}
