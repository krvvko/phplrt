<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

use Phplrt\Contracts\Io\Readable;
use Phplrt\Lexer\Driver\DriverInterface;
use Phplrt\Lexer\Driver\MarkedGroups;
use Phplrt\Lexer\Exception\InitializationException;
use Phplrt\Lexer\Exception\UnrecognizedTokenException;
use Phplrt\Lexer\Token\Token;

/**
 * Class State
 */
class State extends Grammar implements StateInterface
{
    /**
     * @var string
     */
    public const DEFAULT_DRIVER = MarkedGroups::class;

    /**
     * @var string
     */
    private $driver = self::DEFAULT_DRIVER;

    /**
     * @var string
     */
    private $unknown;

    /**
     * @var \Phplrt\Lexer\State\GrammarInterface
     */
    private $parent;

    /**
     * State constructor.
     *
     * @param \Phplrt\Lexer\State\GrammarInterface $parent
     * @throws \Phplrt\Lexer\Exception\InitializationException
     */
    public function __construct(GrammarInterface $parent)
    {
        $this->parent = $parent;
        $this->generateUnknownToken();

        parent::__construct();
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
     * @param \Phplrt\Contracts\Io\Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|\Phplrt\Contracts\Lexer\TokenInterface[]
     * @throws \Phplrt\Lexer\Exception\UnrecognizedTokenException
     */
    public function exec(Readable $file, string $content, int $offset = 0): iterable
    {
        $driver = $this->create();

        $skips = \array_merge($this->parent->skips(), $this->skip);
        $jumps = \array_merge($this->parent->jumps(), $this->jumps);

        foreach ($driver->exec($file, $content, $offset) as [$name, $value, $at]) {
            if (\in_array($name, $skips, true)) {
                continue;
            }

            if ($name === $this->unknown) {
                $exception = new UnrecognizedTokenException(\sprintf('Unrecognized token "%s"', $value));
                $exception->throwsIn($file, $at);

                throw $exception;
            }

            yield new Token($name, $value, $at);
        }

        return isset($name, $jumps[$name]) ? $jumps[$name] : null;
    }

    /**
     * @param array $flags
     * @return \Phplrt\Lexer\Driver\DriverInterface
     */
    private function create(array $flags = []): DriverInterface
    {
        $class = $this->driver;

        $tokens = \array_merge($this->parent->tokens(), $this->tokens, [
            $this->unknown => '.+',
        ]);

        $breaks = \array_keys(
            \array_merge($this->parent->jumps(), $this->jumps)
        );

        return new $class($tokens, $breaks, $flags);
    }
}
