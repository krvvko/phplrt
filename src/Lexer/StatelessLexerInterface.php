<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Driver\DriverInterface;

/**
 * Interface StatelessLexerInterface
 */
interface StatelessLexerInterface extends LexerInterface
{
    /**
     * @param string $name
     * @param DriverInterface $driver
     * @return DriverInterface
     */
    public function add(string $name, DriverInterface $driver): DriverInterface;
}
