<?php

/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phplrt\Visitor;

use Phplrt\Contracts\Ast\NodeInterface;

interface VisitorInterface
{
    /**
     * Called once before traversal.
     *
     * @template T of iterable<NodeInterface>|NodeInterface
     *
     * @param T $nodes
     * @return T|null
     */
    public function before(iterable $nodes): ?iterable;

    /**
     * Called when entering a node.
     *
     * @param NodeInterface $node
     * @return mixed|void
     */
    public function enter(NodeInterface $node);

    /**
     * Called when leaving a node.
     *
     * @param NodeInterface $node
     * @return mixed|void
     */
    public function leave(NodeInterface $node);

    /**
     * Called once after traversal.
     *
     * @template T of iterable<NodeInterface>|NodeInterface
     *
     * @param T $nodes
     * @return T|null
     */
    public function after(iterable $nodes): ?iterable;
}
