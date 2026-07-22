<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;
use Webmozart\Assert\Assert;

/**
 * Ensure that an endblock or endmacro tag has the name of the corresponding block or macro tag.
 */
final class EndblockNameRule extends AbstractFixableRule
{
    /**
     * @param int $tokenPosition
     * @param Tokens $tokens
     *
     * @return void
     */
    protected function process(int $tokenPosition, Tokens $tokens): void
    {
        $token = $tokens->get($tokenPosition);

        if (
            $token->isMatching([Token::BLOCK_NAME_TYPE, Token::MACRO_NAME_TYPE])
            && in_array($token->getValue(), ['endblock', 'endmacro'], true)
        ) {
            $tokenName = $token->getValue();
            $pairedTokenName = str_starts_with($tokenName, 'end') ? substr($tokenName, 3) : $tokenName;

            $matchingBlockTokenPosition = $this->getMatchingBlockTokenPositionForEndblockToken($tokenPosition, $tokens);
            if (!$matchingBlockTokenPosition) {
                $this->addError(sprintf('Could not find matching %s tag for %s tag', $pairedTokenName, $tokenName), $token);

                return;
            }

            $nameAfterBlockTokenPosition = $this->getNameTokenPositionAfterBlockToken($matchingBlockTokenPosition, $tokens);
            $nameAfterBlockToken = ($nameAfterBlockTokenPosition !== false) ? $tokens->get($nameAfterBlockTokenPosition)->getValue() : null;
            $nameAfterEndblockTokenPosition = $this->getNameTokenPositionAfterBlockToken($tokenPosition, $tokens);
            $nameAfterEndblockToken = ($nameAfterEndblockTokenPosition !== false) ? $tokens->get($nameAfterEndblockTokenPosition)->getValue() : null;

            if (!$nameAfterBlockToken && !$nameAfterEndblockToken) {
                $this->addError(sprintf('Missing %s name and %s name', $pairedTokenName, $tokenName), $token);

                return;
            }

            if (!$nameAfterBlockToken && $nameAfterEndblockToken) {
                $this->addError(
                    sprintf('Missing %s name "%s"', $pairedTokenName, $nameAfterEndblockToken),
                    $tokens->get($matchingBlockTokenPosition)
                );

                return;
            }

            if ($nameAfterBlockToken && !$nameAfterEndblockToken) {
                $fixer = $this->addFixableError(
                    sprintf('Missing %s name "%s"', $tokenName, $nameAfterBlockToken),
                    $token
                );
            } elseif ($nameAfterBlockToken !== $nameAfterEndblockToken) {
                $fixer = $this->addFixableError(
                    sprintf('Mismatching %s name "%s" and %s name "%s"', $tokenName, $nameAfterBlockToken, $pairedTokenName, $nameAfterEndblockToken),
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
                $fixer->replaceToken($tokenPosition, sprintf('%s %s', $token->getValue(), $nameAfterBlockToken));
            }
        }
    }

    /**
     * @param int $tokenPosition
     * @param Tokens $tokens
     *
     * @return int|false
     */
    private function getNameTokenPositionAfterBlockToken(int $tokenPosition, Tokens $tokens): int|false
    {
        $token = $tokens->get($tokenPosition);

        // Ignore new line
        $next = $tokens->findNext(Token::INDENT_TOKENS, $tokenPosition + 1, null, true);
        if (false === $next || $tokens->get($next)->isMatching(Token::EOL_TOKENS)) {
            return false;
        }

        $nextPosition = $tokenPosition + 1;
        while (!$tokens->get($nextPosition)->isMatching(Token::BLOCK_END_TYPE)) {
            if ($tokens->get($nextPosition)->isMatching([Token::NAME_TYPE, Token::MACRO_NAME_TYPE])) {
                return $nextPosition;
            }
            ++$nextPosition;
        }

        return false;
    }

    /**
     * @param int $tokenPosition
     * @param Tokens $tokens
     *
     * @return int|false
     */
    private function getMatchingBlockTokenPositionForEndblockToken(int $tokenPosition, Tokens $tokens): int|false
    {
        $token = $tokens->get($tokenPosition);

        if (!$token->isMatching([Token::BLOCK_NAME_TYPE, Token::MACRO_NAME_TYPE]) || !in_array($token->getValue(), ['endblock', 'endmacro'], true)) {
            return false;
        }

        $previousPosition = $tokenPosition - 1;
        $blocks = 0;
        $endblocks = 1;
        // When $blocks === $endblocks we have found the matching block token for the original endblock or endmacro token
        while ($blocks !== $endblocks && false !== ($previousPosition = $tokens->findPrevious([Token::BLOCK_NAME_TYPE, Token::MACRO_NAME_TYPE], $previousPosition))) {
            $previousBlockToken = $tokens->get($previousPosition);
            if (in_array($previousBlockToken->getValue(), ['block', 'macro'])) {
                ++$blocks;
            } elseif (in_array($previousBlockToken->getValue(), ['endblock', 'endmacro'], true)) {
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
