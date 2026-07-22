<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Filter;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Replaces usages of the {% filter %} tag with {% apply %}.
 * Replaces usages of the {% endfilter %} tag with {% endapply %}.
 */
final class NoFilterTagRule extends AbstractFixableRule
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

        $error = false;
        if (
            $token->isMatching(Token::BLOCK_NAME_TYPE)
            && in_array($token->getValue(), ['filter', 'endfilter'], true)
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
            case 'filter':
                return 'apply';
            case 'endfilter':
                return 'endapply';
            default:
                return '';
        }
    }
}
