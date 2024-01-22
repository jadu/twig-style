<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Sniff;

use TwigCsFixer\Sniff\AbstractSniff;
use TwigCsFixer\Token\Token;

/**
 * Ensure that an endblock tag has no name.
 */
final class NoEndblockNameSniff extends AbstractSniff
{
    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return void
     */
    protected function process(int $tokenPosition, array $tokens): void
    {
        $nameAfter = $this->getNameAfter($tokenPosition, $tokens);

        if ($nameAfter === true) {
            $this->checkNameAfter($tokenPosition, $tokens);
        }
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return bool|null
     */
    private function getNameAfter(int $tokenPosition, array $tokens): ?bool
    {
        $token = $tokens[$tokenPosition];

        if (
            $this->isTokenMatching($token, Token::BLOCK_TAG_TYPE)
            && $token->getValue() === 'endblock'
        ) {
            return true;
        }

        return null;
    }

    /**
     * @param int $tokenPosition
     * @param array<int, Token> $tokens
     *
     * @return void
     */
    private function checkNameAfter(int $tokenPosition, array $tokens): void
    {
        $token = $tokens[$tokenPosition];

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
