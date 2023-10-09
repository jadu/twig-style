<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Sniff;

use TwigCsFixer\Sniff\AbstractSniff;
use TwigCsFixer\Token\Token;

/**
 * Replaces usages of the {% spaceless %} tag with {% apply spaceless %}.
 * Replaces usages of the {% endspaceless %} tag with {% endapply %}.
 */
final class NoSpacelessTagSniff extends AbstractSniff
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

        $error = false;
        if (
            $this->isTokenMatching($token, Token::BLOCK_TAG_TYPE)
            && in_array($token->getValue(), ['spaceless', 'endspaceless'], true)
        ) {
            $error = true;
            $fixer = $this->addFixableError(
                sprintf('Unexpected "%s" tag', $token->getValue()),
                $token
            );
        }

        // No errors found
        if (!$error) {
            return;
        }

        // Dry run
        if ($fixer === null) {
            return;
        }

        $fixer->replaceToken($tokenPosition, $this->getReplacementToken($token));
    }

    /**
     * @param Token $token
     *
     * @return string
     */
    private function getReplacementToken(Token $token): string
    {
        switch ($token->getValue()) {
            case 'spaceless':
                return 'apply spaceless';
            case 'endspaceless':
                return 'endapply';
            default:
                return '';
        }
    }
}
