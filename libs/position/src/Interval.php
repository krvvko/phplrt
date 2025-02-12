<?php

/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phplrt\Position;

use Phplrt\Contracts\Position\IntervalInterface;
use Phplrt\Contracts\Position\PositionInterface;

class Interval implements IntervalInterface
{
    use IntervalFactoryTrait;

    /**
     * @var PositionInterface
     */
    private readonly PositionInterface $from;

    /**
     * @var PositionInterface
     */
    private readonly PositionInterface $to;

    /**
     * @param PositionInterface $from
     * @param PositionInterface $to
     */
    public function __construct(PositionInterface $from, PositionInterface $to)
    {
        if ($from->getOffset() > $to->getOffset()) {
            [$from, $to] = [$to, $from];
        }

        $this->from = $from;
        $this->to = $to;
    }

    /**
     * {@inheritDoc}
     */
    public function fromPosition(): PositionInterface
    {
        return $this->from;
    }

    /**
     * {@inheritDoc}
     */
    public function toPosition(): PositionInterface
    {
        return $this->to;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress MoreSpecificReturnType Length is uint: "to" always greater than "from"
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function getLength(): int
    {
        return $this->to->getOffset() - $this->from->getOffset();
    }
}
