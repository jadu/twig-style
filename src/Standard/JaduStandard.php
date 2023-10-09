<?php

namespace Jadu\Style\Twig\Standard;

use Jadu\Style\Twig\Sniff\BlockSpacingSniff;
use Jadu\Style\Twig\Sniff\NoEndblockNameSniff;
use Jadu\Style\Twig\Sniff\NoSpacelessTagSniff;
use Jadu\Style\Twig\Sniff\PunctuationSpacingSniff;
use TwigCsFixer\Sniff\BlankEOFSniff;
use TwigCsFixer\Sniff\DelimiterSpacingSniff;
use TwigCsFixer\Sniff\EmptyLinesSniff;
use TwigCsFixer\Sniff\IndentSniff;
use TwigCsFixer\Sniff\OperatorSpacingSniff;
use TwigCsFixer\Sniff\SniffInterface;
use TwigCsFixer\Sniff\TrailingCommaSingleLineSniff;
use TwigCsFixer\Sniff\TrailingSpaceSniff;
use TwigCsFixer\Standard\StandardInterface;

class JaduStandard implements StandardInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSniffs(): array
    {
        return [
            new BlankEOFSniff(),
            new BlockSpacingSniff(),
            new DelimiterSpacingSniff(),
            new EmptyLinesSniff(),
            new IndentSniff(),
            new NoEndblockNameSniff(),
            new NoSpacelessTagSniff(),
            new OperatorSpacingSniff(),
            new PunctuationSpacingSniff(),
            new TrailingCommaSingleLineSniff(),
            new TrailingSpaceSniff(),
        ];
    }
}