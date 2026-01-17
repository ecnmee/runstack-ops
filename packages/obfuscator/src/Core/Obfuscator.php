<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Core;

/**
 * Classe principal do motor de ofuscação RunStack Obfuscator.
 * Responsável por coordenar o processo de ofuscação em múltiplos níveis,
 * utilizando transformações na Árvore de Sintaxe Abstrata (AST).
 *
 * @package RunStack\Obfuscator
 */
class Obfuscator
{
    /** @var int Nível máximo de ofuscação a ser aplicado */
    private int $level;

    /** @var array<string, mixed> Opções de configuração */
    private array $config;

    /** @var array<object> Instâncias dos processadores de cada nível carregado */
    private array $levels = [];

    /** @var Parser Responsável por converter código em AST */
    private Parser $parser;

    /** @var Writer Responsável por converter AST de volta para código PHP */
    private Writer $writer;

    /**
     * Construtor - Inicializa o ofuscador com as configurações fornecidas.
     *
     * @param array<string, mixed> $config Configurações (level, preserve_public, etc.)
     * @throws \RuntimeException Se o nível solicitado for inválido ou exceder o máximo disponível
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->level = (int) ($this->config['level'] ?? 1);

        $this->parser = new Parser();
        $this->writer = new Writer();

        $available = $this->detectAvailableLevels();

        if ($this->level < 1 || $this->level > $available) {
            throw new \RuntimeException(
                "Nível de ofuscação inválido: {$this->level}. " .
                "Máximo disponível: {$available}"
            );
        }

        $this->loadLevels();
    }

    /**
     * Ofusca um ficheiro PHP único e grava o resultado no caminho especificado.
     *
     * @param string $input  Caminho para o ficheiro de entrada
     * @param string $output Caminho onde o ficheiro ofuscado será salvo
     * @return bool Verdadeiro em caso de sucesso, falso em caso de falha
     * @throws \RuntimeException Se o ficheiro de entrada não existir
     */
    public function obfuscateFile(string $input, string $output): bool
    {
        if (!file_exists($input)) {
            throw new \RuntimeException("Ficheiro de entrada não encontrado: {$input}");
        }

        $code = file_get_contents($input);
        $obfuscated = $this->obfuscateCode($code);

        $this->ensureDirectory($output);

        return file_put_contents($output, $obfuscated) !== false;
    }

    /**
     * Ofusca uma string contendo código PHP fonte.
     *
     * @param string $code Código fonte PHP original
     * @return string Código PHP ofuscado
     */
    public function obfuscateCode(string $code): string
    {
        $ast = $this->parser->parse($code);

        foreach ($this->levels as $level) {
            $ast = $level->process($ast);
        }

        return $this->writer->generate($ast);
    }

    /**
     * Carrega as classes dos níveis de ofuscação conforme o nível configurado.
     */
    private function loadLevels(): void
    {
        for ($i = 1; $i <= $this->level; $i++) {
            $class = $this->getLevelClass($i);
            $this->levels[] = new $class($this->config);
        }
    }

    /**
     * Detecta o nível máximo de ofuscação disponível com base na existência de classes.
     *
     * @return int Nível máximo disponível (2–6 dependendo dos pacotes instalados)
     */
    private function detectAvailableLevels(): int
    {
        if (class_exists('RunStack\\Obfuscator\\Enterprise\\Levels\\Level6Polymorphic')) {
            return 6;
        }
        if (class_exists('RunStack\\Obfuscator\\Premium\\Levels\\Level4Encryptor')) {
            return 5;
        }
        if (class_exists('RunStack\\Obfuscator\\Basic\\Levels\\Level3StringObfuscator')) {
            return 3;
        }
        return 2; // Padrão freemium
    }

    /**
     * Retorna o nome completo da classe correspondente ao nível de ofuscação solicitado.
     *
     * @param int $level Nível de ofuscação (1–6)
     * @return string Nome completo da classe
     * @throws \RuntimeException Se o nível for inválido
     */
    private function getLevelClass(int $level): string
    {
        return match ($level) {
            1 => 'RunStack\\Obfuscator\\Levels\\Level1Minifier',
            2 => 'RunStack\\Obfuscator\\Levels\\Level2Renamer',
            3 => 'RunStack\\Obfuscator\\Basic\\Levels\\Level3StringObfuscator',
            4 => 'RunStack\\Obfuscator\\Premium\\Levels\\Level4Encryptor',
            5 => 'RunStack\\Obfuscator\\Premium\\Levels\\Level5DeadCode',
            6 => 'RunStack\\Obfuscator\\Enterprise\\Levels\\Level6Polymorphic',
            default => throw new \RuntimeException("Nível de ofuscação inválido: {$level}")
        };
    }

    /**
     * Garante que o diretório de destino exista, criando-o se necessário.
     *
     * @param string $path Caminho completo do ficheiro de saída
     */
    private function ensureDirectory(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Retorna a configuração padrão do ofuscador.
     *
     * @return array<string, mixed> Configuração padrão
     */
    private function getDefaultConfig(): array
    {
        return [
            'level' => 1,
            'preserve_public' => true,
            'strip_comments' => true,
        ];
    }
}