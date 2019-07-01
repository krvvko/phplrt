<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

use Phplrt\Contracts\Io\Readable;

/**
 * Class MarkedGroups
 */
class MarkedGroups extends Driver
{
    /**
     * @var string
     */
    private const MARKER = 'MARK';

    /**
     * @return string
     */
    protected function pattern(): string
    {
        return '\G(?|%s)';
    }

    /**
     * @param string $name
     * @param string $pattern
     * @return string
     */
    protected function token(string $name, string $pattern): string
    {
        return \sprintf('(?:%s)(*%s:%s)', $pattern, self::MARKER, $name);
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|array[]
     */
    public function exec(Readable $file, string $content, int $offset = 0): iterable
    {
        \preg_match_all($this->pattern, $content, $matches, \PREG_SET_ORDER, $offset);

        foreach ($matches as [0 => $value, self::MARKER => $name]) {
            yield [
                self::TOKEN_NAME   => $name,
                self::TOKEN_VALUE  => $value,
                self::TOKEN_OFFSET => $offset,
            ];

            $offset += \strlen($value);

            if ($this->breaks($name)) {
                break;
            }
        }
    }
}
