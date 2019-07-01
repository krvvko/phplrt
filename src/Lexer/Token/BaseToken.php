<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Token;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * Class BaseToken
 */
abstract class BaseToken implements TokenInterface
{
    /**
     * @var int|null
     */
    private $length;

    /**
     * @var int|null
     */
    private $bytes;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return Dumper::dump($this);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'name'   => $this->getName(),
            'value'  => $this->getValue(),
            'offset' => $this->getOffset(),
            'length' => $this->getLength(),
            'bytes'  => $this->getBytes(),
        ];
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        if ($this->length === null) {
            $this->length = \mb_strlen($this->getValue());
        }

        return $this->length;
    }

    /**
     * @return int
     */
    public function getBytes(): int
    {
        if ($this->bytes === null) {
            $this->bytes = \strlen($this->getValue());
        }

        return $this->bytes;
    }
}
