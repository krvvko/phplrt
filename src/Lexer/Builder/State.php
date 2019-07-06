<?php
/**
 * This file is part of Phplrt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Phplrt\Lexer\Builder;

use Phplrt\Lexer\Builder;
use Phplrt\Lexer\Builder\Exception\LexemeException;
use Phplrt\Lexer\Builder\Exception\UnrecognizedStateException;
use Phplrt\Lexer\Builder\Exception\UnrecognizedTokenException;
use Phplrt\Lexer\Token\Unknown;

/**
 * Class State
 */
class State implements MutableBuilderInterface
{
    use MutableBuilderTrait {
        MutableBuilderTrait::jumps as traitJumps;
        MutableBuilderTrait::skips as traitSkips;
        MutableBuilderTrait::tokens as traitTokens;
    }

    /**
     * @var string
     */
    private const ERROR_NO_STATE = 'Unable to detect state "%s" because it has not been defined';

    /**
     * @var string
     */
    private const ERROR_NO_TOKEN_FOR_STATE = 'State transition cannot be determined because token %s does not exist';

    /**
     * @var string
     */
    private const ERROR_NO_TOKEN_FOR_SKIP = 'Unable to detect token %s for skipping';

    /**
     * @var string
     */
    private const PCRE_FORMAT = '/\\G(?|%s)/Ssu';

    /**
     * @var string
     */
    private const PCRE_DELIMITER = '/';

    /**
     * @var string
     */
    private const PCRE_CHUNK_FORMAT = '(%s)(*MARK:%s)';

    /**
     * @var string
     */
    private const PCRE_UNKNOWN = '.+?';

    /**
     * @var \Phplrt\Lexer\Builder
     */
    private $builder;

    /**
     * State constructor.
     *
     * @param \Phplrt\Lexer\Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return array
     * @throws \Phplrt\Lexer\Builder\Exception\BuilderException
     */
    public function build(): array
    {
        $this->assertJumps();
        $this->assertSkips();
        $this->assertPatterns();

        return [$this->pcre(), $this->skips(), $this->jumps()];
    }

    /**
     * @return void
     * @throws \Phplrt\Lexer\Builder\Exception\BuilderException
     */
    private function assertJumps(): void
    {
        $tokens = \array_keys($this->tokens());

        foreach ($this->jumps() as $token => $state) {
            if (! $this->builder->hasState($state)) {
                throw new UnrecognizedStateException(\sprintf(self::ERROR_NO_STATE, $state));
            }

            if (! \in_array($token, $tokens, true)) {
                throw new UnrecognizedTokenException(\sprintf(self::ERROR_NO_TOKEN_FOR_STATE, $token));
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function tokens(): array
    {
        return \array_merge($this->traitTokens(), $this->builder->tokens());
    }

    /**
     * @return array|string[]
     */
    public function jumps(): array
    {
        return \array_merge($this->traitJumps(), $this->builder->jumps());
    }

    /**
     * @return void
     * @throws \Phplrt\Lexer\Builder\Exception\UnrecognizedTokenException
     */
    private function assertSkips(): void
    {
        $tokens = \array_keys($this->tokens());

        foreach ($this->skips() as $token) {
            if (! \in_array($token, $tokens, true)) {
                throw new UnrecognizedTokenException(\sprintf(self::ERROR_NO_TOKEN_FOR_SKIP, $token));
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function skips(): array
    {
        $skips = \array_merge($this->traitSkips(), $this->builder->skips());

        return \array_unique($skips);
    }

    /**
     * @return void
     * @throws \Phplrt\Lexer\Builder\Exception\LexemeException
     */
    private function assertPatterns(): void
    {
        foreach ($this->tokens() as $name => $pattern) {
            $pattern = self::PCRE_DELIMITER . $this->escapeTokenPattern($pattern) . self::PCRE_DELIMITER;
            @\preg_match($pattern, '');

            if (\preg_last_error() !== \PREG_NO_ERROR) {
                throw new LexemeException($name, $pattern, \error_get_last());
            }
        }
    }

    /**
     * @return \Traversable|string[]
     */
    private function tokenChunks(): \Traversable
    {
        $tokens = \array_merge($this->tokens(), [
            Unknown::T_NAME => self::PCRE_UNKNOWN,
        ]);

        foreach ($tokens as $name => $pattern) {
            yield $name => \vsprintf(self::PCRE_CHUNK_FORMAT, [
                $this->escapeTokenPattern($pattern),
                $this->escapeTokenName($name),
            ]);
        }
    }

    /**
     * @param string $pattern
     * @return string
     */
    private function escapeTokenPattern(string $pattern): string
    {
        return \addcslashes($pattern, self::PCRE_DELIMITER);
    }

    /**
     * @param string $name
     * @return string
     */
    private function escapeTokenName(string $name): string
    {
        return \preg_quote($name, self::PCRE_DELIMITER);
    }

    /**
     * @return string
     */
    private function pcre(): string
    {
        $body = \implode('|', \iterator_to_array($this->tokenChunks()));

        return \sprintf(self::PCRE_FORMAT, $body);
    }
}
