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
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Lexer\Driver\DriverInterface;
use Phplrt\Lexer\Driver\NamedGroups;
use Phplrt\Lexer\Exception\InitializationException;
use Phplrt\Lexer\Exception\LexerException;
use Phplrt\Lexer\Token\EndOfInput;
use Phplrt\Lexer\Token\Token;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Lexer
 */
class Lexer implements StatelessLexerInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    public const DEFAULT_STATE = 'default';

    /**
     * @var string
     */
    public const DEFAULT_DRIVER = NamedGroups::class;

    /**
     * @var array|DriverInterface[]
     */
    private $drivers = [];

    /**
     * @var string
     */
    private $state = self::DEFAULT_STATE;

    /**
     * Lexer constructor.
     *
     * @param DriverInterface|array $driver
     * @param LoggerInterface|null $logger
     */
    public function __construct($driver, LoggerInterface $logger = null)
    {
        $this->add(self::DEFAULT_STATE, $this->bootDriver($driver));

        if ($logger !== null) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param array|DriverInterface $driver
     * @return DriverInterface
     */
    private function bootDriver($driver): DriverInterface
    {
        if (\is_array($driver)) {
            return new NamedGroups($driver);
        }

        return $driver;
    }

    /**
     * @param string $name
     * @param DriverInterface $driver
     * @return DriverInterface
     */
    public function add(string $name, DriverInterface $driver): DriverInterface
    {
        return $this->drivers[$name] = $driver;
    }

    /**
     * @param Readable $input
     * @return \Traversable|TokenInterface[]
     * @throws LexerException
     */
    public function lex(Readable $input): \Traversable
    {
        [$start, $offset] = [\microtime(true), 0];

        $state = $this->state($this->state);

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

                yield $offset => $token = new Token($name, $value, $offset);
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

                    $state = $this->state($next);
                }
            }
        }

        $offset += isset($token) ? $token->getBytes() : 0;

        if ($this->logger) {
            $this->debug('Completed at offset %d', [$offset], $start);
        }

        yield $offset => new EndOfInput($offset);
    }

    /**
     * @param string|null $state
     * @return string
     * @throws InitializationException
     */
    private function state(?string $state): string
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
