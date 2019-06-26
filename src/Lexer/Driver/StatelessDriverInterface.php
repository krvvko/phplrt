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
 * Interface StatelessDriverInterface
 */
interface StatelessDriverInterface extends DriverInterface
{
    /**
     * @param string $token
     * @param string $pattern
     * @return StatelessDriverInterface|$this
     */
    public function append(string $token, string $pattern): self;

    /**
     * @param string $token
     * @param string $pattern
     * @return StatelessDriverInterface|$this
     */
    public function prepend(string $token, string $pattern): self;

    /**
     * @param string ...$tokens
     * @return StatelessDriverInterface|$this
     */
    public function skip(string ...$tokens): self;

    /**
     * @param string $token
     * @param string $state
     * @return StatelessDriverInterface|$this
     */
    public function jump(string $token, string $state): self;
}
