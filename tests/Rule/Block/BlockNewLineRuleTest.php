<?php

declare(strict_types=1);

namespace Jadu\Style\Twig\Tests\Rules\Block;

use Jadu\Style\Twig\Rule\Block\BlockNewLineRule;
use TwigCsFixer\Test\AbstractRuleTestCase;

final class BlockNewLineRuleTest extends AbstractRuleTestCase
{
    public function testRule(): void
    {
        $this->checkRule(new BlockNewLineRule(), [
            'BlockNewLine.Error:3:12' => 'A block must start with 1 new line; found 0',
            'BlockNewLine.Error:6:16' => 'A block must end with 1 new line; found 0',
            'BlockNewLine.Error:7:16' => 'A block must start with 1 new line; found 0',
            'BlockNewLine.Error:8:16' => 'A block must end with 1 new line; found 0',
            'BlockNewLine.Error:9:16' => 'A block must start with 1 new line; found 0',
            'BlockNewLine.Error:10:16' => 'A block must end with 1 new line; found 0',
            'BlockNewLine.Error:11:12' => 'A block must end with 1 new line; found 0',
        ]);
    }
}
