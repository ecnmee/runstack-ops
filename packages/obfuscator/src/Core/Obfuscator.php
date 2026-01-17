<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Core;

class Obfuscator
{
    /** @var int */
    private int $level;

    /** @var array<string, mixed> */
    private array $config;

    /** @var array<object> */
    private array $levels = [];

    /** @var Parser */
    private Parser $parser;

    /** @var Writer */
    private Writer $writer;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->level  = (int) ($this->config['level'] ?? 1);

        $this->parser = new Parser();
        $this->writer = new Writer();

        $available = $this->detectAvailableLevels();

        // ✅ VALIDAÇÃO CRÍTICA (corrige o teste)
        if ($this->level < 1 || $this->level > $available) {
            throw new \RuntimeException(
                "Nível de ofuscação inválido: {$this->level}. Máximo disponível: {$available}"
            );
        }

        $this->loadLevels();
    }

    public function obfuscateFile(string $input, string $output): bool
    {
        if (!file_exists($input)) {
            throw new \RuntimeException("Ficheiro de entrada não encontrado: {$input}");
        }

        $code       = file_get_contents($input);
        $obfuscated = $this->obfuscateCode($code);

        $this->ensureDirectory($output);

        return file_put_contents($output, $obfuscated) !== false;
    }

    public function obfuscateCode(string $code): string
    {
        $ast = $this->parser->parse($code);

        foreach ($this->levels as $level) {
            $ast = $level->process($ast);
        }

        return $this->writer->generate($ast);
    }

    private function loadLevels(): void
    {
        for ($i = 1; $i <= $this->level; $i++) {
            $class = $this->getLevelClass($i);
            $this->levels[] = new $class($this->config);
        }
    }

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
        return 2;
    }

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

    private function ensureDirectory(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function getDefaultConfig(): array
    {
        return [
            'level'           => 1,
            'preserve_public' => true,
            'strip_comments'  => true,
        ];
    }
}
