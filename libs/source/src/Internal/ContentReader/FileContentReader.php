<?php

/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phplrt\Source\Internal\ContentReader;

use Phplrt\Source\Exception\NotReadableException;
use Phplrt\Source\Internal\ContentReaderInterface;
use Phplrt\Source\MemoizableInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Phplrt\Source
 */
final class FileContentReader implements ContentReaderInterface, MemoizableInterface
{
    /**
     * @var string
     */
    private const ERROR_NOT_READABLE = 'An error occurred while trying to read a file "%s"';

    /**
     * @var string|null
     */
    private ?string $content = null;

    /**
     * FileContentReader constructor.
     *
     * @param string $pathname
     */
    public function __construct(
        private readonly string $pathname
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        if ($this->content === null) {
            $this->content = $this->read();
        }

        return $this->content;
    }

    /**
     * @return string
     */
    private function read(): string
    {
        $result = @\file_get_contents($this->pathname);

        if (! \is_string($result)) {
            throw new NotReadableException(\sprintf(self::ERROR_NOT_READABLE, $this->pathname));
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function free(): void
    {
        $this->content = null;
    }
}
