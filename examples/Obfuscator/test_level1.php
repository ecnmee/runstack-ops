<?php

/**
 * TESTE RunStack Obfuscator - Nivel 1 (Minificacao)
 * 
 * Le codigo de test_input.php, aplica minificacao (remove comentarios e espacos)
 * e grava o resultado em test_level1_output.php
 * 
 * Como usar:
 *   1. Crie um ficheiro test_input.php com o codigo PHP a testar
 *   2. Execute: php test_level1.php
 */

require __DIR__ . '/../bootstrap.php';

use RunStack\Obfuscator\Core\Obfuscator;

echo "RunStack Obfuscator - Teste Nivel 1\n";
echo str_repeat("=", 60) . "\n\n";

try {
    $inputFile = 'test_input.php';

    if (!file_exists($inputFile)) {
        throw new RuntimeException(
            "Ficheiro de entrada nao encontrado: {$inputFile}\n" .
            "Crie um ficheiro test_input.php com o codigo PHP que deseja testar."
        );
    }

    $originalCode = file_get_contents($inputFile);

    $obfuscator = new Obfuscator([
        'level' => 1,
        'strip_comments' => true,
    ]);

    echo "Codigo Original (lido de {$inputFile}):\n";
    echo str_repeat("-", 60) . "\n";
    echo $originalCode . "\n\n";

    echo "Aplicando Nivel 1 (Minificacao)...\n\n";
    $obfuscated = $obfuscator->obfuscateCode($originalCode);

    echo "Codigo Ofuscado (Nivel 1):\n";
    echo str_repeat("-", 60) . "\n";
    echo $obfuscated . "\n\n";

    // Estatisticas
    echo "Comparacao:\n";
    echo str_repeat("-", 60) . "\n";
    echo "Original:  " . strlen($originalCode) . " bytes\n";
    echo "Ofuscado:  " . strlen($obfuscated) . " bytes\n";
    $reduction = round((1 - strlen($obfuscated) / strlen($originalCode)) * 100, 2);
    echo "Reducao:   {$reduction}%\n\n";

    // Salvar ficheiros
    file_put_contents('test_level1_input.php', $originalCode);
    file_put_contents('test_level1_output.php', $obfuscated);

    echo "Ficheiros gerados:\n";
    echo "   - test_level1_input.php  -> copia do original\n";
    echo "   - test_level1_output.php -> codigo minificado (Nivel 1)\n\n";

    echo "Teste Nivel 1 concluido com sucesso\n";

} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Ficheiro: " . $e->getFile() . " (linha " . $e->getLine() . ")\n\n";
    echo $e->getTraceAsString() . "\n";
}