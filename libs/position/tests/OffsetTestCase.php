<?php

/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phplrt\Position\Tests;

use Phplrt\Position\Position;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class OffsetTestCase extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param string $text
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testOffsetOverflow(string $text): void
    {
        $position = Position::fromOffset($text, \PHP_INT_MAX);

        $this->assertSame(\strlen($text), $position->getOffset());
    }

    /**
     * @dataProvider provider
     * @param string $text
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testOffsetUnderflow(string $text): void
    {
        if (\ini_get('assert.active')) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $position = Position::fromOffset($text, \PHP_INT_MIN);

        $this->assertSame(0, $position->getOffset());
    }
}
