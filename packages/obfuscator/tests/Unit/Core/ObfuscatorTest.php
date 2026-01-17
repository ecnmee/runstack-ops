<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use RunStack\Obfuscator\Core\Obfuscator;
use RunStack\Obfuscator\Levels\Level1Minifier;
use RunStack\Obfuscator\Levels\Level2Renamer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Testes unitários para a classe principal Obfuscator.
 */
class ObfuscatorTest extends TestCase
{
    private Obfuscator $obfuscator;
    private \PhpParser\Parser $parser;
    private Standard $printer;

    protected function setUp(): void
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();

        // Configuração padrão: até nível 2
        $this->obfuscator = new Obfuscator([
            'level' => 2,
        ]);
    }

    public function testConstructorLoadsLevels(): void
    {
        $reflection = new \ReflectionClass($this->obfuscator);
        $levelsProperty = $reflection->getProperty('levels');
        $levelsProperty->setAccessible(true);

        $levels = $levelsProperty->getValue($this->obfuscator);

        $this->assertIsArray($levels);
        $this->assertCount(2, $levels);
        $this->assertInstanceOf(Level1Minifier::class, $levels[0]);
        $this->assertInstanceOf(Level2Renamer::class, $levels[1]);
    }

    public function testObfuscateCodeAppliesMinificationOnlyAtLevel1(): void
    {
        $code = <<<'PHP'
<?php
/**
 * Função de teste com comentário
 */
function soma($a, $b) {
    return $a + $b; // soma simples
}
PHP;

        // Testa SOMENTE Level 1
        $obfuscated = (new Obfuscator(['level' => 1]))->obfuscateCode($code);

        $this->assertStringNotContainsString('Função de teste com comentário', $obfuscated);
        $this->assertStringNotContainsString('// soma simples', $obfuscated);

        // Estrutura deve permanecer
        $this->assertStringContainsString('function soma($a, $b)', $obfuscated);
        $this->assertStringContainsString('return $a + $b;', $obfuscated);
    }

    public function testHigherLevelAppliesVariableRenamingAtLevel2(): void
    {
        $code = <<<'PHP'
<?php
function exemplo($valor) {
    $temp = $valor * 2;
    return $temp;
}
PHP;

        // Level 1: não renomeia variáveis
        $obfLevel1 = (new Obfuscator(['level' => 1]))->obfuscateCode($code);
        $this->assertStringContainsString('$valor', $obfLevel1);
        $this->assertStringContainsString('$temp', $obfLevel1);

        // Level 2: deve renomear variáveis
        $obfLevel2 = $this->obfuscator->obfuscateCode($code);
        $this->assertStringNotContainsString('$valor', $obfLevel2);
        $this->assertStringNotContainsString('$temp', $obfLevel2);

        // Espera padrão de nomes ofuscados
        $this->assertMatchesRegularExpression('/\\$_0x[a-f0-9]+/', $obfLevel2);
    }

    public function testInvalidLevelThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Nível de ofuscação/');

        new Obfuscator(['level' => 999]);
    }
}
