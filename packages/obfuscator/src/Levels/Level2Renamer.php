<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Levels;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use RunStack\Obfuscator\Utils\NameGenerator;

/**
 * Nível 2 de ofuscação: Renomeação de Variáveis
 * Renomeia variáveis locais e parâmetros de funções/métodos para nomes ofuscados,
 * preservando escopo e evitando conflitos com palavras reservadas ou variáveis especiais.
 */
class Level2Renamer
{
    /** @var array<string, mixed> Configurações do nível (ex: preserve_superglobals) */
    private array $config;

    /** @var NameGenerator Gerador de nomes ofuscados determinísticos */
    private NameGenerator $nameGenerator;

    /**
     * Construtor - Inicializa o gerador de nomes e mescla configurações padrão.
     *
     * @param array<string, mixed> $config Configurações personalizadas
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'preserve_superglobals' => true,
            'preserve_magic' => true,
        ], $config);

        $this->nameGenerator = new NameGenerator();
    }

    /**
     * Processa a AST aplicando a renomeação de variáveis em todos os escopos.
     *
     * @param array $ast Árvore de sintaxe abstrata original
     * @return array AST com variáveis renomeadas
     */
    public function process(array $ast): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(
            new VariableRenamer($this->nameGenerator, $this->config)
        );

        return $traverser->traverse($ast);
    }
}

/**
 * Visitor que percorre a AST e renomeia variáveis locais e parâmetros,
 * mantendo consistência dentro de cada escopo funcional.
 */
class VariableRenamer extends NodeVisitorAbstract
{
    /** @var NameGenerator Gerador de nomes ofuscados */
    private NameGenerator $nameGenerator;

    /** @var array<string, mixed> Configurações de preservação */
    private array $config;

    /** @var array<int, array<string, string>> Mapeamento de nomes por escopo */
    private array $scopeMap = [];

    /** @var int[] Pilha de IDs de escopo atual */
    private array $scopeStack = [];

    /**
     * Construtor - Recebe o gerador de nomes e as configurações.
     *
     * @param NameGenerator $nameGenerator Gerador de nomes
     * @param array<string, mixed> $config Configurações
     */
    public function __construct(NameGenerator $nameGenerator, array $config)
    {
        $this->nameGenerator = $nameGenerator;
        $this->config = $config;
    }

    /**
     * Executado antes de percorrer toda a AST.
     * Reseta o gerador de nomes para um novo processo completo.
     *
     * @param array $nodes Nós raiz da AST
     * @return null
     */
    public function beforeTraverse(array $nodes)
    {
        $this->nameGenerator->reset();
        return null;
    }

    /**
     * Executado ao entrar em cada nó.
     * Cria novos escopos e renomeia variáveis quando aplicável.
     *
     * @param Node $node Nó atual
     * @return null|Node|array|null
     */
    public function enterNode(Node $node)
    {
        if ($this->isNewScope($node)) {
            $this->enterScope($node);
        }

        if ($node instanceof Node\Expr\Variable && is_string($node->name)) {
            $node->name = $this->rename($node->name);
        }

        return null;
    }

    /**
     * Executado ao sair de cada nó.
     * Remove o escopo atual da pilha quando sai de uma função/método.
     *
     * @param Node $node Nó atual
     * @return null|Node|array|null
     */
    public function leaveNode(Node $node)
    {
        if ($this->isNewScope($node)) {
            array_pop($this->scopeStack);
        }

        return null;
    }

    /**
     * Verifica se o nó representa o início de um novo escopo funcional.
     *
     * @param Node $node Nó a verificar
     * @return bool Verdadeiro se for um novo escopo
     */
    private function isNewScope(Node $node): bool
    {
        return $node instanceof Node\Stmt\Function_
            || $node instanceof Node\Stmt\ClassMethod
            || $node instanceof Node\Expr\Closure
            || $node instanceof Node\Expr\ArrowFunction;
    }

    /**
     * Cria um novo escopo e adiciona à pilha.
     *
     * @param Node $node Nó que define o escopo (função, método, etc.)
     */
    private function enterScope(Node $node): void
    {
        $id = spl_object_id($node);
        $this->scopeMap[$id] = [];
        $this->scopeStack[] = $id;
    }

    /**
     * Retorna o ID do escopo atual ou null se não houver escopo.
     *
     * @return int|null ID do escopo atual
     */
    private function currentScope(): ?int
    {
        return end($this->scopeStack) ?: null;
    }

    /**
     * Renomeia uma variável, preservando casos especiais e garantindo consistência no escopo.
     *
     * @param string $original Nome original da variável
     * @return string Nome renomeado (ofuscado ou preservado)
     */
    private function rename(string $original): string
    {
        // Preservações
        if ($this->shouldPreserve($original)) {
            return $original;
        }

        $scopeId = $this->currentScope();
        if ($scopeId === null) {
            return $original;
        }

        if (isset($this->scopeMap[$scopeId][$original])) {
            return $this->scopeMap[$scopeId][$original];
        }

        $newName = $this->nameGenerator->generate($original);
        $this->scopeMap[$scopeId][$original] = $newName;

        return $newName;
    }

    /**
     * Verifica se uma variável deve ser preservada (não renomeada).
     *
     * @param string $name Nome da variável (sem $)
     * @return bool Verdadeiro se deve ser preservada
     */
    private function shouldPreserve(string $name): bool
    {
        if ($name === 'this') {
            return true;
        }

        if (str_starts_with($name, '_0x')) {
            return true;
        }

        if ($this->config['preserve_superglobals']) {
            $superglobals = [
                'GLOBALS', '_SERVER', '_GET', '_POST',
                '_FILES', '_COOKIE', '_SESSION',
                '_REQUEST', '_ENV'
            ];
            if (in_array($name, $superglobals, true)) {
                return true;
            }
        }

        if ($this->config['preserve_magic'] && str_starts_with($name, '__')) {
            return true;
        }

        return false;
    }
}