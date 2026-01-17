# RunStack Obfuscator

Biblioteca PHP para ofuscação de código fonte em multiplos níveis.  
**Versao open-source**: apenas níveis 1 e 2. Nível 3 será disponível em breve.  
Versões avancadas (4-6) disponíveis na edição premium entre em contacto com autor para mais informações.

## Niveis incluidos nesta versao

| Nivel | Nome                     | O que faz                                      | Redução típica | Nível de proteção | Preserva                              |
|-------|--------------------------|------------------------------------------------|----------------|-------------------|---------------------------------------|
| 1     | Minificação              | Remove comentarios, docblocks e espacos extras | 15-30%         | Baixa             | Todo o código funcional               |
| 2     | Renomeacão de variaveis  | Renomeia variaveis locais e parametros         | 10-25%         | Media-baixa       | `$this`, superglobals, nomes `_0x*`, metodos/propriedades publicas |

## Requisitos

- PHP >= 8.0
- Composer
- Extensoes recomendadas: `mbstring`, `openssl`

## Instalação

```bash
cd packages/obfuscator
composer install
```

Ou como dependencia no teu projeto:

```bash
composer require runstack/obfuscator:dev-main
```

## Uso basico

```php
<?php
require 'vendor/autoload.php';
use RunStack\Obfuscator\Core\Obfuscator;

// Codigo a ofuscar
$codigo = file_get_contents('meu_arquivo.php');

// Nivel 1 - apenas minificacao
$obf1 = (new Obfuscator(['level' => 1]))->obfuscateCode($codigo);

// Nivel 2 - minificacao + renomeacao
$obf2 = (new Obfuscator(['level' => 2]))->obfuscateCode($codigo);

// Ofuscar ficheiro diretamente
(new Obfuscator(['level' => 2]))->obfuscateFile('entrada.php', 'saida_ofuscada.php');
```

## Exemplos prontos

Na pasta `examples/obfuscator/` tens scripts de teste manuais:

```bash
cd examples/obfuscator

# Copia o exemplo (ou edita diretamente)
copy test_input.php.example test_input.php   # Windows
cp test_input.php.example test_input.php     # Linux/macOS

# Executa
php test_level1.php
php test_level2.php
```

Resultados gerados:
- `test_level1_output.php`
- `test_level2_output.php`
- `level1_output.php`

## Como funciona (resumo tecnico)

1. **Analise** - Usa `nikic/php-parser` para gerar AST.
2. **Transformacões**:
   - Nível 1: Visitor remove todos os comentarios.
   - Nível 2: Visitor renomeia variaveis por escopo (deterministico via NameGenerator).
3. **Geração** - `PhpParser\PrettyPrinter\Standard` converte AST de volta para codigo.

### Preservações garantidas:
- `$this`
- Superglobals (`$_GET`, `$_POST`, etc.)
- Nomes que ja comecam por `_0x`
- Metodos e propriedades públicas (não renomeados)

## Testes

```bash
cd packages/obfuscator
vendor/bin/phpunit
```

- Unitários: `tests/Unit/`
- Integração: `tests/Integration/`

## Licenca

MIT - ver LICENSE

## Contribuicoes

Pull requests bem-vindos para:
- Melhorias nos niveis 1-2
- Mais testes
- Correções
- Documentação em português

## Níveis pagos (não incluidos aqui)

- **Nível 3**: Ofuscacao de strings (base64, hex, chr)
- **Nível 4-6**: Criptografia AES, dead code, flow obfuscation, polymorphic engine

Mais informacoes contacte o autor

---

Feito por Eduardo Costa Nkuansambu