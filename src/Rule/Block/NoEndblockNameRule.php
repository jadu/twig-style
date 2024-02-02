<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Block;

use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;

/**
 * Ensure that an endblock tag has no name.
 */
final class NoEndblockNameRule extends AbstractRule
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
            // Ignore new line
            $next = $this->findNext(Token::INDENT_TOKENS, $tokens, $tokenPosition + 1, true);
            if (false === $next || $this->isTokenMatching($tokens[$next], Token::EOL_TOKENS)) {
                return;
            }

            $error = false;
            $nextPosition = $tokenPosition + 1;
            while (!$this->isTokenMatching($tokens[$nextPosition], Token::BLOCK_END_TYPE)) {
                $error = $this->isTokenMatching($tokens[$nextPosition], Token::NAME_TYPE);
                if ($error) {
                    $fixer = $this->addFixableError(
                        sprintf('Unexpected block name "%s" after %s', $tokens[$nextPosition]->getValue(), $token->getValue()),
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

            $fixer->replaceToken($nextPosition, '');
        }
    }
}
