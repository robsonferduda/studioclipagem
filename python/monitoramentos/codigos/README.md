# Sistema de Monitoramentos de Mídia

Este diretório contém a implementação Python dos monitoramentos de mídia para o Sistema Studio Clipagem.

## Estrutura dos Arquivos

```
python/monitoramentos/codigos/
├── __init__.py           # Módulo Python
├── config.py            # Configurações gerais
├── base.py              # Classe base para monitoramentos
├── web.py               # Monitoramento de notícias web
├── tv.py                # Monitoramento de notícias de TV
├── radio.py             # Monitoramento de notícias de rádio
├── impresso.py          # Monitoramento de notícias impressas
├── executor.py          # Executor principal
├── exemplo_uso.py       # Exemplos de uso
└── README.md            # Este arquivo
```

## Configuração

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto com as seguintes variáveis:

```bash
# Banco de dados
DB_DATABASE=studio_clipagem
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
DB_HOST=localhost
DB_PORT=5432

# Email
SMTP_HOST=localhost
SMTP_FROM=boletins@clipagens.com.br
SMTP_TO_FALLBACK=admin@clipagens.com.br

# Busca
TS_CONFIG=simple
INTERVALO_HORAS=4
```

### Dependências

```bash
pip install psycopg2-binary python-dotenv
```

## Uso

### 1. Execução Individual por Tipo

```bash
# Monitoramento Web
python -m monitoramentos.codigos.web

# Monitoramento TV
python -m monitoramentos.codigos.tv

# Monitoramento Rádio
python -m monitoramentos.codigos.radio

# Monitoramento Impressos
python -m monitoramentos.codigos.impresso
```

### 2. Execução com Grupos

```bash
# Web do grupo 1
python -m monitoramentos.codigos.web -g 1

# TV do grupo 2
python -m monitoramentos.codigos.tv -g 2
```

### 3. Executor Principal

```bash
# Executa todos os tipos
python -m monitoramentos.codigos.executor

# Executa tipos específicos
python -m monitoramentos.codigos.executor -t web tv

# Executa grupo específico
python -m monitoramentos.codigos.executor -g 1

# Executa todos os grupos
python -m monitoramentos.codigos.executor --all-grupos

# Lista tipos disponíveis
python -m monitoramentos.codigos.executor --list
```

## Funcionamento

### Fluxo de Execução

1. **Conexão**: Conecta ao banco PostgreSQL
2. **Busca Monitoramentos**: Busca monitoramentos ativos por tipo/grupo
3. **Execução de Queries**: Para cada monitoramento:
   - Executa query com full-text search
   - Aplica filtros (fontes, data, etc.)
   - Busca notícias das últimas 4 horas
4. **Associação**: Vincula notícias encontradas aos clientes
5. **Pós-processamento**: Calcula valores de retorno específicos
6. **Registro**: Registra execução na tabela `monitoramento_execucao`

### Tipos de Mídia

| Tipo | ID | Tabela Principal | Busca Por |
|------|----|-----------------|-----------| 
| Impressos | 1 | `noticia_impresso` | `sinopse_tsv` |
| Web | 2 | `noticias_web` | `conteudo_tsv` |
| Rádio | 3 | `noticia_radio` | `sinopse_tsv` |
| TV | 4 | `noticia_tv` | `sinopse_tsv` |

### Queries de Busca

#### Web (Otimizada com CTEs)
```sql
WITH noticias_fts AS (
    SELECT DISTINCT id_noticia_web
    FROM conteudo_noticia_web
    WHERE conteudo_tsv @@ websearch_to_tsquery('simple', 'expressao')
      AND created_at >= NOW() - INTERVAL '4 hours'
),
noticias_filtradas AS (
    SELECT n.id, n.id_fonte, n.url_noticia, n.data_noticia, n.titulo_noticia
    FROM noticias_web n
    INNER JOIN noticias_fts nf ON nf.id_noticia_web = n.id
    WHERE n.data_noticia BETWEEN 'inicio' AND 'fim'
      AND n.created_at >= NOW() - INTERVAL '4 hours'
)
SELECT DISTINCT ON (nf.titulo_noticia, nf.url_noticia, nf.id_fonte) ...
```

