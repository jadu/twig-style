<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Punctuation;

use Jadu\Style\Twig\Rule\Punctuation\PunctuationSpacingRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class PunctuationSpacingRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new PunctuationSpacingRule(), [
            'PunctuationSpacing.After:3:4' => 'Expecting 0 whitespace after "("; found 1.',
            'PunctuationSpacing.Before:3:10' => 'Expecting 0 whitespace before ")"; found 1.',
            'PunctuationSpacing.After:4:4' => 'Expecting 1 whitespace after "{"; found 0.',
            'PunctuationSpacing.Before:4:11' => 'Expecting 0 whitespace before ":"; found 1.',
            'PunctuationSpacing.Before:4:19' => 'Expecting 0 whitespace before ","; found 1.',
            'PunctuationSpacing.Before:4:27' => 'Expecting 0 whitespace before ":"; found 1.',
            'PunctuationSpacing.Before:4:34' => 'Expecting 1 whitespace before "}"; found 0.',
            'PunctuationSpacing.After:5:12' => 'Expecting 0 whitespace after "["; found 1.',
            'PunctuationSpacing.Before:5:16' => 'Expecting 0 whitespace before ","; found 1.',
            'PunctuationSpacing.Before:5:20' => 'Expecting 0 whitespace before ","; found 1.',
            'PunctuationSpacing.Before:5:24' => 'Expecting 0 whitespace before "]"; found 1.',
            'PunctuationSpacing.After:6:5' => 'Expecting 1 whitespace after "{"; found 0.',
            'PunctuationSpacing.Before:6:15' => 'Expecting 0 whitespace before "]"; found 1.',
            'PunctuationSpacing.After:8:10' => 'Expecting 1 whitespace after "{"; found 0.',
            'PunctuationSpacing.Before:8:26' => 'Expecting 1 whitespace before "?:"; found 0.',
            'PunctuationSpacing.Before:8:37' => 'Expecting 1 whitespace before "}"; found 0.',
            'PunctuationSpacing.After:9:20' => 'Expecting 0 whitespace after "{"; found 1.',
            'PunctuationSpacing.Before:9:22' => 'Expecting 0 whitespace before "}"; found 1.',
        ]);
    }
}
