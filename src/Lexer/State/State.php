<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

use Phplrt\Lexer\Token\Token;
use Phplrt\Contracts\Io\Readable;
use Phplrt\Lexer\Driver\MarkedGroups;
use Phplrt\Lexer\Driver\DriverInterface;
use Phplrt\Lexer\Exception\InitializationException;
use Phplrt\Lexer\Exception\UnrecognizedTokenException;

/**
 * Class State
 */
class State implements StateInterface
{
    /**
     * @var string
     */
    public const DEFAULT_DRIVER = MarkedGroups::class;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var array|string[]
     */
    private $tokens = [];

    /**
     * @var array|string[]
     */
    private $jumps = [];

    /**
     * @var array|string[]
     */
    private $skip = [];

    /**
     * @var string
     */
    private $unknown;

    /**
     * State constructor.
     *
     * @param array $tokens
     * @param array $skip
     * @param string $driver
     * @throws InitializationException
     */
    public function __construct(array $tokens = [], array $skip = [], string $driver = self::DEFAULT_DRIVER)
    {
        $this->addMany($tokens);
        $this->skip(...\array_values($skip));

        $this->driver = $driver;

        $this->generateUnknownToken();
    }

    /**
     * @param array|string[] $tokens
     * @return \Phplrt\Lexer\State\StateInterface|$this
     */
    public function addMany(array $tokens): StateInterface
    {
        $this->tokens = \array_merge($this->tokens, $tokens);

        return $this;
    }

    /**
     * @param string ...$tokens
     * @return \Phplrt\Lexer\State\StateInterface|$this
     */
    public function skip(string ...$tokens): StateInterface
    {
        $this->skip = \array_merge($this->skip, $tokens);

        return $this;
    }

    /**
     * @return void
     * @throws \Phplrt\Lexer\Exception\InitializationException
     */
    private function generateUnknownToken(): void
    {
        try {
            $this->unknown = '_' . \hash('adler32', \random_bytes(8));
        } catch (\Exception $e) {
            throw new InitializationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $driver
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function using(string $driver): StateInterface
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param string $name
     * @param string $pattern
     * @param string|null $state
     * @return \Phplrt\Lexer\State\StateInterface|$this
     */
    public function add(string $name, string $pattern, string $state = null): StateInterface
    {
        $this->tokens[$name] = $pattern;

        if ($state !== null) {
            $this->jump($name, $state);
        }

        return $this;
    }

    /**
     * @param string $token
     * @param string $next
     * @return \Phplrt\Lexer\State\StateInterface|$this
     */
    public function jump(string $token, string $next): StateInterface
    {
        $this->jumps[$token] = $next;

        return $this;
    }

    /**
     * @param array $jumps
     * @return \Phplrt\Lexer\State\StateInterface|$this
     */
    public function jumpMany(array $jumps): StateInterface
    {
        $this->jumps = \array_merge($this->jumps, $jumps);

        return $this;
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|\Phplrt\Contracts\Lexer\TokenInterface[]
     * @throws \Phplrt\Lexer\Exception\UnrecognizedTokenException
     */
    public function exec(Readable $file, string $content, int $offset = 0): iterable
    {
        $driver = $this->create();

        foreach ($driver->exec($file, $content, $offset) as [$name, $value, $at]) {
            if (\in_array($name, $this->skip, true)) {
                continue;
            }

            if ($name === $this->unknown) {
                $exception = new UnrecognizedTokenException(\sprintf('Unrecognized token "%s"', $value));
                $exception->throwsIn($file, $at);

                throw $exception;
            }

            yield new Token($name, $value, $at);
        }

        return isset($name, $this->jumps[$name]) ? $this->jumps[$name] : null;
    }

    /**
     * @return \Phplrt\Lexer\Driver\DriverInterface
     */
    private function create(): DriverInterface
    {
        $class = $this->driver;

        $tokens = \array_merge($this->tokens, [
            $this->unknown => '.+',
        ]);

        return new $class($tokens, \array_keys($this->jumps));
    }
}