#### TV/Rádio/Impressos
```sql
SELECT DISTINCT ON (sinopse, emissora_id, programa_id) ...
FROM noticia_[tipo] 
WHERE created_at >= NOW() - INTERVAL '4 hours'
  AND dt_[campo_data] BETWEEN 'inicio' AND 'fim'
  AND sinopse_tsv @@ websearch_to_tsquery('simple', 'expressao')
```

## Programação no Cron

### Exemplos de Configuração

```bash
# Executa todos os monitoramentos a cada 2 horas
0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.executor

# Executa grupos de web separadamente (distribuído)
0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 1
15 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 2
30 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 3
45 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 4
50 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.web -g 5

# Executa outros tipos a cada 2 horas (sem grupos)
0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.tv
0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.radio  
0 */2 * * * cd /path/to/python && python -m monitoramentos.codigos.impresso
```

### Estratégias de Execução

1. **Simples**: Um cron que executa tudo
2. **Distribuída**: Grupos em horários diferentes para balancear carga
3. **Por Tipo**: Cada tipo de mídia em intervalos específicos
4. **Híbrida**: Web com grupos + outros tipos simples

## Logs e Monitoramento

### Formato dos Logs
```
[YYYY-MM-DD HH:MM:SS] [TIPO_MIDIA] Mensagem
```

### Exemplos de Saída
```
[2025-10-24 14:30:00] [Web] Processando 15 monitoramento(s) web
[2025-10-24 14:30:01] [Web] Monitoramento 123 | Total vinculado: 5
[2025-10-24 14:30:02] [Web] Monitoramento 124 | Total vinculado: 2
```

### Notificações de Erro

Em caso de erro, é enviado email automático com:
- Tipo de mídia
- ID do monitoramento
- Expressão de busca
- Detalhes do erro

## Estrutura das Classes

### BaseMonitoramento
Classe pai com funcionalidades comuns:
- Conexão/desconexão com banco
- Log padronizado  
- Envio de emails
- Registro de execução
- Verificação de duplicatas

### Classes Específicas
Cada tipo herda da base e implementa:
- `buscar_noticias()`: Query específica do tipo
- `pos_processar_noticia()`: Cálculos de valor específicos

## Performance

### Otimizações Implementadas

1. **CTEs para Web**: Busca FTS primeiro, depois aplica filtros
2. **Índices TSVector**: Busca otimizada por texto
3. **DISTINCT ON**: Remove duplicatas na query
4. **Filtros de Data**: Limita busca a últimas 4h
5. **Verificação de Duplicatas**: Evita vínculos duplicados

### Monitoramento de Performance

- Logs incluem tempo de execução
- Total de notícias vinculadas por execução  
- Histórico na tabela `monitoramento_execucao`

## Troubleshooting

### Problemas Comuns

1. **Erro de Conexão**: Verificar credenciais no `.env`
2. **Sem Monitoramentos**: Verificar `fl_ativo=true` e flags específicas
3. **Query Lenta**: Verificar índices TSVector
4. **Duplicatas**: Verificar lógica de detecção por tipo

### Debug

```bash
# Execução com verbose (se implementado)
python -m monitoramentos.codigos.web -g 1 --verbose

# Verificar configurações
python -c "from monitoramentos.codigos.config import *; print(DB_CONFIG)"
```

## Migração do Sistema Atual

Este sistema Python substitui/complementa:
- `app/Console/Commands/WebCron*.php`
- `app/Http/Controllers/MonitoramentoController->executarWeb()`
- Scripts de monitoramento no Laravel

### Vantagens da Versão Python

1. **Performance**: Queries otimizadas com CTEs
2. **Manutenibilidade**: Código mais organizado e reutilizável
3. **Flexibilidade**: Execução granular por tipo/grupo
4. **Escalabilidade**: Fácil distribuição de carga
5. **Monitoramento**: Logs estruturados e notificações

## Contribuição

Para adicionar novos tipos de monitoramento:

1. Criar nova classe herdando de `BaseMonitoramento`
2. Implementar `buscar_noticias()` e `pos_processar_noticia()`
3. Adicionar ao `executor.py`
4. Atualizar documentação

