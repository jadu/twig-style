<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Block;

use Jadu\Style\Twig\Rule\Block\EndblockNameRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class EndblockNameRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new EndblockNameRule(), [
            'EndblockName.Error:7:16' => 'Missing endblock name "before_primary_supplements"',
            'EndblockName.Error:10:16' => 'Missing endblock name "primary_supplements"',
            'EndblockName.Error:13:16' => 'Missing endblock name "after_primary_supplements"',
            'EndblockName.Error:15:12' => 'Missing endblock name "aside_inner"',
            'EndblockName.Error:18:4' => 'Missing endblock name "aside"',
            'EndblockName.Error:20:58' => 'Missing endblock name "body_classes"',
            'EndblockName.Error:24:4' => 'Missing endblock name "aside_container"',
            'EndblockName.Error:27:4' => 'Missing endmacro name "test"',
            'EndblockName.Error:30:8' => 'Missing endmacro name "inner_macro"',
            'EndblockName.Error:31:4' => 'Missing endmacro name "outer_macro"',
            'EndblockName.Error:36:8' => 'Missing endmacro name "render_badge"',
            'EndblockName.Error:40:4' => 'Missing endblock name "badges"',
        ]);
    }
}
