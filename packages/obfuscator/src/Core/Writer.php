<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Core;

use PhpParser\PrettyPrinter\Standard;

/**
 * Classe responsável por converter uma AST de volta para código PHP legível.
 */
class Writer
{
    /** @var Standard Instância do PrettyPrinter utilizada para gerar o código */
    private Standard $printer;

    /**
     * Construtor - Inicializa o pretty printer padrão.
     */
    public function __construct()
    {
        $this->printer = new Standard();
    }

    /**
     * Gera código PHP a partir de uma AST.
     *
     * @param array $ast Array de nós da AST
     * @return string Código PHP formatado
     */
    public function generate(array $ast): string
    {
        return $this->printer->prettyPrintFile($ast);
    }
}