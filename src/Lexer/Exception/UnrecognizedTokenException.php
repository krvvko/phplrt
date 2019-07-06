<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * Class UnrecognizedTokenException
 */
class UnrecognizedTokenException extends LexerException
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Unrecognized token "%s"';

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * UnrecognizedTokenException constructor.
     *
     * @param \Phplrt\Contracts\Lexer\TokenInterface $token
     * @param \Throwable|null $previous
     */
    public function __construct(TokenInterface $token, \Throwable $previous = null)
    {
        $message = \sprintf(self::ERROR_MESSAGE, $this->token = $token);

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return \Phplrt\Contracts\Lexer\TokenInterface
     */
    public function getToken(): TokenInterface
    {
        return $this->token;
    }
}
