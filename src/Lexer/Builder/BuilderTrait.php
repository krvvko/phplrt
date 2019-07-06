<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Builder;

/**
 * Trait BuilderTrait
 */
trait BuilderTrait
{
    /**
     * @var array|string[]
     */
    protected $tokens = [];

    /**
     * @var array|string[]
     */
    protected $skip = [];

    /**
     * @var array|string[]
     */
    protected $jumps = [];

    /**
     * @return iterable|string[]
     */
    public function tokens(): iterable
    {
        return $this->tokens;
    }

    /**
     * @return iterable|string[]
     */
    public function skips(): iterable
    {
        return $this->skip;
    }

    /**
     * @return iterable|string[]
     */
    public function jumps(): iterable
    {
        return $this->jumps;
    }
}
