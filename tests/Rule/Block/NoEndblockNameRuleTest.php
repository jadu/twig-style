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
            'NoEndblockName.Error:27:4' => 'Unexpected macro name "test" after endmacro',
            'NoEndblockName.Error:30:8' => 'Unexpected macro name "inner_macro" after endmacro',
            'NoEndblockName.Error:31:4' => 'Unexpected macro name "outer_macro" after endmacro',
            'NoEndblockName.Error:36:8' => 'Unexpected macro name "render_badge" after endmacro',
            'NoEndblockName.Error:40:4' => 'Unexpected block name "badges" after endblock',
        ]);
    }
}
