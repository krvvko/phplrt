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
 * Class Driver
 */
abstract class Driver implements DriverInterface
{
    /**
     * @var int
     */
    public const TOKEN_NAME = 0x00;

    /**
     * @var int
     */
    public const TOKEN_VALUE = 0x01;

    /**
     * @var int
     */
    public const TOKEN_OFFSET = 0x02;

    /**
     * @var array|string[]
     */
    protected $tokens;

    /**
     * @var array|string[]
     */
    protected $skip;

    /**
     * @var array|int[]|string[]
     */
    protected $jumps;

    /**
     * @var string
     */
    private $unknown;

    /**
     * Driver constructor.
     *
     * @param array $tokens
     * @param array $skip
     * @param array $jumps
     */
    public function __construct(array $tokens, array $skip = [], array $jumps = [])
    {
        $this->tokens = $tokens;
        $this->skip   = $skip;
        $this->jumps  = $jumps;
    }

    /**
     * @return array|string[]
     * @throws \Exception
     */
    protected function getTokens(): array
    {
        return \array_merge($this->tokens, [
            $this->unknown = \uniqid('_', false) => '.+',
        ]);
    }

    /**
     * @param string $token
     * @return int|string|null
     */
    public function then(string $token)
    {
        return $this->jumps[$token] ?? null;
    }

    /**
     * @param string $token
     * @return bool
     */
    protected function shouldBreak(string $token): bool
    {
        return isset($this->jumps[$token]);
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function isUnknown(string $name): bool
    {
        return $name === $this->unknown;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $offset
     * @return array
     */
    protected function token(string $name, string $value, int $offset): array
    {
        return [
            static::TOKEN_NAME   => $name,
            static::TOKEN_VALUE  => $value,
            static::TOKEN_OFFSET => $offset,
        ];
    }
}
