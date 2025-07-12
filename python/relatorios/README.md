# Sistema de Relatórios de Mídia

Este sistema gera relatórios PDF completos com dados de clipagem de mídia, incluindo gráficos e tabelas detalhadas.

## Instalação

### 1. Dependências do Sistema
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install python3 python3-pip python3-venv

# macOS
brew install python3
```

### 2. Instalação das Dependências Python
```bash
cd python/relatorios
pip install -r requirements.txt
```

### 3. Configuração
O sistema usa as seguintes variáveis de ambiente (opcional):
- `DB_HOST`: Host do banco de dados
- `DB_PORT`: Porta do banco de dados
- `DB_USERNAME`: Usuário do banco
- `DB_PASSWORD`: Senha do banco
- `DB_DATABASE`: Nome do banco

## Uso

### Teste do Sistema
```bash
cd python/relatorios
python test_connection.py
```

### Geração de Relatórios
```bash
cd python/relatorios
python main.py --cliente 123 --data_inicio 2024-01-01 --data_fim 2024-01-31 --output relatorio.pdf
```

## Estrutura do Projeto

- `main.py`: Script principal para gerar relatórios
- `database.py`: Gerenciamento de conexão e consultas ao banco
- `pdf_generator.py`: Geração de PDFs com ReportLab
- `chart_generator.py`: Geração de gráficos com Matplotlib/Plotly
- `config.py`: Configurações do sistema
- `test_connection.py`: Script de teste do sistema

## Funcionalidades

### Tipos de Mídia Suportados
- **Web**: Notícias de sites e portais
- **TV**: Notícias de televisão
- **Rádio**: Notícias de rádio
- **Impresso**: Notícias de jornais e revistas

### Relatórios Gerados
- Resumo executivo com gráficos
- Listagem detalhada de clipagens
- Análise de sentimentos
- Valores de retorno por mídia
- Distribuição por veículos

## Integração com Laravel

O sistema é integrado ao Laravel através do método `gerarRelatorioPDF` no `ClienteController`:

```php
// Exemplo de chamada
$comando = "cd $pythonDir && python main.py --cliente $clienteId --data_inicio $dataInicio --data_fim $dataFim --output $nomeArquivo --filtros $filtrosJson";
$resultado = shell_exec($comando);
```

## Troubleshooting

### Erro de Dependências
```bash
pip install --upgrade pip
pip install -r requirements.txt
```

### Erro de Conexão com Banco
1. Verifique se as credenciais estão corretas
2. Teste a conexão com `python test_connection.py`
3. Confirme se o PostgreSQL está acessível

### Erro de Permissões
```bash
chmod +x main.py
chmod +x test_connection.py
```

## Logs

O sistema gera logs detalhados durante a execução:
- Conexão com banco de dados
- Processamento de dados
- Geração de gráficos
- Criação do PDF

## Suporte

Para problemas técnicos, verifique:
1. Logs do sistema
2. Resultado do `test_connection.py`
3. Versões das dependências
4. Conectividade com o banco de dados 