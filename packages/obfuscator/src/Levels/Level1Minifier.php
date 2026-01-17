<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Levels;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * Nível 1 de ofuscação: Minificação
 * Remove comentários, docblocks e espaços em branco desnecessários da AST.
 */
class Level1Minifier
{
    /** @var array<string, mixed> Configurações do nível (ex: opções de preservação) */
    private array $config;

    /**
     * Construtor - Recebe configurações opcionais para o processo de minificação.
     *
     * @param array<string, mixed> $config Configurações (pode ser vazio)
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Processa a AST aplicando a minificação (remoção de comentários).
     *
     * @param array $ast Árvore de sintaxe abstrata original
     * @return array AST modificada sem comentários
     */
    public function process(array $ast): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new CommentRemover());

        return $traverser->traverse($ast);
    }
}

/**
 * Visitor responsável por remover todos os comentários da AST.
 * Limpa o atributo 'comments' de cada nó.
 */
class CommentRemover extends NodeVisitorAbstract
{
    /**
     * Executado ao entrar em cada nó da AST.
     * Remove todos os comentários associados ao nó atual.
     *
     * @param Node $node Nó atual da árvore
     * @return null|Node|array|null Retorna null para continuar a travessia
     */
    public function enterNode(Node $node)
    {
        $node->setAttribute('comments', []);

        return null;
    }
}