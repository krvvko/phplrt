<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

/**
 * Interface StateInterface
 */
interface StateInterface extends RuntimeInterface, GrammarInterface
{
    /**
     * @param string|\Phplrt\Lexer\Driver\DriverInterface $driver
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function using(string $driver): self;
}
