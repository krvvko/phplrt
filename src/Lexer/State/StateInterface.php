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
interface StateInterface extends RuntimeInterface
{
    /**
     * @param string|\Phplrt\Lexer\Driver\DriverInterface $driver
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function using(string $driver): self;

    /**
     * Push a pattern for lexeme recognition
     *
     * @param string $name Token name
     * @param string $pattern Regular expression used for token matching
     * @param string|null $state New state name, after the rule was applied
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function add(string $name, string $pattern, string $state = null): self;

    /**
     * @param array|string[] $tokens
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function addMany(array $tokens): self;

    /**
     * @param string $token
     * @param string $next
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function jump(string $token, string $next): self;

    /**
     * @param array|string[] $jumps
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function jumpMany(array $jumps): self;

    /**
     * Add a token to the ignore list
     *
     * @param string ...$tokens List of token names
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function skip(string ...$tokens): self;
}
