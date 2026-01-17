<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Tests\Integration;

use PHPUnit\Framework\TestCase;
use RunStack\Obfuscator\Core\Obfuscator;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Testes de integração para o fluxo completo de ofuscação.
 * Verifica se o código ofuscado mantém funcionalidade e sintaxe válida.
 */
class ObfuscationFlowTest extends TestCase
{
    private \PhpParser\Parser $parser;
    private Standard $printer;

    protected function setUp(): void
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();
    }

    public function testFullFlowLevel1Minification(): void
    {
        $originalCode = <<<'PHP'
<?php
/**
 * Calculadora de exemplo
 */
class Calculadora {
    private $memoria = 0;

    public function somar($a, $b) {
        $resultado = $a + $b;
        $this->memoria = $resultado;
        return $resultado;
    }
}

$calc = new Calculadora();
echo $calc->somar(10, 20);
PHP;

        $obfuscator = new Obfuscator(['level' => 1]);
        $obfuscated = $obfuscator->obfuscateCode($originalCode);

        // Verifica remoção de comentários
        $this->assertStringNotContainsString('Calculadora de exemplo', $obfuscated);

        // Verifica que código ainda é executável
        $this->assertStringContainsString('class Calculadora', $obfuscated);
        $this->assertStringContainsString('$calc->somar(10, 20)', $obfuscated);
    }

    public function testFullFlowLevel2Renaming(): void
    {
        $originalCode = <<<'PHP'
<?php
function multiplicar($x, $y) {
    $produto = $x * $y;
    return $produto;
}

echo multiplicar(5, 8);
PHP;

        $obfuscator = new Obfuscator(['level' => 2]);
        $obfuscated = $obfuscator->obfuscateCode($originalCode);

        // Variáveis locais e parâmetros devem estar renomeadas
        $this->assertStringNotContainsString('$x', $obfuscated);
        $this->assertStringNotContainsString('$y', $obfuscated);
        $this->assertStringNotContainsString('$produto', $obfuscated);

        // Estrutura da função preservada
        $this->assertStringContainsString('function multiplicar(', $obfuscated);
        $this->assertStringContainsString('return ', $obfuscated);

        // Verifica padrão de nomes ofuscados
        $this->assertMatchesRegularExpression('/\$_0x[a-f0-9]{4}/', $obfuscated);
    }

    public function testObfuscatedCodeExecutesCorrectly(): void
    {
        $originalCode = <<<'PHP'
<?php
function soma($a, $b) {
    return $a + $b;
}

$resultado = soma(15, 30);
echo $resultado;
PHP;

        $obfuscator = new Obfuscator(['level' => 2]);
        $obfuscated = $obfuscator->obfuscateCode($originalCode);

        // Executa o código ofuscado em ambiente isolado
        ob_start();
        eval('?>' . $obfuscated);
        $output = ob_get_clean();

        $this->assertEquals("45", trim($output));
    }

    public function testObfuscationPreservesSyntax(): void
    {
        $originalCode = <<<'PHP'
<?php
class Teste {
    public function metodo() {
        echo "Olá";
    }
}

(new Teste())->metodo();
PHP;

        $obfuscator = new Obfuscator(['level' => 2]);
        $obfuscated = $obfuscator->obfuscateCode($originalCode);

        // Verifica sintaxe básica
        $this->assertStringContainsString('class Teste', $obfuscated);
        $this->assertStringContainsString('public function metodo()', $obfuscated);
        $this->assertStringContainsString('echo "Olá"', $obfuscated);
    }
}