<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Rule\Development;

use ReflectionClass;
use TwigCsFixer\Rules\AbstractRule;
use TwigCsFixer\Token\Token;

/**
 * This rule is intended for development purposes only.
 *
 * Use this rule to tokenize a Twig template and generate a report mapping token types to values.
 */
final class TokenTypeRule extends AbstractRule
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

        $tokenClass = new ReflectionClass(Token::class);
        $tokenTypeName = array_search($token->getType(), $tokenClass->getConstants());

        if ($tokenTypeName !== false) {
            $this->addWarning(
                sprintf('%s: "%s"', $tokenTypeName, $token->getValue()),
                $token
            );
        } else {
            $this->addError(
                sprintf('Could not find token type for token: "%s"', $token->getValue()),
                $token
            );
        }
    }
}
