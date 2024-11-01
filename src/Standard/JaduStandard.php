<?php

namespace Jadu\Style\Twig\Standard;

use Jadu\Style\Twig\Rule\Block\BlockNewLineRule;
use Jadu\Style\Twig\Rule\Block\EndblockNameRule;
use Jadu\Style\Twig\Rule\Filter\NoFilterTagRule;
use Jadu\Style\Twig\Rule\Filter\NoSpacelessTagRule;
use Jadu\Style\Twig\Rule\Punctuation\PunctuationSpacingRule;
use TwigCsFixer\Rules\Delimiter\BlockNameSpacingRule;
use TwigCsFixer\Rules\Delimiter\DelimiterSpacingRule;
use TwigCsFixer\Rules\Operator\OperatorNameSpacingRule;
use TwigCsFixer\Rules\Operator\OperatorSpacingRule;
use TwigCsFixer\Rules\Punctuation\TrailingCommaSingleLineRule;
use TwigCsFixer\Rules\RuleInterface;
use TwigCsFixer\Rules\Whitespace\BlankEOFRule;
use TwigCsFixer\Rules\Whitespace\EmptyLinesRule;
use TwigCsFixer\Rules\Whitespace\IndentRule;
use TwigCsFixer\Rules\Whitespace\TrailingSpaceRule;
use TwigCsFixer\Standard\StandardInterface;

class JaduStandard implements StandardInterface
{
    /**
     * @return RuleInterface[]
     */
    public function getRules(): array
    {
        return [
            new BlankEOFRule(),
            new BlockNameSpacingRule(),
            new BlockNewLineRule(),
            new DelimiterSpacingRule(),
            new EmptyLinesRule(),
            new EndblockNameRule(),
            new IndentRule(),
            new NoSpacelessTagRule(),
            new NoFilterTagRule(),
            new OperatorNameSpacingRule(),
            new OperatorSpacingRule(),
            new PunctuationSpacingRule(),
            new TrailingCommaSingleLineRule(),
            new TrailingSpaceRule(),
        ];
    }
}
