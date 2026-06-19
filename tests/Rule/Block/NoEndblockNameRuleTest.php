<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Block;

use Jadu\Style\Twig\Rule\Block\NoEndblockNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class NoEndblockNameRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new NoEndblockNameRule(), [
            'NoEndblockName.Error:7:16' => 'Unexpected block name "before_primary_supplements" after endblock',
            'NoEndblockName.Error:10:16' => 'Unexpected block name "primary_supplements" after endblock',
            'NoEndblockName.Error:13:16' => 'Unexpected block name "after_primary_supplements" after endblock',
            'NoEndblockName.Error:15:12' => 'Unexpected block name "aside_inner" after endblock',
            'NoEndblockName.Error:18:4' => 'Unexpected block name "aside" after endblock',
            'NoEndblockName.Error:20:58' => 'Unexpected block name "body_classes" after endblock',
            'NoEndblockName.Error:24:4' => 'Unexpected block name "aside_container" after endblock',
        ]);
    }
}
