<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Tests\Unit\Levels;

use PHPUnit\Framework\TestCase;
use RunStack\Obfuscator\Levels\Level1Minifier;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Testes unitários para o Nível 1 de ofuscação (Minificação).
 * Verifica se comentários são removidos corretamente da AST.
 */
class Level1MinifierTest extends TestCase
{
    private Level1Minifier $minifier;
    private \PhpParser\Parser $parser;
    private Standard $printer;

    protected function setUp(): void
    {
        $this->minifier = new Level1Minifier();
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();
    }

    public function testRemoveComments(): void
    {
        $codeWithComments = <<<'PHP'
<?php
/**
 * Comentário de docblock
 */
function soma($a, $b) {
    // Comentário inline
    return $a + $b; // Outro comentário
}
PHP;

        $ast = $this->parser->parse($codeWithComments);
        $processedAst = $this->minifier->process($ast);

        $result = $this->printer->prettyPrintFile($processedAst);

        $this->assertStringNotContainsString('Comentário de docblock', $result);
        $this->assertStringNotContainsString('// Comentário inline', $result);
        $this->assertStringNotContainsString('// Outro comentário', $result);

        // Verifica que a funcionalidade permanece intacta
        $this->assertStringContainsString('function soma($a, $b)', $result);
        $this->assertStringContainsString('return $a + $b;', $result);
    }

    public function testEmptyCode(): void
    {
        $ast = $this->parser->parse('<?php ');
        $processedAst = $this->minifier->process($ast);

        $this->assertSame([], $processedAst);
    }
}