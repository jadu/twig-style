<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;
use Webmozart\Assert\Assert;

/**
 * Ensure that there is one new line before block tags and after endblock tags, with the following exceptions:
 * 1. Inline blocks are allowed. e.g.
 *      <body class="{% block body_classes %}{{ bodyClasses }}{% endblock %}">
 * 2. Comments on the line above block tags are allowed. e.g.
 *      {# This block adds a container around the aside #}
 *      {% block aside_container %}.
 */
final class BlockNewLineRule extends AbstractRule
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
            !$this->isTokenMatching($token, Token::BLOCK_NAME_TYPE)
            || !in_array($token->getValue(), ['block', 'endblock'], true)
        ) {
            return;
        }

        if ($token->getValue() === 'block') {
            $this->checkEolBeforeBlock($tokenPosition, $tokens);
        } elseif ($token->getValue() === 'endblock') {
            $this->checkEolAfterEndblock($tokenPosition, $tokens);
        }
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return void
     */
    private function checkEolBeforeBlock(int $tokenPosition, array $tokens): void
    {
        $token = $tokens[$tokenPosition];

        // Find the opening {% BLOCK_START_TYPE token
        $blockStartPosition = $this->findPrevious(Token::BLOCK_START_TYPE, $tokens, $tokenPosition - 1);
        // Find the token before the BLOCK_START_TYPE token, ignoring any WHITESPACE_TYPE tokens
        $tokenBeforeBlockStartPosition = $this->findPrevious(Token::WHITESPACE_TYPE, $tokens, $blockStartPosition - 1, true);
        // If any token other than EOL is found, this is an "inline" block, so return early
        if (!$this->isTokenMatching($tokens[$tokenBeforeBlockStartPosition], Token::EOL_TYPE)) {
            return;
        }

        // Calculate the number of consecutive EOL tokens before this one by finding the previous non-EOL_TYPE token
        $previousPosition = $this->findPrevious(Token::EOL_TYPE, $tokens, $tokenBeforeBlockStartPosition - 1, true);
        if (false === $previousPosition) {
            // If all previous tokens are EOL_TYPE, we have to count one more
            // since $tokenPosition starts at 0
            $consecutiveEolTokens = $tokenBeforeBlockStartPosition + 1;
        } else {
            $consecutiveEolTokens = $tokenBeforeBlockStartPosition - $previousPosition - 1;
        }

        // Only 0 or 2+ blank lines are reported
        if (1 === $consecutiveEolTokens) {
            return;
        }

        // Allow comments above blocks
        if (0 === $consecutiveEolTokens && $this->isTokenMatching($tokens[$previousPosition], Token::COMMENT_END_TYPE)) {
            return;
        }

        $fixer = $this->addFixableError(
            sprintf('A block must start with 1 new line; found %d', $consecutiveEolTokens),
            $token
        );

        // Dry run
        if (null === $fixer) {
            return;
        }

        // Safeguard because we may have added extra empty lines to the count if all previous tokens are EOL_TYPE
        $consecutiveEolTokens = min($consecutiveEolTokens, $tokenBeforeBlockStartPosition);

        if (0 === $consecutiveEolTokens) {
            $fixer->addNewlineBefore($tokenBeforeBlockStartPosition);
        } else {
            $fixer->beginChangeSet();
            while ($consecutiveEolTokens >= 2 || $consecutiveEolTokens === $tokenBeforeBlockStartPosition) {
                $fixer->replaceToken($tokenBeforeBlockStartPosition - $consecutiveEolTokens, '');
                --$consecutiveEolTokens;
            }
            $fixer->endChangeSet();
        }
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return void
     */
    private function checkEolAfterEndblock(int $tokenPosition, array $tokens): void
    {
        $token = $tokens[$tokenPosition];

        // Find the closing %} BLOCK_END_TYPE token
        $blockEndPosition = $this->findNext(Token::BLOCK_END_TYPE, $tokens, $tokenPosition + 1);
        // Find the token after the BLOCK_END_TYPE token, ignoring any WHITESPACE_TYPE tokens
        $tokenAfterBlockEndPosition = $this->findNext(Token::WHITESPACE_TYPE, $tokens, $blockEndPosition + 1, true);
        // If any token other than EOL is found, this is an "inline" block, so return early
        if (!$this->isTokenMatching($tokens[$tokenAfterBlockEndPosition], Token::EOL_TYPE)) {
            return;
        }

        // Calculate the number of consecutive EOL tokens after this one by finding the next non-EOL_TYPE token
        $nextPosition = $this->findNext(Token::EOL_TYPE, $tokens, $tokenAfterBlockEndPosition + 1, true);
        Assert::notFalse($nextPosition, 'An EOL_TYPE cannot be the last non-empty token');
        $consecutiveEolTokens = $nextPosition - $tokenAfterBlockEndPosition - 1;

        // If the EOF token is found, this is the end of file so an extra new line is not required
        if ($this->isTokenMatching($tokens[$nextPosition], Token::EOF_TYPE)) {
            return;
        }

        // Only 0 or 2+ blank lines are reported
        if (1 === $consecutiveEolTokens) {
            return;
        }

        $fixer = $this->addFixableError(
            sprintf('A block must end with 1 new line; found %d', $consecutiveEolTokens),
            $token
        );

        // Dry run
        if (null === $fixer) {
            return;
        }

        if (0 === $consecutiveEolTokens) {
            $fixer->addNewline($tokenAfterBlockEndPosition);
        } else {
            $fixer->beginChangeSet();
            while ($consecutiveEolTokens >= 2) {
                $fixer->replaceToken($nextPosition - $consecutiveEolTokens, '');
                --$consecutiveEolTokens;
            }
            $fixer->endChangeSet();
        }
    }
}
