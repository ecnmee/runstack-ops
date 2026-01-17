<?php

/**
 * Ficheiro de exemplo para testar o RunStack Obfuscator
 * Este código pode ser editado livremente para simular scripts reais
 */

/**
 * Classe Calculadora de exemplo
 * Demonstra uso de classe, propriedades privadas e métodos públicos
 */
class Calculadora {
    /** @var int Armazena o último resultado */
    private int $memoria = 0;

    /**
     * Soma dois números e atualiza a memória
     *
     * @param int $a Primeiro número
     * @param int $b Segundo número
     * @return int Resultado da soma
     */
    public function somar(int $a, int $b): int
    {
        $resultado = $a + $b;
        $this->memoria = $resultado;
        return $resultado;
    }

    /**
     * Multiplica dois números
     *
     * @param int $x Fator 1
     * @param int $y Fator 2
     * @return int Produto
     */
    public function multiplicar(int $x, int $y): int
    {
        return $x * $y;
    }

    /**
     * Retorna o valor armazenado na memória
     *
     * @return int Último resultado
     */
    public function obterMemoria(): int
    {
        return $this->memoria;
    }
}

// Uso de demonstração
$calc = new Calculadora();

echo "Soma: " . $calc->somar(15, 25) . "\n";
echo "Produto: " . $calc->multiplicar(4, 6) . "\n";
echo "Memória: " . $calc->obterMemoria() . "\n";