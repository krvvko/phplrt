<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

/**
 * Class StatelessDriver
 */
abstract class StatelessDriver extends Driver implements StatelessDriverInterface
{
    /**
     * @param string $token
     * @param string $pattern
     * @return StatelessDriverInterface|$this
     */
    public function append(string $token, string $pattern): StatelessDriverInterface
    {
        $this->reset();
        $this->tokens[$token] = $pattern;

        return $this;
    }

    /**
     * @param string $token
     * @param string $pattern
     * @return StatelessDriverInterface|$this
     */
    public function prepend(string $token, string $pattern): StatelessDriverInterface
    {
        $this->reset();
        $this->tokens = \array_merge([$token => $pattern], $this->tokens);

        return $this;
    }

    /**
     * @param string ...$tokens
     * @return StatelessDriverInterface|$this
     */
    public function skip(string ...$tokens): StatelessDriverInterface
    {
        $this->reset();
        $this->skip = \array_merge($this->skip, $tokens);

        return $this;
    }

    /**
     * @param string $token
     * @param string|int $state
     * @return StatelessDriverInterface|$this
     */
    public function jump(string $token, $state): StatelessDriverInterface
    {
        \assert(\is_string($state) || \is_int($state));

        $this->reset();
        $this->jumps[$token] = $state;

        return $this;
    }

    /**
     * @return void
     */
    protected function reset(): void
    {
        // Do nothing
    }
}
