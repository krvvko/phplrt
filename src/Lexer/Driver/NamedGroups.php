<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Driver;

use Phplrt\Lexer\Compiler\Preg;
use Phplrt\Contracts\Io\Readable;
use Phplrt\Lexer\Exception\LexerException;
use Phplrt\Lexer\Exception\RuntimeException;
use Phplrt\Lexer\Exception\UnrecognizedTokenException;

/**
 * Class NamedGroups
 */
class NamedGroups extends StatelessDriver
{
    /**
     * @var string|null
     */
    private $pattern;

    /**
     * @param Readable $file
     * @param string $content
     * @param int $offset
     * @return iterable|array
     * @throws LexerException
     */
    public function lex(Readable $file, string $content, int $offset = 0): iterable
    {
        $output = [];

        if ($offset !== 0) {
            $content = \substr($content, $offset);
        }

        $this->execute($this->lexCallback($file, $offset, $output), $content);

        return $output;
    }

    /**
     * @param \Closure $callback
     * @param $content $content
     * @return void
     * @throws LexerException
     */
    private function execute(\Closure $callback, string $content): void
    {
        try {
            \error_clear_last();

            @\preg_replace_callback($this->getPattern(), $callback, $content);

            Preg::assert(\preg_last_error(), \error_get_last());
        } catch (LexerException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getPattern(): string
    {
        if ($this->pattern === null) {
            $this->pattern = $this->compilePattern();
        }

        return $this->pattern;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function compilePattern(): string
    {
        $each = static function (string $name, string $pattern): string {
            return \sprintf('(?P<%s>%s)', $name, $pattern);
        };

        return (new Preg($this->getTokens()))->compile($each, '\G%s');
    }

    /**
     * @param Readable $file
     * @param int $offset
     * @param array $output
     * @return \Closure
     */
    private function lexCallback(Readable $file, int &$offset, array &$output): \Closure
    {
        $completed = false;


        return function (array $matches) use ($file, &$completed, &$offset, &$output): string {
            if ($completed) {
                return '';
            }

            [$name, $value] = $this->each($matches);

            if ($this->isUnknown($name)) {
                $error = \sprintf('Unrecognized token "%s"', $value);

                throw (new UnrecognizedTokenException($error))->throwsIn($file, $offset);
            }

            if ($this->shouldBreak($name)) {
                $completed = true;
            }

            $output[] = $this->token($name, $value, $offset);

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

    /**
     * @return void
     */
    protected function reset(): void
    {
        $this->pattern = null;
    }
}
