<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Io\File;
use Phplrt\Lexer\Token\Token;
use Phplrt\Lexer\Token\Unknown;
use Phplrt\Contracts\Io\Readable;
use Phplrt\Lexer\Token\EndOfInput;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Exception\UnrecognizedTokenException;

/**
 * Class Lexer
 */
class Lexer implements LexerInterface
{
    /**
     * @var int
     */
    private const PREG_FLAGS = \PREG_SET_ORDER;

    /**
     * @var mixed
     */
    private const GROUP_VALUE = 0x00;

    /**
     * @var mixed
     */
    private const GROUP_NAME = 'MARK';

    /**
     * @var array
     */
    private $states;

    /**
     * @var string
     */
    private $state;

    /**
     * Lexer constructor.
     *
     * @param array $states
     * @param string $state
     */
    public function __construct(array $states, string $state)
    {
        $this->states = $states;
        $this->state = $state;
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable|resource|\SplFileInfo|string $input
     * @return \Traversable
     * @throws \Phplrt\Lexer\Exception\LexerException
     */
    public function lex($input): \Traversable
    {
        $input = File::new($input);
        $content = $input->getContents();

        yield from $this->run($input, $this->state, $content);

        yield new EndOfInput(\strlen($content));
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $input
     * @param string $state
     * @param string $content
     * @param int $offset
     * @return \Traversable|\Phplrt\Contracts\Lexer\TokenInterface[]
     * @throws \Phplrt\Lexer\Exception\LexerException
     * @throws \Phplrt\Lexer\Exception\UnrecognizedTokenException
     */
    private function run(Readable $input, string $state, string $content, int $offset = 0): \Traversable
    {
        $generator = $this->exec($input, $state, $content, $offset);

        while ($generator->valid()) {
            [$name, $value, $offset] = $generator->current();

            if (! \in_array($name, $this->states[$state][1], true)) {
                yield $token = new Token($name, $value, $offset);
            }

            $generator->next();
        }

        if (isset($token)) {
            $offset += $token->getBytes();
        }

        if ($next = (string)$generator->getReturn()) {
            yield from $this->run($input, $next, $content, $offset);
        }
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $input
     * @param string $state
     * @param string $content
     * @param int $offset
     * @return \Generator|string
     * @throws \Phplrt\Lexer\Exception\UnrecognizedTokenException
     */
    private function exec(Readable $input, string $state, string $content, int $offset = 0): \Generator
    {
        [$pattern, , $jumps] = $this->states[$state];

        \preg_match_all($pattern, $content, $matches, self::PREG_FLAGS, $offset);

        foreach ($matches as [self::GROUP_NAME => $name, self::GROUP_VALUE => $value]) {
            if ($name === Unknown::T_NAME) {
                throw $this->unknownToken($input, $value, $offset);
            }

            yield [$name, $value, $offset];

            $offset += \strlen($value);

            if (isset($jumps[$name])) {
                return $jumps[$name];
            }
        }
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $input
     * @param string $value
     * @param int $offset
     * @return \Phplrt\Lexer\Exception\UnrecognizedTokenException
     */
    private function unknownToken(Readable $input, string $value, int $offset): UnrecognizedTokenException
    {
        $exception = new UnrecognizedTokenException(new Unknown($value, $offset));
        $exception->throwsIn($input, $offset);

        return $exception;
    }
}
