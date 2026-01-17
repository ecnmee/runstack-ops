<?php

/**
 * TESTE RunStack Obfuscator - Nivel 2 (Minificacao + Renomeacao)
 * 
 * Le codigo de test_input.php, aplica:
 *   - Nivel 1: Minificacao
 *   - Nivel 2: Renomeacao de variaveis
 * 
 * Gera dois ficheiros de saida:
 *   - level1_output.php
 *   - test_level2_output.php
 * 
 * Como usar:
 *   1. Crie um ficheiro test_input.php com o codigo PHP que deseja testar
 *   2. Execute: php test_level2.php
 */

require __DIR__ . '/../bootstrap.php';

use RunStack\Obfuscator\Core\Obfuscator;

echo "RunStack Obfuscator - Teste Nivel 2\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $inputFile = 'test_input.php';

    if (!file_exists($inputFile)) {
        throw new RuntimeException(
            "Ficheiro de entrada nao encontrado: {$inputFile}\n" .
            "Crie um ficheiro test_input.php com o codigo PHP que deseja testar."
        );
    }

    $code = file_get_contents($inputFile);

    echo "CODIGO ORIGINAL (lido de {$inputFile}):\n";
    echo str_repeat("-", 70) . "\n";
    echo $code . "\n\n";

    // Nivel 1
    echo "NIVEL 1 (Minificacao)...\n\n";
    $obfuscator1 = new Obfuscator(['level' => 1]);
    $level1 = $obfuscator1->obfuscateCode($code);

    echo "CODIGO NIVEL 1:\n";
    echo str_repeat("-", 70) . "\n";
    echo $level1 . "\n\n";

    // Nivel 2
    echo "NIVEL 2 (Minificacao + Renomeacao)...\n\n";
    $obfuscator2 = new Obfuscator(['level' => 2]);
    $level2 = $obfuscator2->obfuscateCode($code);

    echo "CODIGO NIVEL 2 (OFUSCADO):\n";
    echo str_repeat("-", 70) . "\n";
    echo $level2 . "\n\n";

    // Comparacao
    echo "COMPARACAO:\n";
    echo str_repeat("-", 70) . "\n";
    echo "Original: " . strlen($code) . " bytes | " . (substr_count($code, "\n") + 1) . " linhas\n";
    echo "Nivel 1:  " . strlen($level1) . " bytes | " . (substr_count($level1, "\n") + 1) . " linhas | " .
         round((1 - strlen($level1) / strlen($code)) * 100, 2) . "% reducao\n";
    echo "Nivel 2:  " . strlen($level2) . " bytes | " . (substr_count($level2, "\n") + 1) . " linhas | " .
         round((1 - strlen($level2) / strlen($code)) * 100, 2) . "% reducao\n\n";

    // Salvar ficheiros
    file_put_contents('level1_output.php', $level1);
    file_put_contents('test_level2_output.php', $level2);

    echo "FICHEIROS GERADOS:\n";
    echo str_repeat("-", 70) . "\n";
    echo "   - level1_output.php        -> apenas minificado\n";
    echo "   - test_level2_output.php   -> minificado + variaveis renomeadas\n\n";

    // Validacao de sintaxe
    echo "VALIDANDO SINTAXE DO NIVEL 2...\n";
    exec('php -l test_level2_output.php 2>&1', $output, $returnCode);

    if ($returnCode === 0) {
        echo "   Sintaxe valida\n\n";
    } else {
        echo "   Erro de sintaxe detectado:\n\n";
        echo implode("\n", $output) . "\n\n";
    }

    // Teste de execucao
    echo "TESTANDO EXECUCAO DO CODIGO OFUSCADO (Nivel 2)...\n";
    echo str_repeat("-", 70) . "\n";

    ob_start();
    eval('?>' . $level2);
    $executionOutput = ob_get_clean();

    echo "Saida obtida:\n";
    echo $executionOutput . "\n";

    echo str_repeat("=", 70) . "\n";
    echo "TESTE NIVEL 2 CONCLUIDO\n";
    echo str_repeat("=", 70) . "\n\n";

    echo "RESUMO DAS MUDANCAS:\n";
    echo "   - Nivel 1 -> remove comentarios e espacos\n";
    echo "   - Nivel 2 -> remove comentarios + renomeia variaveis locais e parametros\n";
    echo "   - Preservados: \$this, metodos publicos, propriedades publicas\n\n";

    echo "PROXIMO PASSO:\n";
    echo "   Experimente o Nivel 3 (ofuscacao de strings) quando estiver pronto.\n\n";

} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Ficheiro: " . $e->getFile() . " (linha " . $e->getLine() . ")\n\n";
    echo $e->getTraceAsString() . "\n";
}