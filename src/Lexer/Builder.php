<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Builder\State;
use Phplrt\Lexer\Builder\MutableBuilderTrait;
use Phplrt\Lexer\Builder\MutableBuilderInterface;

/**
 * Class Builder
 */
class Builder implements MutableBuilderInterface
{
    use MutableBuilderTrait;

    /**
     * @var string
     */
    public const DEFAULT_STATE = 'default';

    /**
     * @var array|State[]
     */
    private $states = [];

    /**
     * @var string
     */
    private $state = self::DEFAULT_STATE;

    /**
     * @return \Phplrt\Lexer\Builder
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * @param string $name
     * @param \Closure $then
     * @return \Phplrt\Lexer\Builder|$this
     */
    public function state(string $name, \Closure $then): self
    {
        $state = $this->states[$name] ?? $this->states[$name] = $this->createState();

        $then($state);

        return $this;
    }

    /**
     * @param string $state
     * @return \Phplrt\Lexer\Builder|$this
     */
    public function withInitialState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getInitialState(): string
    {
        return $this->state;
    }

    /**
     * @return \Phplrt\Lexer\Builder\State
     */
    private function createState(): State
    {
        return new State($this);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasState(string $name): bool
    {
        return isset($this->states[$name]);
    }

    /**
     * @return array
     * @throws \Phplrt\Lexer\Builder\Exception\BuilderException
     */
    public function build(): array
    {
        $result = [];

        foreach ($this->compile() as $name => $payload) {
            $result[$name] = $payload;
        }

        return $result;
    }

    /**
     * @return \Phplrt\Contracts\Lexer\LexerInterface
     * @throws \Phplrt\Lexer\Builder\Exception\BuilderException
     */
    public function create(): LexerInterface
    {
        return new Lexer($this->build(), $this->getInitialState());
    }

    /**
     * @return iterable|array[]
     * @throws \Phplrt\Lexer\Builder\Exception\BuilderException
     */
    private function compile(): iterable
    {
        if (\count($this->states) === 0) {
            $this->states[self::DEFAULT_STATE] = $this->createState();
        }

        foreach ($this->states as $name => $state) {
            yield $name => $state->build();
        }
    }
}
