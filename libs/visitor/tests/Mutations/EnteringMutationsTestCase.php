<?php

/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phplrt\Visitor\Tests\Mutations;

use Phplrt\Visitor\Exception\VisitorException;
use Phplrt\Visitor\Visitor;
use Phplrt\Visitor\Tests\TestCase;
use Phplrt\Visitor\Tests\Stub\Node;
use Phplrt\Contracts\Ast\NodeInterface;
use Phplrt\Visitor\Exception\BadMethodException;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Class EnteringMutationsTestCase
 *
 * @testdox A set of tests that verify an AST modification using the Visitor::enter() method.
 */
class EnteringMutationsTestCase extends TestCase
{
    /**
     * @testdox Modifying a collection of AST nodes using array return
     *
     * @return void
     */
    public function testUpdateRootsByArrayWhenEntering(): void
    {
        $this->expectException(BadMethodException::class);
        $this->expectExceptionCode(VisitorException::ERROR_CODE_ARRAY_ENTERING);

        $this->traverse($this->nodes(2), new class () extends Visitor {
            public function enter(NodeInterface $node): mixed
            {
                return $node instanceof Node && $node->getId() === 0 ? [] : $node;
            }
        });
    }

    /**
     * @testdox Modifying an AST node using array return
     *
     * @return void
     */
    public function testUpdateRootByArrayWhenEntering(): void
    {
        $this->expectException(BadMethodException::class);
        $this->expectExceptionCode(VisitorException::ERROR_CODE_ARRAY_ENTERING);

        $this->traverse($this->node(), new class () extends Visitor {
            public function enter(NodeInterface $node): mixed
            {
                return $node instanceof Node && $node->getId() === 0 ? [] : $node;
            }
        });
    }

    /**
     * @testdox Modifying a collection of AST nodes using a new node object return
     *
     * @return void
     * @throws ExpectationFailedException
     */
    public function testUpdateRootsByNodeWhenEntering(): void
    {
        $actual = $this->traverse($original = $this->nodes(2), new class () extends Visitor {
            public function enter(NodeInterface $node): mixed
            {
                return $node instanceof Node && $node->getId() === 0 ? new Node(42) : $node;
            }
        });

        $this->assertEquals([new Node(42), new Node(42)], $actual);
        $this->assertNotSame($original, $actual);
    }

    /**
     * @testdox Modifying an AST node using a new node object return
     *
     * @return void
     * @throws ExpectationFailedException
     */
    public function testUpdateRootByNodeWhenEntering(): void
    {
        $actual = $this->traverse($original = $this->node(), new class () extends Visitor {
            public function enter(NodeInterface $node): mixed
            {
                return $node instanceof Node && $node->getId() === 0 ? new Node(42) : $node;
            }
        });

        $this->assertEquals(new Node(42), $actual);
        $this->assertNotSame($original, $actual);
    }
}
