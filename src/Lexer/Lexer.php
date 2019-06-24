<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Contracts\Io\Readable;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Lexer\Driver\DriverInterface;
use Phplrt\Lexer\Driver\StatelessDriverInterface;
use Phplrt\Lexer\Exception\InitializationException;
use Phplrt\Lexer\Exception\LexerException;
use Phplrt\Lexer\Token\EndOfInput;
use Phplrt\Lexer\Token\Token;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Lexer
 */
class Lexer implements LexerInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    public const DEFAULT_STATE = 'default';

    /**
     * @var array|DriverInterface[]
     */
    private $drivers = [];

    /**
     * @var string|null
     */
    private $state;

    /**
     * Lexer constructor.
     *
     * @param iterable|DriverInterface[] $drivers
     * @param LoggerInterface|null $logger
     */
    public function __construct(iterable $drivers = [], LoggerInterface $logger = null)
    {
        foreach ($drivers as $name => $driver) {
            $this->append($driver, $name);
        }

        if ($logger !== null) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param DriverInterface $driver
     * @param string|int|null $name
     * @return DriverInterface|StatelessDriverInterface
     */
    public function append(DriverInterface $driver, $name = null): DriverInterface
    {
        \assert(\is_int($name) || \is_string($name) || $name === null);

        if (\count($this->drivers) === 0) {
            $name = static::DEFAULT_STATE;
        }

        if ($name === null) {
            return $this->drivers[] = $driver;
        }

        return $this->drivers[$name] = $driver;
    }

    /**
     * @param DriverInterface $driver
     * @param string|int|null $name
     * @return DriverInterface|StatelessDriverInterface
     */
    public function prepend(DriverInterface $driver, $name = null): DriverInterface
    {
        \assert(\is_int($name) || \is_string($name) || $name === null);

        if (\count($this->drivers) === 0) {
            $name = static::DEFAULT_STATE;
        }

        if ($name === null) {
            \array_unshift($this->drivers, $driver);
        } else {
            $this->drivers = \array_merge([$name => $driver], $this->drivers);
        }

        return $driver;
    }

    /**
     * @param Readable $input
     * @return \Traversable|TokenInterface[]
     * @throws LexerException
     */
    public function lex(Readable $input): \Traversable
    {
        [$start, $offset] = [\microtime(true), 0];

        $state = $this->getState($this->state);

        $content = $input->getContents();
        $length  = \strlen($content);


        while ($offset < $length) {
            $driver = $this->drivers[$state];

            if ($this->logger) {
                $this->debug('Use state driver %s at offset %d', [\get_class($driver), $offset], $start);
            }

            foreach ($driver->lex($input, $content, $offset) as [$name, $value, $offset]) {
                if ($this->logger) {
                    $this->debug('Token %s:%s ("%s") at offset %d', [
                        $state,
                        $name,
                        $value,
                        $offset,
                    ], $start);
                }

                yield $token = new Token($name, $value, $offset);
            }

            if (isset($token)) {
                $offset += $token->getBytes();

                $next = $driver->then($token->getName());

                if ($next) {
                    if ($this->logger) {
                        $this->debug('Change state "%s" => "%s" at offset %d', [
                            $state,
                            $next,
                            $offset,
                        ], $start);
                    }

                    $state = $this->getState($next);
                }
            }
        }

        $offset += isset($token) ? $token->getBytes() : 0;

        if ($this->logger) {
            $this->debug('Completed at offset %d', [$offset], $start);
        }

        yield new EndOfInput($offset);
    }

    /**
     * @param string|null $state
     * @return string
     * @throws InitializationException
     */
    private function getState(?string $state): string
    {
        if ($state === null) {
            /** @noinspection LoopWhichDoesNotLoopInspection */
            foreach ($this->drivers as $name => $driver) {
                return $name;
            }

            throw new InitializationException('Unable to run lexical analysis because lexer is not initialized', 1);
        }

        if (isset($this->drivers[$state])) {
            return $state;
        }

        throw new InitializationException(\sprintf('Unrecognized state "%s"', $state), 2);
    }

    /**
     * @param string $message
     * @param string|int|float|bool ...$args
     * @param float $time
     * @return void
     */
    private function debug(string $message, array $args, float $time): void
    {
        if ($this->logger !== null) {
            $message = \sprintf('Lexer<%s#%s>: %s', \get_class($this), $this->getId(), \vsprintf($message, $args));

            $this->logger->debug($message, [
                'time'   => \number_format(\microtime(true) - $time, 3) . 'µs',
                'memory' => \number_format(\memory_get_usage(false) / 1000 / 1000, 2) . 'Mb',
            ]);
        }
    }

    /**
     * @return int|string
     */
    private function getId()
    {
        return \function_exists('\spl_object_id') ? \spl_object_id($this) : \spl_object_hash($this);
    }
}
