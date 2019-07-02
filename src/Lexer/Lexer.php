<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Lexer\State\Factory;
use Phplrt\Lexer\State\Grammar;
use Phplrt\Lexer\State\GrammarInterface;
use Phplrt\Lexer\State\State;
use Phplrt\Contracts\Io\Readable;
use Phplrt\Lexer\State\StateInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Exception\RuntimeException;
use Phplrt\Lexer\Exception\InitializationException;

/**
 * Class Lexer
 */
class Lexer implements LexerInterface, Stateless
{
    /**
     * @var array|\Phplrt\Lexer\State\StateInterface[]
     */
    private $states;

    /**
     * @var \Phplrt\Lexer\State\GrammarInterface
     */
    private $global;

    /**
     * @var string
     */
    private $state = 'default';

    /**
     * Lexer constructor.
     *
     * @param array $tokens
     * @param array $skip
     */
    public function __construct(array $tokens = [], array $skip = [])
    {
        $this->global = new Grammar($tokens, $skip);
    }

    /**
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function global(): GrammarInterface
    {
        return $this->global;
    }

    /**
     * @param string $name
     * @return \Phplrt\Lexer\State\StateInterface
     * @throws \Phplrt\Lexer\Exception\InitializationException
     */
    public function state(string $name = self::DEFAULT_STATE): StateInterface
    {
        return $this->states[$name] ?? $this->states[$name] = new State($this->global);
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $input
     * @return \Traversable|\Phplrt\Contracts\Lexer\TokenInterface[]
     * @throws \Phplrt\Lexer\Exception\InitializationException
     * @throws \Phplrt\Lexer\Exception\RuntimeException
     */
    public function lex(Readable $input): \Traversable
    {
        [$offset, $content, $state] = [0, $input->getContents(), $this->state];

        $runtime = \count($this->states) === 0
            ? new State($this->global)
            : $this->current($state);

        yield from $last = $runtime->exec($input, $content, $offset);
    }

    /**
     * @param string|null $state
     * @return \Phplrt\Lexer\State\StateInterface
     * @throws \Phplrt\Lexer\Exception\InitializationException
     * @throws \Phplrt\Lexer\Exception\RuntimeException
     */
    private function current(string $state): StateInterface
    {
        if (\count($this->states) === 0) {
            throw new InitializationException('No tokens were defined');
        }

        if (! isset($this->states[$state])) {
            throw new RuntimeException('Unrecognized state "' . $state . '"');
        }

        return $this->states[$state];
    }
}
