<?php

declare(strict_types=1);

namespace RunStack\Obfuscator\Utils;

/**
 * Classe utilitária para gerenciar palavras reservadas do PHP.
 * Usada para evitar conflitos ao renomear variáveis, funções ou propriedades
 * durante o processo de ofuscação.
 */
class ReservedWords
{
    /**
     * Lista de palavras reservadas do PHP que não devem ser usadas como nomes
     * de variáveis, funções, classes, etc.
     *
     * @var array<string>
     */
    private static array $reserved = [
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable',
        'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default',
        'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends',
        'false', 'final', 'finally', 'for', 'foreach', 'function', 'global', 'goto',
        'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof',
        'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private',
        'protected', 'public', 'require', 'require_once', 'return', 'static',
        'switch', 'throw', 'trait', 'true', 'try', 'unset', 'use', 'var', 'while',
        'xor', 'yield', 'yield from',
    ];

    /**
     * Verifica se um nome é uma palavra reservada do PHP.
     *
     * @param string $name Nome a ser verificado
     * @return bool Verdadeiro se for palavra reservada, falso caso contrário
     */
    public static function isReserved(string $name): bool
    {
        return in_array(strtolower($name), self::$reserved, true);
    }

    /**
     * Retorna a lista completa de palavras reservadas conhecidas.
     *
     * @return array<string> Lista de palavras reservadas
     */
    public static function getAll(): array
    {
        return self::$reserved;
    }

    /**
     * Adiciona palavras reservadas personalizadas (útil para extensões ou contextos específicos).
     *
     * @param array<string> $additional Palavras adicionais a considerar reservadas
     */
    public static function addCustom(array $additional): void
    {
        self::$reserved = array_merge(self::$reserved, array_map('strtolower', $additional));
        self::$reserved = array_unique(self::$reserved);
    }
}