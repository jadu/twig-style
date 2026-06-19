<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Filter;

use Jadu\Style\Twig\Rule\Filter\NoFilterTagRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class NoFilterTagRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new NoFilterTagRule(), [
            'NoFilterTag.Error:1:4' => 'Unexpected "filter" tag',
            'NoFilterTag.Error:3:4' => 'Unexpected "endfilter" tag',
        ]);
    }
}
