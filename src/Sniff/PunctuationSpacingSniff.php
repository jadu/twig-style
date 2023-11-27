<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Sniff;

use TwigCsFixer\Sniff\AbstractSpacingSniff;
use TwigCsFixer\Token\Token;
use Webmozart\Assert\Assert;

/**
 * Ensure there is no space before and after punctuation except for '{', '}', ':', and ','.
 * No spaces are allowed between "paired" tokens such as arrays, hashes, and parentheses when they are empty.
 *
 * @see TwigCsFixer\Sniff\PunctuationSpacingSniff
 */
final class PunctuationSpacingSniff extends AbstractSpacingSniff
{
    private const SPACE_BEFORE = [
        ')' => 0,
        ']' => 0,
        '}' => 1,
        ':' => 0,
        '.' => 0,
        ',' => 0,
        '|' => 0,
    ];

    private const SPACE_AFTER = [
        '(' => 0,
        '[' => 0,
        '{' => 1,
        '.' => 0,
        '|' => 0,
        ':' => 1,
        ',' => 1,
    ];

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return int|null
     */
    protected function getSpaceBefore(int $tokenPosition, array $tokens): ?int
    {
        $token = $tokens[$tokenPosition];
        if (!$this->isTokenMatching($token, Token::PUNCTUATION_TYPE)) {
            return null;
        }

        $previousPosition = $this->findPrevious(Token::WHITESPACE_TOKENS, $tokens, $tokenPosition - 1, true);
        if (false === $previousPosition) {
            return null;
        }

        // Always remove spaces for empty arrays, hashes, and parentheses
        $previousToken = $tokens[$previousPosition];
        if ($this->getPairedTokens($previousToken, $token)) {
            return 0;
        }

        return self::SPACE_BEFORE[$token->getValue()] ?? null;
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return int|null
     */
    protected function getSpaceAfter(int $tokenPosition, array $tokens): ?int
    {
        $token = $tokens[$tokenPosition];

        if (!$this->isTokenMatching($token, Token::PUNCTUATION_TYPE)) {
            return null;
        }

        $nextPosition = $this->findNext(Token::WHITESPACE_TOKENS, $tokens, $tokenPosition + 1, true);
        Assert::notFalse($nextPosition, 'A PUNCTUATION_TYPE cannot be the last non-empty token');

        // Always remove spaces for empty arrays, hashes, and parentheses
        $nextToken = $tokens[$nextPosition];
        if ($this->getPairedTokens($token, $nextToken)) {
            return 0;
        }

        // We cannot change spaces after a token, if the next one has a constraint: `[1,2,3,]`.
        if (null !== $this->getSpaceBefore($nextPosition, $tokens)) {
            return null;
        }

        return self::SPACE_AFTER[$token->getValue()] ?? null;
    }

    /**
     * Returns whether two tokens are the opening and closing tokens of an array, hash, or parentheses.
     *
     * @param Token $firstToken
     * @param Token $secondToken
     *
     * @return bool
     */
    protected function getPairedTokens(Token $firstToken, Token $secondToken): bool
    {
        return
            ($firstToken->getValue() === '{' && $secondToken->getValue() === '}')
            || ($firstToken->getValue() === '(' && $secondToken->getValue() === ')')
            || ($firstToken->getValue() === '[' && $secondToken->getValue() === ']');
    }
}
