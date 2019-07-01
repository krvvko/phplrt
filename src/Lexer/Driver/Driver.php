<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

use Phplrt\Lexer\Exception\LexerException;
use Phplrt\Lexer\Exception\RuntimeException;

/**
 * Class Driver
 */
abstract class Driver implements DriverInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array|string[]
     */
    private $breaks;

    /**
     * NamedGroups constructor.
     *
     * @param array|string[] $tokens
     * @param array|string[] $breaks
     * @param array|string[] $flags
     */
    public function __construct(array $tokens, array $breaks = [], array $flags = [])
    {
        $this->breaks  = $breaks;
        $this->pattern = $this->buildPattern($tokens, $flags);
    }

    /**
     * @param array $tokens
     * @param array $flags
     * @return string
     */
    private function buildPattern(array $tokens, array $flags = []): string
    {
        $pcre = new Preg($tokens, $flags);

        $callback = \Closure::fromCallable([$this, 'token']);

        return $pcre->compile($callback, $this->pattern());
    }

    /**
     * @return string
     */
    abstract protected function pattern(): string;

    /**
     * @param \Closure $expr
     * @return mixed
     * @throws \Phplrt\Lexer\Exception\LexerException
     * @throws \Phplrt\Lexer\Exception\RuntimeException
     */
    protected function wrap(\Closure $expr)
    {
        try {
            \error_clear_last();

            $result = $expr();

            Preg::assertCode(\preg_last_error());
            Preg::assertLastError(\error_get_last());
        } catch (LexerException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @param string $token
     * @return bool
     */
    protected function breaks(string $token): bool
    {
        return \in_array($token, $this->breaks, true);
    }

    /**
     * @param string $name
     * @param string $pattern
     * @return string
     */
    abstract protected function token(string $name, string $pattern): string;
}
