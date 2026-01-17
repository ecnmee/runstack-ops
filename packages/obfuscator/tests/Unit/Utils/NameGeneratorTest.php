<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use RunStack\Obfuscator\Utils\NameGenerator;

/**
 * Testes unitários para o gerador de nomes ofuscados.
 */
class NameGeneratorTest extends TestCase
{
    private NameGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new NameGenerator();
    }

    public function testDeterministicGeneration(): void
    {
        $name1 = $this->generator->generate('variavelTeste');
        $name2 = $this->generator->generate('variavelTeste');

        $this->assertSame($name1, $name2);
        $this->assertStringStartsWith('_0x', $name1);
    }

    public function testDifferentNamesGenerateDifferentObfuscated(): void
    {
        $name1 = $this->generator->generate('primeiro');
        $name2 = $this->generator->generate('segundo');

        $this->assertNotSame($name1, $name2);
    }

    public function testResetClearsMappings(): void
    {
        $this->generator->generate('teste');
        $this->generator->reset();
        $newName = $this->generator->generate('teste');

        $this->assertStringStartsWith('_0x', $newName);
        // Após reset, o contador volta a 0, então o primeiro nome gerado deve ser o mesmo inicial
    }
}