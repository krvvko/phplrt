<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

use Phplrt\Lexer\State\RuntimeInterface;

/**
 * Interface DriverInterface
 */
interface DriverInterface extends RuntimeInterface
{
    /**
     * DriverInterface constructor.
     *
     * @param array|string[] $tokens
     * @param array|string[] $breaks
     * @param array|string[] $flags
     */
    public function __construct(array $tokens, array $breaks = [], array $flags = []);
}
