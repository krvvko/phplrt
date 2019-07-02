<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer;

use Phplrt\Lexer\State\GrammarInterface;
use Phplrt\Lexer\State\StateInterface;

/**
 * Interface Stateless
 */
interface Stateless
{
    /**
     * @var string
     */
    public const DEFAULT_STATE = 'default';

    /**
     * @param string $name
     * @return \Phplrt\Lexer\State\StateInterface
     */
    public function state(string $name = self::DEFAULT_STATE): StateInterface;

    /**
     * @return \Phplrt\Lexer\State\GrammarInterface
     */
    public function global(): GrammarInterface;
}
