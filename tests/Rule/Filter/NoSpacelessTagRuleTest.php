<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Filter;

use Jadu\Style\Twig\Rule\Filter\NoSpacelessTagRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class NoSpacelessTagRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new NoSpacelessTagRule(), [
            'NoSpacelessTag.Error:1:4' => 'Unexpected "spaceless" tag',
            'NoSpacelessTag.Error:2:4' => 'Unexpected "endspaceless" tag',
        ]);
    }
}
