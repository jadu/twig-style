<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Ensure that an endblock tag has no name.
 */
final class NoEndblockNameRule extends AbstractFixableRule
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
            $token->isMatching(Token::BLOCK_NAME_TYPE)
            && $token->getValue() === 'endblock'
        ) {
            // Ignore new line
            $next = $tokens->findNext(Token::INDENT_TOKENS, $tokenPosition + 1, null, true);
            if (false === $next || $tokens->get($next)->isMatching(Token::EOL_TOKENS)) {
                return;
            }

            $error = false;
            $nextPosition = $tokenPosition + 1;
            while (!$tokens->get($nextPosition)->isMatching(Token::BLOCK_END_TYPE)) {
                $error = $tokens->get($nextPosition)->isMatching([Token::NAME_TYPE]);
                if ($error) {
                    $fixer = $this->addFixableError(
                        sprintf('Unexpected block name "%s" after %s', $tokens->get($nextPosition)->getValue(), $token->getValue()),
                        $token
                    );
                    break;
                }
                ++$nextPosition;
            }

            // No errors found
            if (!$error) {
                return;
            }

            // Dry run
            if ($fixer === null) {
                return;
            }

            while (!$tokens->get($nextPosition)->isMatching(Token::BLOCK_END_TYPE)) {
                $fixer->replaceToken($nextPosition, '');
                ++$nextPosition;
            }
        }
    }
}
