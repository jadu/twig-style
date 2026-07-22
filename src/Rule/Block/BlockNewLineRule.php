<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;
use Webmozart\Assert\Assert;

/**
 * Ensure that there is one new line before block and macro tags and after endblock and endmacro tags, with the following exceptions:
 * 1. Inline blocks are allowed. e.g.
 *      <body class="{% block body_classes %}{{ bodyClasses }}{% endblock %}">
 * 2. Comments on the line above block tags are allowed. e.g.
 *      {# This block adds a container around the aside #}
 *      {% block aside_container %}.
 */
final class BlockNewLineRule extends AbstractFixableRule
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
            !$token->isMatching([Token::BLOCK_NAME_TYPE, Token::MACRO_NAME_TYPE])
            || !in_array($token->getValue(), ['block', 'macro', 'endblock', 'endmacro'], true)
        ) {
            return;
        }

        if (in_array($token->getValue(), ['block', 'macro'], true)) {
            $this->checkEolBeforeBlock($tokenPosition, $tokens);
        } elseif (in_array($token->getValue(), ['endblock', 'endmacro'], true)) {
            $this->checkEolAfterEndblock($tokenPosition, $tokens);
        }
    }

    /**
     * @param int $tokenPosition
     * @param Tokens $tokens
     *
     * @return void
     */
    private function checkEolBeforeBlock(int $tokenPosition, Tokens $tokens): void
    {
        $token = $tokens->get($tokenPosition);
        $tokenName = str_starts_with($token->getValue(), 'end') ? substr($token->getValue(), 3) : $token->getValue();

        // Find the opening {% BLOCK_START_TYPE token
        $blockStartPosition = $tokens->findPrevious(Token::BLOCK_START_TYPE, $tokenPosition - 1);
        // Find the token before the BLOCK_START_TYPE token, ignoring any WHITESPACE_TYPE tokens
        $tokenBeforeBlockStartPosition = $tokens->findPrevious(Token::WHITESPACE_TYPE, $blockStartPosition - 1, 0, true);
        // If any token other than EOL is found, this is an "inline" block, so return early
        if ($tokenBeforeBlockStartPosition !== false && !$tokens->get($tokenBeforeBlockStartPosition)->isMatching(Token::EOL_TYPE)) {
            return;
        }

        // Calculate the number of consecutive EOL tokens before this one by finding the previous non-EOL_TYPE token
        $previousPosition = $tokens->findPrevious(Token::EOL_TYPE, $tokenBeforeBlockStartPosition - 1, 0, true);
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
        if (0 === $consecutiveEolTokens && $tokens->get($previousPosition)->isMatching(Token::COMMENT_END_TYPE)) {
            return;
        }

        $fixer = $this->addFixableError(
            sprintf('A %s must start with 1 new line; found %d', $tokenName, $consecutiveEolTokens),
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
     * @param Tokens $tokens
     *
     * @return void
     */
    private function checkEolAfterEndblock(int $tokenPosition, Tokens $tokens): void
    {
        $token = $tokens->get($tokenPosition);
        $tokenName = str_starts_with($token->getValue(), 'end') ? substr($token->getValue(), 3) : $token->getValue();

        // Find the closing %} BLOCK_END_TYPE token
        $blockEndPosition = $tokens->findNext(Token::BLOCK_END_TYPE, $tokenPosition + 1);
        // Find the token after the BLOCK_END_TYPE token, ignoring any WHITESPACE_TYPE tokens
        $tokenAfterBlockEndPosition = $tokens->findNext(Token::WHITESPACE_TYPE, $blockEndPosition + 1, null, true);
        // If any token other than EOL is found, this is an "inline" block, so return early
        if (!$tokens->get($tokenAfterBlockEndPosition)->isMatching(Token::EOL_TYPE)) {
            return;
        }

        // Calculate the number of consecutive EOL tokens after this one by finding the next non-EOL_TYPE token
        $nextPosition = $tokens->findNext(Token::EOL_TYPE, $tokenAfterBlockEndPosition + 1, null, true);
        Assert::notFalse($nextPosition, 'An EOL_TYPE cannot be the last non-empty token');
        $consecutiveEolTokens = $nextPosition - $tokenAfterBlockEndPosition - 1;

        // If the EOF token is found, this is the end of file so an extra new line is not required
        if ($tokens->get($nextPosition)->isMatching(Token::EOF_TYPE)) {
            return;
        }

        // Only 0 or 2+ blank lines are reported
        if (1 === $consecutiveEolTokens) {
            return;
        }

        $fixer = $this->addFixableError(
            sprintf('A %s must end with 1 new line; found %d', $tokenName, $consecutiveEolTokens),
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
