<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Core;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

/**
 * Classe responsável por converter código PHP em uma Árvore de Sintaxe Abstrata (AST)
 * e resolver nomes qualificados na AST.
 */
class Parser
{
    /** @var \PhpParser\Parser Instância do analisador PHP-Parser */
    private \PhpParser\Parser $parser;

    /**
     * Construtor - Inicializa o analisador preferindo sintaxe PHP 7.
     */
    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * Converte código fonte PHP em AST e resolve nomes.
     *
     * @param string $code Código fonte PHP a ser analisado
     * @return array Nós da AST resultante
     * @throws \RuntimeException Se a análise falhar
     */
    public function parse(string $code): array
    {
        $code = $this->normalizeCode($code);

        $ast = $this->parser->parse($code);

        if ($ast === null) {
            throw new \RuntimeException('Falha ao analisar o código PHP');
        }

        return $this->resolveNames($ast);
    }

    /**
     * Resolve nomes totalmente qualificados na AST usando o visitor NameResolver.
     *
     * @param array $ast AST inicial
     * @return array AST com nomes resolvidos
     */
    private function resolveNames(array $ast): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        return $traverser->traverse($ast);
    }

    /**
     * Normaliza o código fonte:
     * - Remove espaços em branco desnecessários no início/fim
     * - Garante que o código comece com a tag <?php
     *
     * @param string $code Código fonte bruto
     * @return string Código normalizado
     */
    private function normalizeCode(string $code): string
    {
        $code = trim($code);

        if (!str_starts_with($code, '<?php')) {
            $code = "<?php\n" . $code;
        }

        return $code;
    }
}