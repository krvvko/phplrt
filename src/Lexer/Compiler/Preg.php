<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Compiler;

use Phplrt\Lexer\Exception\InitializationException;
use Phplrt\Lexer\Exception\RegularExpressionException;

/**
 * Class Preg
 */
class Preg implements CompilerInterface
{
    /**
     * @var string
     */
    public const ERROR_PARSING = 'The error occurs while compiling PCRE';

    /**
     * @var string
     */
    public const ERROR_INTERNAL = 'The given PCRE contain a syntax error';

    /**
     * @var string
     */
    public const ERROR_BACKTRACK_LIMIT = 'Backtrack limit was exhausted';

    /**
     * @var string
     */
    public const ERROR_RECURSION_LIMIT = 'Recursion limit was exhausted';

    /**
     * @var string
     */
    public const ERROR_BAD_UTF8 = 'The offset didn\'t correspond to the begin of a valid UTF-8 code point';

    /**
     * @var string
     */
    public const ERROR_BAD_UTF8_OFFSET = 'Malformed UTF-8 data';

    /**
     * @var string
     */
    public const ERROR_UNEXPECTED = 'Unexpected PCRE error (Code %d)';

    /**
     * @var string
     */
    public const FLAG_UNICODE = 'u';

    /**
     * @var string
     */
    public const FLAG_DOT_ALL = 's';

    /**
     * @var string
     */
    public const FLAG_CASE_INSENSITIVE = 'i';

    /**
     * @var string
     */
    public const FLAG_MULTILINE = 'm';

    /**
     * Regex delimiter
     *
     * @var string
     */
    private const DELIMITER = '/';

    /**
     * Regex expression delimiter
     *
     * @var string
     */
    private const CHUNK_DELIMITER = '|';

    /**
     * @var string
     */
    private const FLAG_ANALYZED = 'S';

    /**
     * @var array
     */
    private $tokens;

    /**
     * @var array|string[]
     */
    private $flags = [
        self::FLAG_ANALYZED,
        self::FLAG_UNICODE,
    ];

    /**
     * Preg constructor.
     *
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param string ...$flags
     * @return CompilerInterface|$this
     */
    public function withFlag(string ...$flags): CompilerInterface
    {
        $this->flags = \array_merge($this->flags, $flags);

        return $this;
    }

    /**
     * @param string ...$flags
     * @return CompilerInterface|$this
     */
    public function withoutFlag(string ...$flags): CompilerInterface
    {
        $filter = static function (string $flag) use ($flags) {
            return ! \in_array($flag, $flags, true);
        };

        $this->flags = \array_filter($this->flags, $filter);

        return $this;
    }

    /**
     * @param \Closure $each
     * @param string $pattern
     * @return string
     */
    public function compile(\Closure $each, string $pattern = '%s'): string
    {
        $pattern = \sprintf($pattern, $this->compilePattern($each));
        $flags   = $this->compileFlags();

        return self::DELIMITER . $pattern . self::DELIMITER . $flags;
    }

    /**
     * @param \Closure $each
     * @return string
     */
    private function compilePattern(\Closure $each): string
    {
        $result = [];

        foreach ($this->tokens as $name => $pattern) {
            $result[] = $each($this->escapeName($name), $this->escapePattern($pattern));
        }

        return \implode(self::CHUNK_DELIMITER, $result);
    }

    /**
     * @param string $token
     * @return string
     */
    protected function escapeName(string $token): string
    {
        return \preg_quote($token, static::DELIMITER);
    }

    /**
     * @param string $pattern
     * @return string
     */
    protected function escapePattern(string $pattern): string
    {
        return \addcslashes($pattern, static::DELIMITER);
    }

    /**
     * @return string
     */
    private function compileFlags(): string
    {
        return \implode('', \array_unique($this->flags));
    }

    /**
     * @return bool
     * @throws RegularExpressionException
     */
    public static function assertFromGlobals(): bool
    {
        return static::assert(\preg_last_error(), \error_get_last());
    }

    /**
     * Checks the result for correctness.
     *
     * <code>
     *  Validator::assert(\ERROR_last(), \error_get_last());
     * </code>
     *
     * @param int $code PCRE error code
     * @param array|null $last
     * @return bool
     * @throws InitializationException
     * @throws RegularExpressionException
     */
    public static function assert(int $code, ?array $last): bool
    {
        static::assertCode($code);
        static::assertLastError($last);

        return true;
    }

    /**
     * @param array|null $error
     * @return void
     * @throws InitializationException
     */
    public static function assertLastError(?array $error): void
    {
        if ($error !== null) {
            $code    = $error['type'] ?? 0;
            $message = $error['message'] ?? \sprintf(self::ERROR_UNEXPECTED, $code);

            throw new InitializationException($message, $code);
        }
    }

    /**
     * @param int $code
     * @return void
     * @throws RegularExpressionException
     */
    public static function assertCode(int $code): void
    {
        if ($code !== \PREG_NO_ERROR) {
            throw new RegularExpressionException(self::getErrorMessage($code), $code);
        }
    }

    /**
     * @param int $code
     * @return string
     */
    private static function getErrorMessage(int $code): string
    {
        switch ($code) {
            case \PREG_INTERNAL_ERROR:
                return self::ERROR_INTERNAL;

            case \PREG_BACKTRACK_LIMIT_ERROR:
                return self::ERROR_BACKTRACK_LIMIT;

            case \PREG_RECURSION_LIMIT_ERROR:
                return self::ERROR_RECURSION_LIMIT;

            case \PREG_BAD_UTF8_ERROR:
                return self::ERROR_BAD_UTF8;

            case \PREG_BAD_UTF8_OFFSET_ERROR:
                return self::ERROR_BAD_UTF8_OFFSET;
        }

        return \sprintf(self::ERROR_UNEXPECTED, $code);
    }
}
