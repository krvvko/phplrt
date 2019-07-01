<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\State;

use Phplrt\Lexer\Exception\InitializationException;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @param \Phplrt\Lexer\State\StateInterface|\Phplrt\Lexer\State\StateInterface[]|array $states
     * @return iterable|\Phplrt\Lexer\State\StateInterface
     */
    public static function create($states): iterable
    {
        if ($states instanceof StateInterface) {
            return yield $states;
        }

        if (\is_iterable($states)) {
            return yield from self::fromIterable($states);
        }

        yield $states;
    }

    /**
     * @param iterable $states
     * @return iterable|\Phplrt\Lexer\State\StateInterface[]
     * @throws \Phplrt\Lexer\Exception\InitializationException
     */
    private static function fromIterable(iterable $states): iterable
    {
        foreach ($states as $name => $state) {
            //
            // List of tokens
            //
            if (\is_string($state)) {
                return yield new State($states);
            }

            //
            // Instance of State
            //
            if ($state instanceof StateInterface) {
                yield $name => $state;

                continue;
            }

            //
            // Otherwise
            //
            yield $name => new State($state);
        }
    }
}
