#!/usr/bin/env python3
"""
Interface Web para Geração de Relatórios de Mídia

Aplicação Flask que permite gerar relatórios PDF através de uma interface web.
"""

import os
import subprocess
import json
from datetime import datetime, timedelta
from functools import wraps
from flask import Flask, render_template, request, jsonify, send_file, flash, redirect, url_for, session
from database import DatabaseManager

app = Flask(__name__)
app.secret_key = 'Admin2025Studio@$Clipagem_SECRET_KEY_ULTRA_SECURE'  # Chave secreta para sessões

# Configurações de login
LOGIN_USERNAME = 'admin'
LOGIN_PASSWORD = 'Admin2025Studio@$Clipagem'

def login_required(f):
    """Decorator para proteger rotas que precisam de autenticação"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'logged_in' not in session or not session['logged_in']:
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

@app.route('/login', methods=['GET', 'POST'])
def login():
    """Página de login"""
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        
        if username == LOGIN_USERNAME and password == LOGIN_PASSWORD:
            session['logged_in'] = True
            session['username'] = username
            flash('Login realizado com sucesso!', 'success')
            return redirect(url_for('index'))
        else:
            flash('Usuário ou senha incorretos!', 'error')
    
    return render_template('login.html')

@app.route('/logout')
def logout():
    """Realiza logout do usuário"""
    session.clear()
    flash('Logout realizado com sucesso!', 'success')
    return redirect(url_for('login'))

@app.route('/')
@login_required
def index():
    """Página principal com formulário de geração de relatórios"""
    try:
        # Busca lista de clientes
        db = DatabaseManager()
        clientes = db.get_clientes()
        db.disconnect()
        
        # Define datas padrão (último mês)
        hoje = datetime.now()
        primeiro_dia_mes_atual = hoje.replace(day=1)
        ultimo_dia_mes_anterior = primeiro_dia_mes_atual - timedelta(days=1)
        primeiro_dia_mes_anterior = ultimo_dia_mes_anterior.replace(day=1)
        
        data_inicio_padrao = primeiro_dia_mes_anterior.strftime('%Y-%m-%d')
        data_fim_padrao = ultimo_dia_mes_anterior.strftime('%Y-%m-%d')
        
        return render_template('index.html', 
                             clientes=clientes,
                             data_inicio_padrao=data_inicio_padrao,
                             data_fim_padrao=data_fim_padrao)
    
    except Exception as e:
        flash(f'Erro ao carregar dados: {str(e)}', 'error')
        return render_template('index.html', clientes=[], 
                             data_inicio_padrao='', data_fim_padrao='')

@app.route('/gerar_relatorio', methods=['POST'])
@login_required
def gerar_relatorio():
    """Gera o relatório PDF baseado nos parâmetros do formulário"""
    try:
        print("🎯 Iniciando geração de relatório...")
        
        # Extrai dados do formulário
        cliente_id = request.form.get('cliente_id')
        data_inicio = request.form.get('data_inicio')
        data_fim = request.form.get('data_fim')
        
        # NOVO: Extrai IDs das notícias filtradas (prioritário)
        ids_web = request.form.getlist('ids_web')
        ids_impresso = request.form.getlist('ids_impresso')
        ids_tv = request.form.getlist('ids_tv')
        ids_radio = request.form.getlist('ids_radio')
        
        # Converte para int
        try:
            ids_web = [int(id_) for id_ in ids_web if id_]
            ids_impresso = [int(id_) for id_ in ids_impresso if id_]
            ids_tv = [int(id_) for id_ in ids_tv if id_]
            ids_radio = [int(id_) for id_ in ids_radio if id_]
        except ValueError:
            print("❌ Erro ao converter IDs das notícias")
            ids_web = ids_impresso = ids_tv = ids_radio = []
        
        # Verifica se há IDs específicos (modo prioritário)
        usar_ids_especificos = any([ids_web, ids_impresso, ids_tv, ids_radio])
        
        # Extrai filtros opcionais (para compatibilidade ou quando não há IDs específicos)
        tipos_midia = request.form.getlist('tipos_midia') or ['web', 'tv', 'radio', 'impresso']
        status_filtros = request.form.getlist('status') or ['positivo', 'negativo', 'neutro']
        retorno_filtro = request.form.get('retorno') or 'com_retorno'  # Radio button retorna apenas um valor
        areas_filtros = request.form.getlist('areas') or []
        
        # NOVO: Extrai flag de mostrar retorno de mídia no relatório
        mostrar_retorno_relatorio = request.form.get('mostrar_retorno_relatorio', 'true').lower() == 'true'
        
        # Converte áreas para int
        if areas_filtros:
            try:
                areas_filtros = [int(area) for area in areas_filtros]
            except ValueError:
                areas_filtros = []
        
        # Verifica se o cliente tem permissão para ver retorno de mídia
        print("🔍 Verificando permissão de retorno de mídia do cliente...")
        db_temp = DatabaseManager()
        cliente_config = db_temp.get_cliente_configuracoes(cliente_id)
        tem_permissao_retorno = cliente_config.get('fl_retorno_midia', False) if cliente_config else False
        db_temp.disconnect()
        
        print(f"🔐 Cliente {cliente_id} - Permissão retorno mídia: {tem_permissao_retorno}")
        print(f"🎯 Usuário quer mostrar retorno no relatório: {mostrar_retorno_relatorio}")
        
        # Se o cliente não tem permissão, força mostrar_retorno_relatorio = False
        if not tem_permissao_retorno:
            mostrar_retorno_relatorio = False
            print("⚠️ Cliente sem permissão - forçando mostrar_retorno_relatorio = False")
        
        filtros = {
            'tipos_midia': tipos_midia,
            'status': status_filtros,
            'retorno': [retorno_filtro],  # Converte para lista para manter compatibilidade
            'areas': areas_filtros,
            'mostrar_retorno_relatorio': mostrar_retorno_relatorio,  # NOVO: controla se mostra seções de retorno
            'tem_permissao_retorno': tem_permissao_retorno  # NOVO: indica se o cliente tem permissão
        }
        
        # Adiciona IDs específicos ao filtro
        if usar_ids_especificos:
            filtros['ids_especificos'] = {
                'web': ids_web,
                'impresso': ids_impresso,
                'tv': ids_tv,
                'radio': ids_radio
            }
        
        print(f"📊 Parâmetros: Cliente={cliente_id}, Início={data_inicio}, Fim={data_fim}")
        print(f"🔍 Filtros: {filtros}")
        if usar_ids_especificos:
            print(f"🎯 IDs específicos: Web={len(ids_web)}, Impresso={len(ids_impresso)}, TV={len(ids_tv)}, Rádio={len(ids_radio)}")
            print(f"📋 Total de notícias: {len(ids_web) + len(ids_impresso) + len(ids_tv) + len(ids_radio)}")
        
        # Validações
        if not cliente_id or not data_inicio or not data_fim:
            print("❌ Campos obrigatórios faltando")
            flash('Todos os campos são obrigatórios', 'error')
            return redirect(url_for('index'))
        
        # Converte para int
        try:
            cliente_id = int(cliente_id)
        except ValueError:
            print("❌ ID do cliente inválido")
            flash('ID do cliente deve ser um número válido', 'error')
            return redirect(url_for('index'))
        
        # Verifica se as datas são válidas
        try:
            datetime.strptime(data_inicio, '%Y-%m-%d')
            datetime.strptime(data_fim, '%Y-%m-%d')
        except ValueError:
            print("❌ Formato de data inválido")
            flash('Formato de data inválido', 'error')
            return redirect(url_for('index'))
        
        # Verifica se o cliente existe
        print("🔍 Verificando se cliente existe...")
        db = DatabaseManager()
        if not db.check_cliente(cliente_id):
            db.disconnect()
            print("❌ Cliente não encontrado")
            flash('Cliente não encontrado no banco de dados', 'error')
            return redirect(url_for('index'))
        db.disconnect()
        print("✅ Cliente validado")
        
        # Gera nome do arquivo (mesmo padrão do main.py)
        data_inicio_clean = data_inicio.replace('-', '')
        data_fim_clean = data_fim.replace('-', '')
        nome_arquivo = f"relatorio_{cliente_id}_{data_inicio_clean}_{data_fim_clean}.pdf"
        
        print(f"📁 Nome do arquivo: {nome_arquivo}")
        
        # Executa o script main.py
        import json
        filtros_json = json.dumps(filtros)
        
        comando = [
            'python', 'main.py',
            '--cliente', str(cliente_id),
            '--data_inicio', data_inicio,
            '--data_fim', data_fim,
            '--output', nome_arquivo,
            '--filtros', filtros_json
        ]
        
        print(f"🚀 Executando comando: {' '.join(comando)}")
        resultado = subprocess.run(comando, capture_output=True, text=True, cwd=os.getcwd())
        
        print(f"📤 Código de retorno: {resultado.returncode}")
        if resultado.stdout:
            print(f"📝 STDOUT: {resultado.stdout[:200]}...")
        if resultado.stderr:
            print(f"❌ STDERR: {resultado.stderr[:200]}...")
        
        if resultado.returncode == 0:
            caminho_arquivo = os.path.join('./output', nome_arquivo)
            print(f"🔍 Verificando arquivo em: {caminho_arquivo}")
            
            if os.path.exists(caminho_arquivo):
                print("✅ Arquivo encontrado! Iniciando download...")
                flash('Relatório gerado com sucesso!', 'success')
                
                # Cria resposta com o arquivo
                response = send_file(caminho_arquivo, as_attachment=True, 
                                   download_name=nome_arquivo)
                
                # Define cookie para indicar que o download foi concluído
                import uuid
                download_token = str(uuid.uuid4())
                response.set_cookie('download_token', download_token, max_age=60)  # Expira em 1 minuto
                response.set_cookie('download_status', 'complete', max_age=60)
                
                return response
            else:
                print("❌ Arquivo não encontrado após geração")
                print(f"📁 Verificando arquivos existentes...")
                try:
                    arquivos = os.listdir('./output')
                    print(f"📂 Arquivos no output: {arquivos}")
                    # Procura por arquivo similar
                    for arquivo in arquivos:
                        if f"relatorio_{cliente_id}_" in arquivo and arquivo.endswith('.pdf'):
                            caminho_alternativo = os.path.join('./output', arquivo)
                            print(f"✅ Arquivo alternativo encontrado: {arquivo}")
                            flash('Relatório gerado com sucesso!', 'success')
                            
                            # Cria resposta com o arquivo alternativo
                            response = send_file(caminho_alternativo, as_attachment=True, 
                                               download_name=arquivo)
                            
                            # Define cookie para indicar que o download foi concluído
                            import uuid
                            download_token = str(uuid.uuid4())
                            response.set_cookie('download_token', download_token, max_age=60)
                            response.set_cookie('download_status', 'complete', max_age=60)
                            
                            return response
                except Exception as e:
                    print(f"❌ Erro ao listar arquivos: {e}")
                
                flash('Erro: Arquivo PDF não foi encontrado após geração', 'error')
                return redirect(url_for('index'))
        else:
            erro_msg = resultado.stderr if resultado.stderr else 'Erro desconhecido'
            print(f"❌ Erro na execução: {erro_msg}")
            flash(f'Erro ao gerar relatório: {erro_msg}', 'error')
            return redirect(url_for('index'))
    
    except Exception as e:
        print(f"💥 Exceção não tratada: {str(e)}")
        import traceback
        traceback.print_exc()
        flash(f'Erro interno: {str(e)}', 'error')
        return redirect(url_for('index'))

@app.route('/api/clientes')
@login_required
def api_clientes():
    """API endpoint para buscar lista de clientes"""
    try:
        db = DatabaseManager()
        clientes = db.get_clientes()
        db.disconnect()
        return jsonify(clientes)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/validar_cliente/<int:cliente_id>')
@login_required
def api_validar_cliente(cliente_id):
    """API endpoint para validar se um cliente existe"""
    try:
        db = DatabaseManager()
        existe = db.check_cliente(cliente_id)
        db.disconnect()
        return jsonify({'existe': existe})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/areas/<int:cliente_id>')
@login_required
def api_areas_cliente(cliente_id):
    """API endpoint para buscar áreas de um cliente"""
    try:
        db = DatabaseManager()
        areas = db.get_areas_by_cliente(cliente_id)
        db.disconnect()
        return jsonify(areas)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/status')
@login_required
def status():
    """Página de status do sistema"""
    try:
        # Testa conexão com banco
        db = DatabaseManager()
        conexao_ok = db.connect()
        if conexao_ok:
            clientes = db.get_clientes()
            total_clientes = len(clientes)
            db.disconnect()
        else:
            total_clientes = 0
        
        # Verifica se diretório de output existe
        output_dir_exists = os.path.exists('./output')
        
        # Lista arquivos recentes no output
        arquivos_recentes = []
        if output_dir_exists:
            try:
                arquivos = os.listdir('./output')
                arquivos_pdf = [f for f in arquivos if f.endswith('.pdf')]
                arquivos_pdf.sort(key=lambda x: os.path.getmtime(os.path.join('./output', x)), reverse=True)
                arquivos_recentes = arquivos_pdf[:5]  # Últimos 5 arquivos
            except:
                pass
        
        return render_template('status.html',
                             conexao_db=conexao_ok,
                             total_clientes=total_clientes,
                             output_dir_exists=output_dir_exists,
                             arquivos_recentes=arquivos_recentes)
    
    except Exception as e:
        return render_template('status.html',
                             conexao_db=False,
                             total_clientes=0,
                             output_dir_exists=False,
                             arquivos_recentes=[],
                             error=str(e))

@app.route('/check_download_status')
@login_required
def check_download_status():
    """Endpoint para verificar se um download foi concluído"""
    download_status = request.cookies.get('download_status')
    if download_status == 'complete':
        return jsonify({'status': 'complete'})
    else:
        return jsonify({'status': 'pending'})

@app.route('/listar_noticias', methods=['POST'])
@login_required
def listar_noticias():
    """Lista todas as notícias do período separadas por tipo de mídia com filtros aplicados"""
    try:
        print("🎯 Iniciando listagem de notícias...")
        
        # Extrai dados do formulário
        cliente_id = request.form.get('cliente_id')
        data_inicio = request.form.get('data_inicio')
        data_fim = request.form.get('data_fim')
        
        # Extrai filtros avançados
        tipos_midia = request.form.getlist('tipos_midia') or ['web', 'tv', 'radio', 'impresso']
        status_filtros = request.form.getlist('status') or ['positivo', 'negativo', 'neutro']
        retorno_filtro = request.form.get('retorno') or 'com_retorno'  # Radio button retorna apenas um valor
        valor_filtros = request.form.getlist('valor') or ['com_valor', 'sem_valor']  # Checkbox values
        areas_filtros = request.form.getlist('areas') or []
        
        # Converte áreas para int
        if areas_filtros:
            try:
                areas_filtros = [int(area) for area in areas_filtros]
            except ValueError:
                areas_filtros = []
        
        # Verifica se o cliente tem permissão para ver retorno de mídia
        print("🔍 Verificando permissão de retorno de mídia do cliente...")
        db_temp = DatabaseManager()
        cliente_config = db_temp.get_cliente_configuracoes(cliente_id)
        tem_permissao_retorno = cliente_config.get('fl_retorno_midia', False) if cliente_config else False
        db_temp.disconnect()
        
        print(f"🔐 Cliente {cliente_id} - Permissão retorno mídia: {tem_permissao_retorno}")
        
        # Monta objeto de filtros
        filtros = {
            'tipos_midia': tipos_midia,
            'status': status_filtros,
            'retorno': [retorno_filtro],  # Converte para lista para manter compatibilidade
            'valor': valor_filtros,
            'areas': areas_filtros,
            'tem_permissao_retorno': tem_permissao_retorno  # NOVO: indica se o cliente tem permissão
        }
        
        print(f"📊 Parâmetros: Cliente={cliente_id}, Início={data_inicio}, Fim={data_fim}")
        print(f"🔍 Filtros aplicados: {filtros}")
        
        # Validações
        if not cliente_id or not data_inicio or not data_fim:
            print("❌ Campos obrigatórios faltando")
            return jsonify({
                'success': False,
                'message': 'Todos os campos são obrigatórios'
            }), 400
        
        # Converte para int
        try:
            cliente_id = int(cliente_id)
        except ValueError:
            print("❌ ID do cliente inválido")
            return jsonify({
                'success': False,
                'message': 'ID do cliente deve ser um número válido'
            }), 400
        
        # Verifica se as datas são válidas
        try:
            from datetime import datetime
            datetime.strptime(data_inicio, '%Y-%m-%d')
            datetime.strptime(data_fim, '%Y-%m-%d')
        except ValueError:
            print("❌ Formato de data inválido")
            return jsonify({
                'success': False,
                'message': 'Formato de data inválido'
            }), 400
        
        # Verifica se o cliente existe
        print("🔍 Verificando se cliente existe...")
        db = DatabaseManager()
        if not db.check_cliente(cliente_id):
            db.disconnect()
            print("❌ Cliente não encontrado")
            return jsonify({
                'success': False,
                'message': 'Cliente não encontrado no banco de dados'
            }), 404
        
        # Lista as notícias com filtros aplicados
        print("📋 Listando notícias com filtros...")
        noticias = db.listar_noticias_por_periodo_com_filtros(cliente_id, data_inicio, data_fim, filtros)
        db.disconnect()
        
        print("✅ Notícias listadas com sucesso")
        
        # Garante que a resposta seja JSON válido
        response = jsonify({
            'success': True,
            'message': 'Notícias listadas com sucesso',
            'noticias': noticias,
            'filtros_aplicados': filtros
        })
        response.headers['Content-Type'] = 'application/json; charset=utf-8'
        return response
    
    except Exception as e:
        print(f"💥 Erro na listagem: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/adicionar_noticia', methods=['POST'])
@login_required
def adicionar_noticia():
    """Adiciona uma nova notícia ao banco de dados"""
    try:
        print("🎯 Iniciando adição de notícia...")
        
        # Extrai dados do formulário
        dados_noticia = {
            'tipo': request.form.get('tipo'),
            'cliente_id': request.form.get('cliente_id'),
            'data': request.form.get('data'),
            'titulo': request.form.get('titulo'),
            'veiculo': request.form.get('veiculo'),
            'texto': request.form.get('texto'),
            'valor': request.form.get('valor', 0),
            'tags': request.form.get('tags', '')  # Adiciona suporte para tags
        }
        
        # Campos específicos por tipo de mídia
        if dados_noticia['tipo'] == 'WEB':
            dados_noticia['link'] = request.form.get('link', '')
        elif dados_noticia['tipo'] == 'TV':
            dados_noticia['programa'] = request.form.get('programa', '')
            dados_noticia['horario'] = request.form.get('horario', '')
        elif dados_noticia['tipo'] == 'RADIO':
            dados_noticia['programa_radio'] = request.form.get('programa_radio', '')
            dados_noticia['horario_radio'] = request.form.get('horario_radio', '')
        
        print(f"📊 Dados da notícia: {dados_noticia['tipo']} - {dados_noticia['titulo']}")
        if dados_noticia['tags']:
            print(f"🏷️ Tags: {dados_noticia['tags']}")
        
        # Adiciona a notícia
        db = DatabaseManager()
        resultado = db.adicionar_noticia(dados_noticia)
        db.disconnect()
        
        if resultado['success']:
            print("✅ Notícia adicionada com sucesso")
            return jsonify(resultado)
        else:
            print(f"❌ Erro ao adicionar notícia: {resultado['message']}")
            return jsonify(resultado), 400
    
    except Exception as e:
        print(f"💥 Erro na adição: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/editar_noticia', methods=['POST'])
@login_required
def editar_noticia():
    """Edita uma notícia existente no banco de dados"""
    try:
        print("🎯 Iniciando edição de notícia...")
        print(f"🔍 Content-Type da requisição: {request.content_type}")
        print(f"🔍 Método da requisição: {request.method}")
        print(f"🔍 Headers da requisição: {dict(request.headers)}")
        
        # Debug: Mostra todos os dados recebidos
        print(f"🔍 Form data keys: {list(request.form.keys())}")
        print(f"🔍 Files keys: {list(request.files.keys())}")
        
        # Extrai dados do formulário
        noticia_id = request.form.get('noticia_id')
        print(f"🔍 noticia_id extraído: {noticia_id}")
        
        # Validação do ID da notícia
        if not noticia_id:
            print("❌ ID da notícia não fornecido")
            return jsonify({
                'success': False,
                'message': 'ID da notícia é obrigatório'
            }), 400
        
        try:
            noticia_id = int(noticia_id)
            print(f"✅ noticia_id convertido para int: {noticia_id}")
        except ValueError as ve:
            print(f"❌ ID da notícia inválido - Erro: {str(ve)}")
            return jsonify({
                'success': False,
                'message': f'ID da notícia deve ser um número válido. Recebido: {noticia_id}'
            }), 400
        
        # Extrai dados do formulário com debug
        try:
            dados_noticia = {
                'tipo': request.form.get('tipo'),
                'cliente_id': request.form.get('cliente_id'),
                'data': request.form.get('data'),
                'titulo': request.form.get('titulo'),
                'veiculo': request.form.get('veiculo'),
                'texto': request.form.get('texto'),
                'valor': request.form.get('valor', 0),
                'tags': request.form.get('tags', '')  # Adiciona suporte para tags
            }
            print(f"✅ Dados do formulário extraídos com sucesso")
            print(f"🔍 Tipo: {dados_noticia['tipo']}")
            print(f"🔍 Cliente ID: {dados_noticia['cliente_id']}")
            print(f"🔍 Titulo: {dados_noticia['titulo']}")
            print(f"🔍 Valor: {dados_noticia['valor']}")
        except Exception as form_error:
            print(f"❌ Erro ao extrair dados do formulário: {str(form_error)}")
            return jsonify({
                'success': False,
                'message': f'Erro ao processar dados do formulário: {str(form_error)}'
            }), 400
        
        # Campos específicos por tipo de mídia
        if dados_noticia['tipo'] == 'WEB':
            dados_noticia['link'] = request.form.get('link', '')
        elif dados_noticia['tipo'] == 'TV':
            dados_noticia['programa'] = request.form.get('programa', '')
            dados_noticia['horario'] = request.form.get('horario', '')
        elif dados_noticia['tipo'] == 'RADIO':
            dados_noticia['programa_radio'] = request.form.get('programa_radio', '')
            dados_noticia['horario_radio'] = request.form.get('horario_radio', '')
            # Também aceita os campos sem o sufixo _radio para compatibilidade
            if not dados_noticia['programa_radio']:
                dados_noticia['programa_radio'] = request.form.get('programa', '')
            if not dados_noticia['horario_radio']:
                dados_noticia['horario_radio'] = request.form.get('horario', '')
        
        print(f"📊 Dados da notícia para edição: {dados_noticia['tipo']} - {dados_noticia['titulo']}")
        print(f"🔍 ID da notícia: {noticia_id}")
        if dados_noticia['tags']:
            print(f"🏷️ Tags: {dados_noticia['tags']}")
        
        # Validações básicas
        print(f"🔍 Validando campos obrigatórios:")
        print(f"   - Tipo: '{dados_noticia['tipo']}' (válido: {bool(dados_noticia['tipo'])})")
        print(f"   - Título: '{dados_noticia['titulo']}' (válido: {bool(dados_noticia['titulo'])})")
        print(f"   - Veículo: '{dados_noticia['veiculo']}' (válido: {bool(dados_noticia['veiculo'])})")
        print(f"   - Data: '{dados_noticia['data']}' (válido: {bool(dados_noticia['data'])})")
        
        if not dados_noticia['tipo'] or not dados_noticia['titulo'] or not dados_noticia['veiculo'] or not dados_noticia['data']:
            campos_faltando = []
            if not dados_noticia['tipo']: campos_faltando.append('tipo')
            if not dados_noticia['titulo']: campos_faltando.append('título')
            if not dados_noticia['veiculo']: campos_faltando.append('veículo')
            if not dados_noticia['data']: campos_faltando.append('data')
            
            print(f"❌ Campos obrigatórios faltando: {', '.join(campos_faltando)}")
            return jsonify({
                'success': False,
                'message': f'Campos obrigatórios não preenchidos: {", ".join(campos_faltando)}'
            }), 400
        
        # Processa upload de imagem para notícias impressas e web
        upload_status = None
        print(f"🔍 Verificando upload de imagem - Tipo: {dados_noticia['tipo']}")
        print(f"🔍 Arquivos recebidos: {list(request.files.keys())}")
        
        if dados_noticia['tipo'] in ['JORNAL', 'WEB'] and 'upload_imagem' in request.files:
            upload_file = request.files['upload_imagem']
            print(f"📷 Arquivo de upload encontrado: {upload_file}")
            print(f"📷 Filename: {upload_file.filename}")
            print(f"📷 Content-Type: {upload_file.content_type}")
            
            if upload_file and upload_file.filename:
                print(f"📷 Processando upload de imagem: {upload_file.filename}")
                
                try:
                    # Validações do arquivo
                    allowed_extensions = ['jpg', 'jpeg', 'png']
                    file_ext = upload_file.filename.lower().split('.')[-1]
                    print(f"📷 Extensão do arquivo: {file_ext}")
                    
                    if file_ext not in allowed_extensions:
                        print(f"❌ Extensão não suportada: {file_ext}")
                        return jsonify({
                            'success': False,
                            'message': f'Formato de arquivo não suportado. Use: {", ".join(allowed_extensions)}'
                        }), 400
                    
                    # Validação do tamanho (5MB)
                    print("📏 Validando tamanho do arquivo...")
                    upload_file.seek(0, 2)  # Vai para o final do arquivo
                    file_size = upload_file.tell()  # Pega o tamanho
                    upload_file.seek(0)  # Volta para o início
                    print(f"📏 Tamanho do arquivo: {file_size / 1024 / 1024:.2f}MB")
                    
                    if file_size > 5 * 1024 * 1024:
                        print(f"❌ Arquivo muito grande: {file_size / 1024 / 1024:.2f}MB")
                        return jsonify({
                            'success': False,
                            'message': f'Arquivo muito grande. Tamanho: {file_size / 1024 / 1024:.2f}MB. Máximo: 5MB'
                        }), 400
                        
                except Exception as validation_error:
                    print(f"❌ Erro na validação do arquivo: {str(validation_error)}")
                    return jsonify({
                        'success': False,
                        'message': f'Erro na validação do arquivo: {str(validation_error)}'
                    }), 400
                
                # Determina o Content-Type baseado na extensão
                content_type_map = {
                    'jpg': 'image/jpeg',
                    'jpeg': 'image/jpeg', 
                    'png': 'image/png'
                }
                content_type = content_type_map.get(file_ext, 'image/jpeg')
                
                try:
                    print("🔧 Iniciando configuração S3...")
                    # Configuração S3
                    import boto3
                    s3_client = boto3.client(
                        's3',
                        aws_access_key_id='AKIAXH7FCUIUMZ7NFM5Q',
                        aws_secret_access_key='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',
                        region_name='us-east-1'
                    )
                    print("✅ Cliente S3 configurado com sucesso")
                    
                    bucket_name = 'docmidia-files'
                    
                    # Define o caminho S3 baseado no tipo de mídia
                    if dados_noticia['tipo'] == 'WEB':
                        s3_key = f"backup_studioclipagemco/public_html/fmanager/clipagem/web/arquivo{noticia_id}_1.jpeg"
                    else:  # JORNAL
                        s3_key = f"backup_studioclipagemco/public_html/fmanager/clipagem/jornal/arquivo{noticia_id}_1.jpeg"
                    
                    print(f"📤 Fazendo upload para S3:")
                    print(f"   - Bucket: {bucket_name}")
                    print(f"   - Key: {s3_key}")
                    print(f"   - Content-Type: {content_type}")
                    print(f"   - Posição do arquivo: {upload_file.tell()}")
                    
                    # Upload para S3
                    s3_client.upload_fileobj(
                        upload_file,
                        bucket_name,
                        s3_key,
                        ExtraArgs={
                            'ContentType': content_type
                        }
                    )
                    
                    upload_status = {
                        'success': True,
                        'message': 'Imagem enviada com sucesso para o S3',
                        's3_path': s3_key
                    }
                    print(f"✅ Upload concluído: {s3_key}")
                    
                except Exception as upload_error:
                    print(f"❌ Erro no upload S3: {str(upload_error)}")
                    print(f"❌ Tipo do erro: {type(upload_error).__name__}")
                    import traceback
                    traceback.print_exc()
                    upload_status = {
                        'success': False,
                        'message': f'Erro ao enviar imagem: {str(upload_error)}'
                    }
                    # Continua com a edição da notícia mesmo se o upload falhar
        
        # Edita a notícia
        print("💾 Iniciando edição no banco de dados...")
        try:
            db = DatabaseManager()
            print("✅ Conexão com banco estabelecida")
            
            resultado = db.editar_noticia(noticia_id, dados_noticia)
            print(f"📊 Resultado da edição: {resultado}")
            
            db.disconnect()
            print("✅ Conexão com banco encerrada")
            
            if resultado['success']:
                print("✅ Notícia editada com sucesso")
                
                # Adiciona informações do upload se houve
                if upload_status:
                    resultado['upload_status'] = upload_status
                    if upload_status['success']:
                        resultado['message'] += f" | Imagem enviada para S3"
                    else:
                        resultado['message'] += f" | Aviso: {upload_status['message']}"
                
                return jsonify(resultado)
            else:
                print(f"❌ Erro ao editar notícia: {resultado['message']}")
                return jsonify(resultado), 400
                
        except Exception as db_error:
            print(f"❌ Erro na operação do banco: {str(db_error)}")
            print(f"❌ Tipo do erro: {type(db_error).__name__}")
            import traceback
            traceback.print_exc()
            return jsonify({
                'success': False,
                'message': f'Erro na operação do banco: {str(db_error)}'
            }), 500
    
    except Exception as e:
        print(f"💥 Erro na edição: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/excluir_noticia', methods=['POST'])
@login_required
def excluir_noticia():
    """Exclui uma notícia PERMANENTEMENTE do banco de dados"""
    db = None
    
    try:
        print("🎯 Iniciando exclusão PERMANENTE de notícia...")
        
        # Verifica se o request tem dados JSON
        if not request.is_json:
            print("❌ Request não é JSON")
            return jsonify({
                'success': False,
                'message': 'Requisição deve ser JSON'
            }), 400
        
        # Extrai dados do JSON
        data = request.get_json()
        if not data:
            print("❌ Dados JSON vazios")
            return jsonify({
                'success': False,
                'message': 'Dados JSON não fornecidos'
            }), 400
        
        vinculo_id = data.get('vinculo_id')
        
        if not vinculo_id:
            print("❌ ID do vínculo não fornecido")
            return jsonify({
                'success': False,
                'message': 'ID do vínculo é obrigatório para exclusão'
            }), 400
        
        try:
            vinculo_id = int(vinculo_id)
            if vinculo_id <= 0:
                raise ValueError("ID deve ser positivo")
        except ValueError as ve:
            print(f"❌ ID do vínculo inválido: {ve}")
            return jsonify({
                'success': False,
                'message': 'ID do vínculo deve ser um número válido e positivo'
            }), 400
        
        print(f"🗑️ Excluindo vínculo ID: {vinculo_id}")
        
        # Cria instância única do DB para toda a operação
        try:
            db = DatabaseManager()
            # Tenta conectar explicitamente
            if not db.connect():
                raise Exception("Falha ao conectar com o banco de dados")
        except Exception as db_error:
            print(f"❌ Erro ao conectar com o banco: {db_error}")
            return jsonify({
                'success': False,
                'message': 'Erro de conexão com o banco de dados'
            }), 500
        
        # Tenta excluir o vínculo
        try:
            print(f"🔍 Tentando excluir vínculo ID {vinculo_id}...")
            resultado_final = db.excluir_noticia(vinculo_id)
            
            if not resultado_final['success']:
                print(f"❌ Falha ao excluir vínculo: {resultado_final.get('message', 'Erro desconhecido')}")
                
        except Exception as table_error:
            print(f"❌ Erro ao excluir vínculo: {table_error}")
            resultado_final = {'success': False, 'message': str(table_error)}
        
        # Fecha conexão imediatamente
        try:
            db.disconnect()
            print("🔌 Conexão DB fechada após operação")
        except Exception as close_error:
            print(f"⚠️ Erro ao fechar conexão: {close_error}")
        
        if resultado_final and resultado_final['success']:
            noticia_info = resultado_final.get('noticia_info', {})
            print(f"✅ Vínculo EXCLUÍDO: ID {noticia_info.get('vinculo_id', 'N/A')}")
            
            # Log da exclusão para auditoria
            print(f"📋 AUDITORIA - Exclusão de vínculo:")
            print(f"   - Vínculo ID: {noticia_info.get('vinculo_id', 'N/A')}")
            print(f"   - Notícia ID: {noticia_info.get('noticia_id', 'N/A')}")
            print(f"   - Cliente ID: {noticia_info.get('cliente_id', 'N/A')}")
            print(f"   - Tipo: {noticia_info.get('tipo_midia', 'N/A')}")
            print(f"   - Linhas afetadas: {noticia_info.get('rows_affected', 0)}")
            
            return jsonify({
                'success': True,
                'message': resultado_final['message'],
                'noticia_excluida': noticia_info
            })
        else:
            print("❌ Vínculo não encontrado ou falha na exclusão")
            erro_msg = resultado_final.get('message', 'Erro desconhecido') if resultado_final else 'Erro desconhecido'
            return jsonify({
                'success': False,
                'message': f'Vínculo não encontrado ou já foi excluído: {erro_msg}'
            }), 404
    
    except Exception as e:
        print(f"💥 Erro crítico na exclusão do vínculo: {str(e)}")
        print(f"💥 Tipo do erro: {type(e).__name__}")
        print(f"💥 Dados recebidos: {locals()}")
        import traceback
        traceback.print_exc()
        
        # Log mais detalhado do erro
        import sys
        exc_type, exc_value, exc_traceback = sys.exc_info()
        print(f"💥 Linha do erro: {exc_traceback.tb_lineno}")
        
        return jsonify({
            'success': False,
            'message': f'Erro interno do servidor: {str(e)}',
            'error_type': type(e).__name__,
            'error_line': exc_traceback.tb_lineno if exc_traceback else 'N/A'
        }), 500
    
    finally:
        # Garante que a conexão seja fechada
        if db:
            try:
                db.disconnect()
                print("🔌 Conexão DB fechada no finally")
            except Exception as close_error:
                print(f"⚠️ Erro ao fechar conexão: {close_error}")

@app.route('/upload_imagem', methods=['POST'])
@login_required
def upload_imagem():
    """Upload dedicado de imagem para S3"""
    try:
        print("🎯 Iniciando upload dedicado de imagem...")
        print(f"🔍 Content-Type da requisição: {request.content_type}")
        print(f"🔍 Form data keys: {list(request.form.keys())}")
        print(f"🔍 Files keys: {list(request.files.keys())}")
        
        # Extrai dados do formulário
        noticia_id = request.form.get('noticia_id')
        cliente_id = request.form.get('cliente_id')
        tipo_midia = request.form.get('tipo_midia')
        
        print(f"🔍 Dados extraídos - Notícia: {noticia_id}, Cliente: {cliente_id}, Tipo: {tipo_midia}")
        
        # Validações básicas
        if not noticia_id or not cliente_id or not tipo_midia:
            print("❌ Campos obrigatórios faltando")
            print(f"   - noticia_id: '{noticia_id}' (presente: {bool(noticia_id)})")
            print(f"   - cliente_id: '{cliente_id}' (presente: {bool(cliente_id)})")
            print(f"   - tipo_midia: '{tipo_midia}' (presente: {bool(tipo_midia)})")
            return jsonify({
                'success': False,
                'message': 'Campos obrigatórios: noticia_id, cliente_id, tipo_midia'
            }), 400
        
        if tipo_midia not in ['WEB', 'JORNAL']:
            print(f"❌ Tipo de mídia não suportado: {tipo_midia}")
            return jsonify({
                'success': False,
                'message': 'Upload de imagem disponível apenas para WEB e JORNAL'
            }), 400
        
        # Verifica se há arquivo
        if 'imagem' not in request.files:
            print("❌ Arquivo de imagem não encontrado")
            return jsonify({
                'success': False,
                'message': 'Arquivo de imagem não encontrado na requisição'
            }), 400
        
        upload_file = request.files['imagem']
        print(f"📷 Arquivo recebido: {upload_file.filename}")
        print(f"📷 Content-Type: {upload_file.content_type}")
        
        if not upload_file or not upload_file.filename:
            print("❌ Arquivo vazio ou sem nome")
            return jsonify({
                'success': False,
                'message': 'Arquivo de imagem inválido'
            }), 400
        
        try:
            noticia_id = int(noticia_id)
            cliente_id = int(cliente_id)
        except ValueError:
            print("❌ IDs inválidos")
            return jsonify({
                'success': False,
                'message': 'IDs da notícia e cliente devem ser números válidos'
            }), 400
        
        print(f"📷 Processando upload de imagem: {upload_file.filename}")
        
        try:
            # Validações do arquivo
            allowed_extensions = ['jpg', 'jpeg', 'png']
            file_ext = upload_file.filename.lower().split('.')[-1]
            print(f"📷 Extensão do arquivo: {file_ext}")
            
            if file_ext not in allowed_extensions:
                print(f"❌ Extensão não suportada: {file_ext}")
                return jsonify({
                    'success': False,
                    'message': f'Formato de arquivo não suportado. Use: {", ".join(allowed_extensions)}'
                }), 400
            
            # Validação do tamanho (5MB)
            print("📏 Validando tamanho do arquivo...")
            upload_file.seek(0, 2)  # Vai para o final do arquivo
            file_size = upload_file.tell()  # Pega o tamanho
            upload_file.seek(0)  # Volta para o início
            print(f"📏 Tamanho do arquivo: {file_size / 1024 / 1024:.2f}MB")
            
            if file_size > 5 * 1024 * 1024:
                print(f"❌ Arquivo muito grande: {file_size / 1024 / 1024:.2f}MB")
                return jsonify({
                    'success': False,
                    'message': f'Arquivo muito grande. Tamanho: {file_size / 1024 / 1024:.2f}MB. Máximo: 5MB'
                }), 400
                
        except Exception as validation_error:
            print(f"❌ Erro na validação do arquivo: {str(validation_error)}")
            return jsonify({
                'success': False,
                'message': f'Erro na validação do arquivo: {str(validation_error)}'
            }), 400
        
        # Determina o Content-Type baseado na extensão
        content_type_map = {
            'jpg': 'image/jpeg',
            'jpeg': 'image/jpeg', 
            'png': 'image/png'
        }
        content_type = content_type_map.get(file_ext, 'image/jpeg')
        
        try:
            print("🔧 Iniciando configuração S3...")
            # Configuração S3
            import boto3
            s3_client = boto3.client(
                's3',
                aws_access_key_id='AKIAXH7FCUIUMZ7NFM5Q',
                aws_secret_access_key='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',
                region_name='us-east-1'
            )
            print("✅ Cliente S3 configurado com sucesso")
            
            bucket_name = 'docmidia-files'
            
            # Define o caminho S3 baseado no tipo de mídia
            if tipo_midia == 'WEB':
                s3_key = f"backup_studioclipagemco/public_html/fmanager/clipagem/web/arquivo{noticia_id}_1.jpeg"
            else:  # JORNAL
                s3_key = f"backup_studioclipagemco/public_html/fmanager/clipagem/jornal/arquivo{noticia_id}_1.jpeg"
            
            print(f"📤 Fazendo upload para S3:")
            print(f"   - Bucket: {bucket_name}")
            print(f"   - Key: {s3_key}")
            print(f"   - Content-Type: {content_type}")
            print(f"   - Posição do arquivo: {upload_file.tell()}")
            
            # Upload para S3
            s3_client.upload_fileobj(
                upload_file,
                bucket_name,
                s3_key,
                ExtraArgs={
                    'ContentType': content_type
                }
            )
            
            print(f"✅ Upload concluído: {s3_key}")
            
            return jsonify({
                'success': True,
                'message': 'Imagem enviada com sucesso para o S3',
                's3_path': s3_key,
                'noticia_id': noticia_id,
                'tipo_midia': tipo_midia
            })
            
        except Exception as upload_error:
            print(f"❌ Erro no upload S3: {str(upload_error)}")
            print(f"❌ Tipo do erro: {type(upload_error).__name__}")
            import traceback
            traceback.print_exc()
            return jsonify({
                'success': False,
                'message': f'Erro ao enviar imagem para S3: {str(upload_error)}'
            }), 500
    
    except Exception as e:
        print(f"💥 Erro no upload: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/aplicar_tags_lote', methods=['POST'])
@login_required
def aplicar_tags_lote():
    """Aplica tags a múltiplas notícias de uma vez"""
    try:
        print("🏷️ Iniciando aplicação de tags em lote...")
        
        # Extrai dados do JSON
        data = request.get_json()
        
        if not data:
            return jsonify({
                'success': False,
                'message': 'Dados não fornecidos'
            }), 400
        
        noticias_ids = data.get('noticias_ids', [])
        tags_aplicar = data.get('tags', '').strip()
        acao = data.get('acao', 'adicionar')  # 'adicionar', 'substituir', 'remover'
        
        print(f"📋 IDs das notícias: {noticias_ids}")
        print(f"🏷️ Tags a aplicar: {tags_aplicar}")
        print(f"🎯 Ação: {acao}")
        
        # Validações
        if not noticias_ids:
            return jsonify({
                'success': False,
                'message': 'Nenhuma notícia selecionada'
            }), 400
        
        if not tags_aplicar and acao != 'remover':
            return jsonify({
                'success': False,
                'message': 'Tags não fornecidas'
            }), 400
        
        # Aplica as tags
        db = DatabaseManager()
        resultado = db.aplicar_tags_lote(noticias_ids, tags_aplicar, acao)
        db.disconnect()
        
        if resultado['success']:
            print(f"✅ Tags aplicadas com sucesso a {resultado['noticias_atualizadas']} notícias")
            return jsonify(resultado)
        else:
            print(f"❌ Erro ao aplicar tags: {resultado['message']}")
            return jsonify(resultado), 400
            
    except Exception as e:
        print(f"💥 Erro na aplicação de tags em lote: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/gerar_relatorio_impresso', methods=['POST'])
@login_required
def gerar_relatorio_impresso():
    """Gera relatório PDF específico para notícias impressas com imagens do S3"""
    try:
        print("🎯 Iniciando geração de relatório de impressos...")
        
        # Extrai dados do formulário
        data = request.get_json()
        cliente_id = data.get('cliente_id')
        data_inicio = data.get('data_inicio')
        data_fim = data.get('data_fim')
        ids_impresso = data.get('ids_impresso', [])
        
        print(f"📊 Parâmetros: Cliente={cliente_id}, Início={data_inicio}, Fim={data_fim}")
        print(f"📋 IDs das notícias impressas: {ids_impresso}")
        
        # Validações
        if not cliente_id or not data_inicio or not data_fim:
            print("❌ Campos obrigatórios faltando")
            return jsonify({
                'success': False,
                'message': 'Campos obrigatórios não preenchidos (cliente, data início, data fim)'
            }), 400
        
        if not ids_impresso:
            print("❌ Nenhuma notícia impressa selecionada")
            return jsonify({
                'success': False,
                'message': 'Nenhuma notícia impressa selecionada'
            }), 400
        
        try:
            cliente_id = int(cliente_id)
            ids_impresso = [int(id_) for id_ in ids_impresso if id_]
        except ValueError:
            print("❌ IDs inválidos")
            return jsonify({
                'success': False,
                'message': 'IDs inválidos fornecidos'
            }), 400
        
        # Verifica se o cliente existe e busca dados
        db = DatabaseManager()
        if not db.check_cliente(cliente_id):
            db.disconnect()
            print("❌ Cliente não encontrado")
            return jsonify({
                'success': False,
                'message': 'Cliente não encontrado'
            }), 404
        
        # Busca nome do cliente
        clientes = db.get_clientes()
        cliente_nome = "Cliente"
        for cliente in clientes:
            if cliente['id'] == cliente_id:
                cliente_nome = cliente['nome']
                break
        
        # Busca notícias impressas pelos IDs
        noticias_impressas = []
        for noticia_id in ids_impresso:
            noticia = db.get_noticia_by_id(noticia_id, 'impresso')
            if noticia:
                noticias_impressas.append(noticia)
        
        db.disconnect()
        
        if not noticias_impressas:
            print("❌ Nenhuma notícia impressa encontrada")
            return jsonify({
                'success': False,
                'message': 'Nenhuma notícia impressa encontrada com os IDs fornecidos'
            }), 404
        
        print(f"✅ Encontradas {len(noticias_impressas)} notícias impressas")
        
        # Gera nome do arquivo
        data_inicio_clean = data_inicio.replace('-', '')
        data_fim_clean = data_fim.replace('-', '')
        nome_arquivo = f"relatorio_impresso_{cliente_id}_{data_inicio_clean}_{data_fim_clean}.pdf"
        caminho_arquivo = os.path.join('./output', nome_arquivo)
        
        print(f"📁 Nome do arquivo: {nome_arquivo}")
        
        # Gera o relatório usando o novo gerador
        from pdf_generator_impresso import PDFGeneratorImpresso
        
        print(f"🔧 [DEBUG] Iniciando geração do PDF com {len(noticias_impressas)} notícias...")
        for i, noticia in enumerate(noticias_impressas):
            print(f"🔧 [DEBUG] Notícia {i+1}: ID={noticia.get('id')}, Título='{noticia.get('titulo', '')[:50]}...'")
        
        generator = PDFGeneratorImpresso()
        sucesso = generator.generate_impresso_report(
            noticias_impressas,
            cliente_nome,
            data_inicio,
            data_fim,
            caminho_arquivo
        )
        
        if sucesso and os.path.exists(caminho_arquivo):
            print("✅ Relatório de impressos gerado com sucesso!")
            print(f"📄 Arquivo salvo: {caminho_arquivo}")
            print(f"📏 Tamanho do arquivo: {os.path.getsize(caminho_arquivo)} bytes")
            
            # Retorna o arquivo para download
            response = send_file(caminho_arquivo, as_attachment=True, 
                               download_name=nome_arquivo)
            
            # Define cookie para indicar que o download foi concluído
            import uuid
            download_token = str(uuid.uuid4())
            response.set_cookie('download_token', download_token, max_age=60)
            response.set_cookie('download_status', 'complete', max_age=60)
            
            return response
        else:
            print("❌ Erro ao gerar relatório de impressos")
            print(f"🔧 [DEBUG] Sucesso: {sucesso}")
            print(f"🔧 [DEBUG] Arquivo existe: {os.path.exists(caminho_arquivo) if 'caminho_arquivo' in locals() else 'N/A'}")
            return jsonify({
                'success': False,
                'message': 'Erro ao gerar o relatório PDF. Verifique os logs para mais detalhes.'
            }), 500
            
    except Exception as e:
        print(f"💥 Erro na geração do relatório de impressos: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/gerar_relatorio_web', methods=['POST'])
@login_required
def gerar_relatorio_web():
    """Gera relatório PDF específico para notícias web com imagens do S3"""
    try:
        print("🌐 Iniciando geração de relatório web...")
        
        # Extrai dados do formulário
        data = request.get_json()
        cliente_id = data.get('cliente_id')
        data_inicio = data.get('data_inicio')
        data_fim = data.get('data_fim')
        ids_web = data.get('ids_web', [])
        
        print(f"📊 Parâmetros: Cliente={cliente_id}, Início={data_inicio}, Fim={data_fim}")
        print(f"📋 IDs das notícias web: {ids_web}")
        
        # Validações
        if not cliente_id or not data_inicio or not data_fim:
            print("❌ Campos obrigatórios faltando")
            return jsonify({
                'success': False,
                'message': 'Campos obrigatórios não preenchidos (cliente, data início, data fim)'
            }), 400
        
        if not ids_web:
            print("❌ Nenhuma notícia web selecionada")
            return jsonify({
                'success': False,
                'message': 'Nenhuma notícia web selecionada'
            }), 400
        
        try:
            cliente_id = int(cliente_id)
            ids_web = [int(id_) for id_ in ids_web if id_]
        except ValueError:
            print("❌ IDs inválidos")
            return jsonify({
                'success': False,
                'message': 'IDs inválidos fornecidos'
            }), 400
        
        # Verifica se o cliente existe e busca dados
        db = DatabaseManager()
        if not db.check_cliente(cliente_id):
            db.disconnect()
            print("❌ Cliente não encontrado")
            return jsonify({
                'success': False,
                'message': 'Cliente não encontrado'
            }), 404
        
        # Busca nome do cliente
        clientes = db.get_clientes()
        cliente_nome = "Cliente"
        for cliente in clientes:
            if cliente['id'] == cliente_id:
                cliente_nome = cliente['nome']
                break
        
        # Busca notícias web pelos IDs
        noticias_web = []
        for noticia_id in ids_web:
            noticia = db.get_noticia_by_id(noticia_id, 'web')
            if noticia:
                noticias_web.append(noticia)
        
        db.disconnect()
        
        if not noticias_web:
            print("❌ Nenhuma notícia web encontrada")
            return jsonify({
                'success': False,
                'message': 'Nenhuma notícia web encontrada com os IDs fornecidos'
            }), 404
        
        print(f"✅ Encontradas {len(noticias_web)} notícias web")
        
        # Gera nome do arquivo
        data_inicio_clean = data_inicio.replace('-', '')
        data_fim_clean = data_fim.replace('-', '')
        nome_arquivo = f"relatorio_web_{cliente_id}_{data_inicio_clean}_{data_fim_clean}.pdf"
        caminho_arquivo = os.path.join('./output', nome_arquivo)
        
        print(f"📁 Nome do arquivo: {nome_arquivo}")
        
        # Gera o relatório usando o novo gerador
        from pdf_generator_web import PDFGeneratorWeb
        
        generator = PDFGeneratorWeb()
        sucesso = generator.generate_web_report(
            noticias_web,
            cliente_nome,
            data_inicio,
            data_fim,
            caminho_arquivo
        )
        
        if sucesso and os.path.exists(caminho_arquivo):
            print("✅ Relatório web gerado com sucesso!")
            
            # Retorna o arquivo para download
            response = send_file(caminho_arquivo, as_attachment=True, 
                               download_name=nome_arquivo)
            
            # Define cookie para indicar que o download foi concluído
            import uuid
            download_token = str(uuid.uuid4())
            response.set_cookie('download_token', download_token, max_age=60)
            response.set_cookie('download_status', 'complete', max_age=60)
            
            return response
        else:
            print("❌ Erro ao gerar relatório web")
            return jsonify({
                'success': False,
                'message': 'Erro ao gerar o relatório PDF'
            }), 500
            
    except Exception as e:
        print(f"💥 Erro na geração do relatório web: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/vincular_noticia_area', methods=['POST'])
@login_required
def vincular_noticia_area():
    """Vincula uma notícia a uma área específica"""
    try:
        print("🎯 Iniciando vinculação de notícia à área...")
        
        # Obtém dados do JSON
        dados = request.get_json()
        if not dados:
            return jsonify({
                'success': False,
                'message': 'Dados JSON não fornecidos'
            }), 400
        
        noticia_id = dados.get('noticia_id')
        tipo_midia = dados.get('tipo_midia')
        area_id = dados.get('area_id')  # Pode ser None para remover área
        
        print(f"📊 Parâmetros: noticia_id={noticia_id}, tipo={tipo_midia}, area_id={area_id}")
        
        # Validações
        if not noticia_id or not tipo_midia:
            return jsonify({
                'success': False,
                'message': 'noticia_id e tipo_midia são obrigatórios'
            }), 400
        
        if tipo_midia not in ['web', 'tv', 'radio', 'impresso']:
            return jsonify({
                'success': False,
                'message': 'tipo_midia deve ser web, tv, radio ou impresso'
            }), 400
        
        try:
            noticia_id = int(noticia_id)
            if area_id is not None:
                area_id = int(area_id)
        except ValueError:
            return jsonify({
                'success': False,
                'message': 'IDs devem ser números válidos'
            }), 400
        
        # Conecta ao banco
        db = DatabaseManager()
        
        # Busca a notícia para verificar se existe
        noticia = db.get_noticia_by_id(noticia_id, tipo_midia)
        if not noticia:
            db.disconnect()
            return jsonify({
                'success': False,
                'message': 'Notícia não encontrada'
            }), 404
        
        # Atualiza a área da notícia
        sucesso = db.vincular_noticia_area(noticia_id, tipo_midia, area_id)
        
        if sucesso:
            # Busca a notícia atualizada para retornar
            noticia_atualizada = db.get_noticia_by_id(noticia_id, tipo_midia)
            db.disconnect()
            
            area_texto = f"área ID {area_id}" if area_id else "nenhuma área"
            print(f"✅ Notícia {noticia_id} vinculada à {area_texto}")
            
            return jsonify({
                'success': True,
                'message': f'Notícia vinculada à {area_texto} com sucesso',
                'noticia': noticia_atualizada
            })
        else:
            db.disconnect()
            return jsonify({
                'success': False,
                'message': 'Erro ao vincular notícia à área'
            }), 500
            
    except Exception as e:
        print(f"💥 Erro na vinculação: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/imagem/<tipo>/<int:noticia_id>', methods=['GET', 'HEAD'])
@login_required
def servir_imagem(tipo, noticia_id):
    """Serve imagens do S3 com base no tipo e ID da notícia - com retry para múltiplas extensões"""
    print(f"🖼️ [DEBUG] Solicitação de imagem: {tipo} ID {noticia_id}")
    try:
        # Valida o tipo
        if tipo not in ['web', 'impresso']:
            print(f"❌ [DEBUG] Tipo inválido: {tipo}")
            return jsonify({'error': 'Tipo inválido'}), 400
        
        # Importa configurações do S3
        from config_s3 import (
            try_multiple_extensions_local, try_multiple_extensions_s3,
            get_image_content_type, S3_BUCKET_NAME, S3_REGION,
            USE_LOCAL_IMAGES_FOR_DEVELOPMENT, PRESIGNED_URL_EXPIRATION,
            AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY
        )
        
        # Tenta primeiro o arquivo local para desenvolvimento
        if USE_LOCAL_IMAGES_FOR_DEVELOPMENT:
            print(f"🔍 [DEBUG] Modo desenvolvimento ativo - verificando arquivos locais")
            local_path, extension = try_multiple_extensions_local(tipo, noticia_id)
            
            if local_path and extension:
                print(f"✅ [DEBUG] Arquivo local encontrado: {local_path}")
                content_type = get_image_content_type(extension)
                return send_file(local_path, mimetype=content_type)
            else:
                print(f"❌ [DEBUG] Nenhum arquivo local encontrado para todas as extensões")
        else:
            print(f"⚠️ [DEBUG] Modo desenvolvimento desabilitado, pulando verificação local")
        
        # Se não encontrar localmente, tenta no S3 com múltiplas extensões
        try:
            import boto3
            from botocore.exceptions import ClientError, NoCredentialsError
            
            s3_client = boto3.client(
                's3',
                aws_access_key_id=AWS_ACCESS_KEY_ID,
                aws_secret_access_key=AWS_SECRET_ACCESS_KEY,
                region_name=S3_REGION
            )
            
            print(f"🔍 [DEBUG] Tentando múltiplas extensões no S3...")
            s3_key, extension = try_multiple_extensions_s3(tipo, noticia_id, s3_client)
            
            if s3_key and extension:
                print(f"✅ [DEBUG] Imagem encontrada no S3: {s3_key}")
                
                # Se existe, gera uma URL pré-assinada para a imagem
                url = s3_client.generate_presigned_url(
                    'get_object',
                    Params={'Bucket': S3_BUCKET_NAME, 'Key': s3_key},
                    ExpiresIn=PRESIGNED_URL_EXPIRATION
                )
                print(f"🔗 [DEBUG] URL pré-assinada gerada: {url[:100]}...")
                
                # Para requisições HEAD, retorna 200 sem redirect
                from flask import request
                if request.method == 'HEAD':
                    print(f"👤 [DEBUG] Requisição HEAD - retornando 200")
                    response = app.response_class(status=200)
                    response.headers['Content-Type'] = get_image_content_type(extension)
                    return response
                
                # Para GET, redireciona para a URL do S3
                from flask import redirect
                return redirect(url)
            else:
                # Nenhuma imagem encontrada em nenhuma extensão
                print(f"❌ [DEBUG] Nenhuma imagem encontrada no S3 para todas as extensões")
                return jsonify({'error': 'Imagem não encontrada'}), 404
                    
        except NoCredentialsError:
            print("⚠️ Credenciais AWS não configuradas. Usando apenas arquivos locais.")
            return jsonify({'error': 'Imagem não encontrada'}), 404
        except Exception as s3_error:
            print(f"⚠️ Erro ao acessar S3: {str(s3_error)}")
            return jsonify({'error': 'Imagem não encontrada'}), 404
            
    except Exception as e:
        print(f"Erro ao servir imagem: {str(e)}")
        return jsonify({'error': 'Erro interno do servidor'}), 500

@app.route('/validar_integridade', methods=['GET', 'POST'])
@login_required
def validar_integridade():
    """Valida a integridade da tabela noticia_cliente"""
    try:
        print("🔍 Iniciando validação de integridade...")
        
        # Extrai cliente_id se fornecido
        cliente_id = None
        if request.method == 'POST':
            cliente_id = request.form.get('cliente_id')
            if cliente_id:
                try:
                    cliente_id = int(cliente_id)
                except ValueError:
                    cliente_id = None
        
        # Executa validação
        db = DatabaseManager()
        relatorio = db.validar_integridade_noticia_cliente(cliente_id)
        db.disconnect()
        
        print("✅ Validação de integridade concluída")
        
        return jsonify({
            'success': True,
            'message': 'Validação de integridade concluída',
            'relatorio': relatorio
        })
        
    except Exception as e:
        print(f"❌ Erro na validação de integridade: {e}")
        return jsonify({
            'success': False,
            'message': f'Erro na validação: {str(e)}'
        }), 500

if __name__ == '__main__':
    # Cria diretório de output se não existir
    os.makedirs('./output', exist_ok=True)
    
    # Inicia o servidor Flask
    app.run(debug=True, host='0.0.0.0', port=5050) 