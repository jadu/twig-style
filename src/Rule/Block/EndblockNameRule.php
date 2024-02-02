<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use Webmozart\Assert\Assert;

/**
 * Ensure that an endblock tag has the name of the corresponding block tag.
 */
final class EndblockNameRule extends AbstractRule
{
    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return void
     */
    protected function process(int $tokenPosition, array $tokens): void
    {
        $token = $tokens[$tokenPosition];

        if (
            $this->isTokenMatching($token, Token::BLOCK_NAME_TYPE)
            && $token->getValue() === 'endblock'
        ) {
            $matchingBlockTokenPosition = $this->getMatchingBlockTokenPositionForEndblockToken($tokenPosition, $tokens);
            if (!$matchingBlockTokenPosition) {
                $this->addError('Could not find matching block tag for endblock tag', $token);

                return;
            }

            $nameAfterBlockTokenPosition = $this->getNameTokenPositionAfterBlockToken($matchingBlockTokenPosition, $tokens);
            $nameAfterBlockToken = ($nameAfterBlockTokenPosition !== false) ? $tokens[$nameAfterBlockTokenPosition]->getValue() : null;
            $nameAfterEndblockTokenPosition = $this->getNameTokenPositionAfterBlockToken($tokenPosition, $tokens);
            $nameAfterEndblockToken = ($nameAfterEndblockTokenPosition !== false) ? $tokens[$nameAfterEndblockTokenPosition]->getValue() : null;

            if (!$nameAfterBlockToken && !$nameAfterEndblockToken) {
                $this->addError('Missing block name and endblock name', $token);

                return;
            }

            if (!$nameAfterBlockToken && $nameAfterEndblockToken) {
                $this->addError(
                    sprintf('Missing block name "%s"', $nameAfterEndblockToken),
                    $tokens[$matchingBlockTokenPosition]
                );

                return;
            }

            if ($nameAfterBlockToken && !$nameAfterEndblockToken) {
                $fixer = $this->addFixableError(
                    sprintf('Missing endblock name "%s"', $nameAfterBlockToken),
                    $token
                );
            } elseif ($nameAfterBlockToken !== $nameAfterEndblockToken) {
                $fixer = $this->addFixableError(
                    sprintf('Mismatching block name "%s" and endblock name "%s"', $nameAfterBlockToken, $nameAfterEndblockToken),
                    $token
                );
            } else {
                // Block name and endblock name already match
                Assert::same($nameAfterBlockToken, $nameAfterEndblockToken);

                return;
            }

            // Dry run
            if ($fixer === null) {
                return;
            }

            if ($nameAfterEndblockTokenPosition !== false) {
                $fixer->replaceToken($nameAfterEndblockTokenPosition, $nameAfterBlockToken);
            } else {
                $fixer->replaceToken($tokenPosition, sprintf('endblock %s', $nameAfterBlockToken));
            }
        }
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return int|false
     */
    private function getNameTokenPositionAfterBlockToken(int $tokenPosition, array $tokens): int|false
    {
        $token = $tokens[$tokenPosition];

        // Ignore new line
        $next = $this->findNext(Token::INDENT_TOKENS, $tokens, $tokenPosition + 1, true);
        if (false === $next || $this->isTokenMatching($tokens[$next], Token::EOL_TOKENS)) {
            return false;
        }

        $nextPosition = $tokenPosition + 1;
        while (!$this->isTokenMatching($tokens[$nextPosition], Token::BLOCK_END_TYPE)) {
            if ($this->isTokenMatching($tokens[$nextPosition], Token::NAME_TYPE)) {
                return $nextPosition;
            }
            ++$nextPosition;
        }

        return false;
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return int|false
     */
    private function getMatchingBlockTokenPositionForEndblockToken(int $tokenPosition, array $tokens): int|false
    {
        $token = $tokens[$tokenPosition];

        if (!$this->isTokenMatching($token, Token::BLOCK_NAME_TYPE) || $token->getValue() !== 'endblock') {
            return false;
        }

        $previousPosition = $tokenPosition - 1;
        $blocks = 0;
        $endblocks = 1;
        // When $blocks === $endblocks we have found the matching block token for the original endblock token
        while ($blocks !== $endblocks && false !== ($previousPosition = $this->findPrevious(Token::BLOCK_NAME_TYPE, $tokens, $previousPosition))) {
            $previousBlockToken = $tokens[$previousPosition];
            if ($previousBlockToken->getValue() === 'block') {
                ++$blocks;
            } elseif ($previousBlockToken->getValue() === 'endblock') {
                ++$endblocks;
            }
            --$previousPosition;
        }

        if ($blocks === $endblocks) {
            return $previousPosition;
        }

        return false;
    }
}
