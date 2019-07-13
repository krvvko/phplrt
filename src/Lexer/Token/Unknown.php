<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Token;

/**
 * Class Unknown
 */
class Unknown extends BaseToken
{
    /**
     * Unknown token name
     */
    public const T_NAME = 'T_UNKNOWN';

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $offset;

    /**
     * Unknown constructor.
     *
     * @param string $value
     * @param int $offset
     */
    public function __construct(string $value, int $offset = 0)
    {
        $this->value  = $value;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::T_NAME;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
