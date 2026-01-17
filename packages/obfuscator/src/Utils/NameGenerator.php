<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Utils;

/**
 * Gerador de nomes ofuscados determinísticos.
 * Produz nomes curtos no formato _0xXXXX baseados em um contador e no nome original,
 * garantindo que o mesmo nome original sempre gere o mesmo nome ofuscado.
 */
class NameGenerator
{
    /** @var array<string, string> Mapeamento de nomes originais para nomes ofuscados */
    private array $usedNames = [];

    /** @var int Contador sequencial para geração de nomes únicos */
    private int $counter = 0;

    /**
     * Gera um nome ofuscado para um nome original.
     * Se o nome original já foi processado, retorna o mesmo nome ofuscado anterior.
     *
     * @param string $originalName Nome original da variável/função/propriedade
     * @return string Nome ofuscado no formato _0xXXXX
     */
    public function generate(string $originalName): string
    {
        if (isset($this->usedNames[$originalName])) {
            return $this->usedNames[$originalName];
        }

        $this->counter++;

        // Gera valor hexadecimal determinístico baseado no contador
        $hex = str_pad(
            dechex(($this->counter * 16807) % 0xFFFF),
            4,
            '0',
            STR_PAD_LEFT
        );

        $name = '_0x' . $hex;

        // Armazena o mapeamento para reutilização
        $this->usedNames[$originalName] = $name;

        return $name;
    }

    /**
     * Reseta o gerador, limpando o mapeamento e o contador.
     * Útil quando se inicia um novo processo de ofuscação completo.
     */
    public function reset(): void
    {
        $this->usedNames = [];
        $this->counter = 0;
    }

    /**
     * Retorna todos os mapeamentos gerados até o momento.
     *
     * @return array<string, string> Mapeamento completo (original => ofuscado)
     */
    public function getMappings(): array
    {
        return $this->usedNames;
    }
}