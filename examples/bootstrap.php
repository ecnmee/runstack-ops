<?php

$autoloadCandidates = [
    // Estrutura atual confirmada
    __DIR__ . '/../src/obfuscator/vendor/autoload.php',

    // Estrutura alternativa futura (packages)
    __DIR__ . '/../packages/obfuscator/vendor/autoload.php',

    // Fallback extra (caso bootstrap seja movido)
    dirname(__DIR__) . '/src/obfuscator/vendor/autoload.php',
];

$autoloadPath = null;

foreach ($autoloadCandidates as $candidate) {
    if (is_file($candidate)) {
        $autoloadPath = realpath($candidate);
        break;
    }
}

if ($autoloadPath === null) {
    throw new RuntimeException(
        "Autoload não encontrado. Caminhos testados:\n" .
        implode("\n", $autoloadCandidates)
    );
}

require $autoloadPath;
