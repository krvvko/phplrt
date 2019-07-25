<?php
/**
 * This file is part of phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Source\Exception;

use Phplrt\Contracts\Source\Exception\NotFoundExceptionInterface;

/**
 * The exception that occurs in the absence of a file in the file system.
 */
class NotFoundException extends NotReadableException implements
    NotFoundExceptionInterface
{
}
