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
use Phplrt\Lexer\Exception\LexerException;
use Phplrt\Lexer\Exception\RuntimeException;

/**
 * Class NamedGroups
 */
class NamedGroups extends Driver
{
    /**
     * @return string
     */
    protected function pattern(): string
    {
        return '\G%s';
    }

    /**
     * @param string $name
     * @param string $pattern
     * @return string
     */
    protected function token(string $name, string $pattern): string
    {
        return \sprintf('(?P<%s>%s)', $name, $pattern);
    }

    /**
     * @param \Phplrt\Contracts\Io\Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|array[]
     * @throws \Phplrt\Lexer\Exception\LexerException
     */
    public function exec(Readable $file, string $content, int $offset = 0): iterable
    {
        $content = $offset !== 0 ? (string)\substr($content, $offset) : $content;

        return (array)$this->wrap(function() use ($content, $offset) {
            $output = [];

            $callback = $this->callback($offset, $output);

            @\preg_replace_callback($this->pattern, $callback, $content);

            return $output;
        });
    }

    /**
     * @param int $offset
     * @param array $output
     * @return \Closure
     */
    private function callback(int &$offset, array &$output): \Closure
    {
        $completed = false;

        return function (array $matches) use (&$completed, &$offset, &$output): string {
            if ($completed) {
                return '';
            }

            [$name, $value] = $this->each($matches);

            if ($this->breaks($name)) {
                $completed = true;
            }

            $output[] = [
                static::TOKEN_NAME   => $name,
                static::TOKEN_VALUE  => $value,
                static::TOKEN_OFFSET => $offset,
            ];

            $offset += \strlen($value);

            return '';
        };
    }

    /**
     * @param array $matches
     * @return array
     * @throws RuntimeException
     */
    private function each(array $matches): array
    {
        foreach (\array_reverse($matches) as $name => $value) {
            if (\is_string($name)) {
                return [$name, $value];
            }
        }

        throw new RuntimeException('An empty lexeme pattern was detected', 1);
    }
}
