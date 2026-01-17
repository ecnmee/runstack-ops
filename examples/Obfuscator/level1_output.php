<?php

class Calculadora
{
    private int $memoria = 0;
    public function somar(int $a, int $b) : int
    {
        $resultado = $a + $b;
        $this->memoria = $resultado;
        return $resultado;
    }
    public function multiplicar(int $x, int $y) : int
    {
        return $x * $y;
    }
    public function obterMemoria() : int
    {
        return $this->memoria;
    }
}
$calc = new \Calculadora();
echo "Soma: " . $calc->somar(15, 25) . "\n";
echo "Produto: " . $calc->multiplicar(4, 6) . "\n";
echo "Memória: " . $calc->obterMemoria() . "\n";