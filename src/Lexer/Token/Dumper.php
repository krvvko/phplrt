<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Token;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * Class Dumper
 */
class Dumper
{
    /**
     * @var string
     */
    protected const TOKEN_PATTERN = '"%s" (%s)';

    /**
     * @var string
     */
    protected const TOKEN_SHORTEN_SHORTEN_SUFFIX = '… (%s+)';

    /**
     * @var string
     */
    protected const TOKEN_VALUE_ESCAPE = '"';

    /**
     * @param TokenInterface $token
     * @return string
     */
    public static function dump(TokenInterface $token): string
    {
        $value = self::dumpValue($token->getValue());
        $value = self::shortenValue($value);

        return \sprintf(static::TOKEN_PATTERN, $value, $token->getName());
    }

    /**
     * @param string $value
     * @param int $length
     * @return string
     */
    private static function shortenValue(string $value, int $length = 15): string
    {
        $maxLength = $length - 5;

        if (\mb_strlen($value) > $length) {
            $size = \sprintf(static::TOKEN_SHORTEN_SHORTEN_SUFFIX, \mb_strlen($value) - $maxLength);

            return \mb_substr($value, 0, $maxLength) . $size;
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    private static function dumpValue(string $value): string
    {
        $value = (string)(\preg_replace('/\s+/u', ' ', $value) ?? $value);

        return \addcslashes($value, static::TOKEN_VALUE_ESCAPE);
    }
}
