<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Builder\Exception;

use Phplrt\Io\File;

/**
 * Class LexemeException
 */
class LexemeException extends BuilderException
{
    /**
     * @var string
     */
    private const ERROR_BAD_LEXEME = 'Bad lexeme %s for token %s';

    /**
     * @var string
     */
    private const ERROR_PATTERN = '/.+?\(\): Compilation failed: (.+?) at offset (\d+)/isum';

    /**
     * LexemeException constructor.
     *
     * @param string $token
     * @param string $lexeme
     * @param array $last
     * @param \Throwable|null $e
     */
    public function __construct(string $token, string $lexeme, array $last = [], \Throwable $e = null)
    {
        [$suffix, $offset] = $this->extract($last);
        $suffix = $suffix ? ': ' . $suffix : '';

        parent::__construct(\sprintf(self::ERROR_BAD_LEXEME, $lexeme, $token) . $suffix, 0, $e);

        $this->throwsIn(File::fromSources($lexeme, 'PCRE'), (int)$offset);
        $this->withCode($last['type'] ?? 0);
    }

    /**
     * @param array $last
     * @return array
     */
    private function extract(array $last = []): array
    {
        if (isset($last['message'])) {
            \preg_match(self::ERROR_PATTERN, $last['message'], $matches);

            if (count($matches) === 3) {
                return [\ucfirst(\trim($matches[1])), $matches[2]];
            }
        }

        return [null, 0];
    }
}
