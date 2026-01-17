<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Tests\Unit\Levels;

use PHPUnit\Framework\TestCase;
use RunStack\Obfuscator\Levels\Level2Renamer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Testes unitários para o Nível 2 de ofuscação (Renomeação de variáveis).
 * Verifica se variáveis locais são renomeadas corretamente e se preservações funcionam.
 */
class Level2RenamerTest extends TestCase
{
    private Level2Renamer $renamer;
    private \PhpParser\Parser $parser;
    private Standard $printer;

    protected function setUp(): void
    {
        $this->renamer = new Level2Renamer();
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();
    }

    public function testRenameLocalVariables(): void
    {
        $code = <<<'PHP'
<?php
function soma($a, $b) {
    $resultado = $a + $b;
    return $resultado;
}
PHP;

        $ast = $this->parser->parse($code);
        $processedAst = $this->renamer->process($ast);

        $result = $this->printer->prettyPrintFile($processedAst);

        $this->assertStringNotContainsString('$a', $result);
        $this->assertStringNotContainsString('$b', $result);
        $this->assertStringNotContainsString('$resultado', $result);

        // Verifica que $this não foi tocado (se houver classe)
        $this->assertStringContainsString('function soma(', $result);
        $this->assertStringContainsString('return ', $result);
    }

    public function testPreserveThisAndSuperglobals(): void
    {
        $code = <<<'PHP'
<?php
function teste() {
    $this->valor = $_GET['id'];
    return $this->valor;
}
PHP;

        $ast = $this->parser->parse($code);
        $processedAst = $this->renamer->process($ast);

        $result = $this->printer->prettyPrintFile($processedAst);

        $this->assertStringContainsString('$this->valor', $result);
        $this->assertStringContainsString('$_GET[\'id\']', $result);
    }
}