# CHANGELOG

Todas as mudanças notáveis do RunStack Obfuscator serão documentadas neste ficheiro.

O formato baseia-se em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), e este projeto usa [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-17

### Added
- Nível 1: Minificação (remoção de comentários e espaços)
- Nível 2: Renomeação de variáveis locais e parâmetros (com preservação de escopo)
- Scripts de teste manuais em examples/obfuscator/
- Testes unitários e de integração (PHPUnit)
- Documentação inicial (README.md)
- Suporte a Composer e PSR-4 autoload

### Fixed
- Bug de renomeação dupla em parâmetros (corrigido em Level2Renamer)
- Problemas de caminho no autoload dos scripts de exemplo

### Security
- Preservação obrigatória de $this, superglobals e nomes já ofuscados (_0x*)

[1.0.0]: https://github.com/ecnmee/runstack-ops/releases/tag/v1.0.0