@extends('layouts.template')
@section('content')
 <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
                <div class="shape shape-4"></div>
            </div>
        </div>
        
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-chart-line"></i>
                    <span>Monitoramento Profissional</span>
                </div>
                
                <h1 class="hero-title">
                    Transformamos
                    <span class="title-highlight">dados de mídia</span>
                    em insights estratégicos
                </h1>
                
                <p class="hero-description">
                    Monitoramento completo e análise inteligente de TV, Rádio, Jornal, Web e Mídias Sociais. 
                    Tenha o controle total da presença da sua marca na mídia.
                </p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Monitoramento</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">5.000+</span>
                        <span class="stat-label">Fontes Monitoradas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">1M+</span>
                        <span class="stat-label">Sites e Blogs</span>
                    </div>
                </div>
                
                <div class="hero-actions">
                    <a href="#servicos" class="btn btn-primary">
                        <span>Conhecer Serviços</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#contato" class="btn btn-secondary">
                        <i class="fas fa-calendar"></i>
                        <span>Agendar Demonstração</span>
                    </a>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="media-dashboard">
                    <div class="dashboard-header">
                        <div class="dashboard-title">Dashboard de Mídia</div>
                        <div class="dashboard-status">
                            <div class="status-dot"></div>
                            <span>Ao Vivo</span>
                        </div>
                    </div>
                    
                    <div class="media-cards">
                        <div class="media-card tv">
                            <div class="card-icon">
                                <i class="fas fa-tv"></i>
                            </div>
                            <div class="card-content">
                                <h4>TV</h4>
                                <span class="card-count">200+</span>
                                <span class="card-trend">Emissoras</span>
                            </div>
                        </div>
                        
                        <div class="media-card radio">
                            <div class="card-icon">
                                <i class="fas fa-tower-broadcast"></i>
                            </div>
                            <div class="card-content">
                                <h4>Rádio</h4>
                                <span class="card-count">750+</span>
                                <span class="card-trend">Emissoras</span>
                            </div>
                        </div>
                        
                        <div class="media-card web">
                            <div class="card-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="card-content">
                                <h4>Web</h4>
                                <span class="card-count">1M+</span>
                                <span class="card-trend">Sites</span>
                            </div>
                        </div>
                        
                        <div class="media-card social">
                            <div class="card-icon">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="card-content">
                                <h4>Impressos</h4>
                                <span class="card-count">4k+</span>
                                <span class="card-trend">Jornais</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-chart">
                        <div class="chart-bars">
                            <div class="bar" style="height: 60%"></div>
                            <div class="bar" style="height: 80%"></div>
                            <div class="bar" style="height: 45%"></div>
                            <div class="bar" style="height: 90%"></div>
                            <div class="bar" style="height: 70%"></div>
                            <div class="bar" style="height: 95%"></div>
                            <div class="bar" style="height: 55%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <div class="scroll-arrow">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="servicos" class="services">
        <div class="section-container">
            <div class="section-header">
                <span class="section-badge">Nossos Serviços</span>
                <h2 class="section-title">Buscamos as informações de seu interesse onde você quiser, onde ela estiver!</h2>
                <p class="section-description">
                    Monitoramento completo e análise inteligente com cobertura nacional e regional. 
                    Todas as notícias cadastradas em banco de dados para pesquisa e acesso digital.
                </p>
            </div>
            
            <div class="services-grid">
                <div class="service-card featured">
                    <div class="service-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <h3>Clipagem de TV</h3>
                    <p>Mais de 200 emissoras de TV Nacionais, Regionais e de Santa Catarina monitoradas diariamente</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> +200 emissoras monitoradas</li>
                        <li><i class="fas fa-check"></i> Cobertura nacional e regional</li>
                        <li><i class="fas fa-check"></i> Banco de dados digitalizado</li>
                        <li><i class="fas fa-check"></i> Pesquisa e acesso digital</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Entre em contato conosco!</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-tower-broadcast"></i>
                    </div>
                    <h3>Clipagem de Rádio</h3>
                    <p>Mais de 750 emissoras de Rádio para você ter a informação que você quer</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> +750 emissoras de rádio</li>
                        <li><i class="fas fa-check"></i> Nacionais e de Santa Catarina</li>
                        <li><i class="fas fa-check"></i> Pesquisa retroativa disponível</li>
                        <li><i class="fas fa-check"></i> Grande acervo de informações</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Entre em contato conosco!</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3>Clipagem de Jornal</h3>
                    <p>Mais de 4.000 impressos monitorados diariamente entre jornais e revistas</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> +4.000 impressos monitorados</li>
                        <li><i class="fas fa-check"></i> ~300 impressos de Santa Catarina</li>
                        <li><i class="fas fa-check"></i> +3.500 impressos nacionais</li>
                        <li><i class="fas fa-check"></i> Acervo totalmente digitalizado</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Entre em contato conosco!</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Clipagem de Web</h3>
                    <p>Mais de 1.000.000 de BLOGS, PORTAIS e SITES com robô poderoso para busca diária ou retroativa</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> +1.000.000 blogs, portais e sites</li>
                        <li><i class="fas fa-check"></i> Robô poderoso de busca</li>
                        <li><i class="fas fa-check"></i> Busca diária ou retroativa</li>
                        <li><i class="fas fa-check"></i> Processo manual e automático</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Entre em contato conosco!</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3>Monitoramento de Redes Sociais</h3>
                    <p>Ferramenta desenvolvida para otimizar o monitoramento das principais redes sociais com IA</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Palavras-chave estratégicas</li>
                        <li><i class="fas fa-check"></i> Análise de sentimento com IA</li>
                        <li><i class="fas fa-check"></i> Identificação de influenciadores</li>
                        <li><i class="fas fa-check"></i> Monitoramento de reputação</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Tenho interesse</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Análise de Mídia</h3>
                    <p>Ferramenta importante para tomada de decisões e verificação de investimentos nas mídias</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Relatórios quantitativos e qualitativos</li>
                        <li><i class="fas fa-check"></i> Relatórios comparativos por cidade</li>
                        <li><i class="fas fa-check"></i> Relatórios por veículos</li>
                        <li><i class="fas fa-check"></i> Retorno de mídia (ROI)</li>
                    </ul>
                    <a href="#contato" class="service-link">
                        <span>Tenho interesse</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <!-- New Features Section -->
            <div class="services-features">
                <div class="features-header">
                    <h3>Recursos Avançados</h3>
                    <p>Tecnologia de ponta para análise completa de mídia</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>Inteligência Artificial</h4>
                        <p>Análise de sentimentos automatizada com configuração simplificada e precisão avançada</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>Busca Avançada</h4>
                        <p>Sistema de busca poderoso para localizar informações específicas em nosso vasto banco de dados</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Análise de Influenciadores</h4>
                        <p>Identificação de influenciadores e possíveis detratores nas redes sociais</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Relatórios Personalizados</h4>
                        <p>Dashboards e relatórios executivos adaptados às necessidades do seu negócio</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="sobre" class="about">
        <div class="section-container">
            <div class="about-content">
                <div class="about-text">
                    <span class="section-badge">Sobre Nós</span>
                    <h2 class="section-title">Especialistas em monitoramento de mídia</h2>
                    <p>
                        Com anos de experiência no mercado, oferecemos soluções completas de monitoramento 
                        e análise de mídia para empresas de todos os portes.
                    </p>
                    <p>
                        Nossa tecnologia avançada combinada com expertise humana garante a precisão e 
                        relevância das informações que você precisa para tomar decisões estratégicas.
                    </p>
                    
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h4>Monitoramento 24/7</h4>
                                <p>Acompanhamento contínuo sem interrupções</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <h4>Dados Seguros</h4>
                                <p>Proteção total das suas informações</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <h4>Suporte Especializado</h4>
                                <p>Equipe dedicada para atender suas necessidades</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="about-visual">
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">10M+</span>
                                <span class="stat-label">Menções Monitoradas</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">500+</span>
                                <span class="stat-label">Clientes Ativos</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number">15+</span>
                                <span class="stat-label">Anos de Experiência</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contato" class="contact">
        <div class="section-container">
            <div class="contact-content">
                <div class="contact-info">
                    <span class="section-badge">Contato</span>
                    <h2 class="section-title">Vamos conversar sobre seu projeto</h2>
                    <p>
                        Entre em contato conosco e descubra como podemos ajudar sua empresa 
                        a monitorar e analisar sua presença na mídia.
                    </p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="method-info">
                                <h4>Florianópolis</h4>
                                <p>Rua Bento Gonçalves, 183 - Sala 602<br>Centro - Florianópolis/SC</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="method-info">
                                <h4>Blumenau</h4>
                                <p>Rua Padre Jacobs, 75<br>Centro - Blumenau/SC</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="method-info">
                                <h4>E-mail</h4>
                                <p>contato@studioclipagem.com.br</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="method-info">
                                <h4>Telefone</h4>
                                <p>(48) 3333-4444</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-container">
                    <form class="contact-form" id="contact-form">
                        <div class="form-header">
                            <h3>Solicite um orçamento</h3>
                            <p>Preencha o formulário e entraremos em contato</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nome">Nome completo</label>
                                <input type="text" id="nome" name="nome" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="empresa">Empresa</label>
                                <input type="text" id="empresa" name="empresa">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="servico">Serviço de interesse</label>
                            <select id="servico" name="servico" required>
                                <option value="">Selecione um serviço</option>
                                <option value="tv">Clipagem de TV (+200 emissoras)</option>
                                <option value="radio">Clipagem de Rádio (+750 emissoras)</option>
                                <option value="jornal">Clipagem de Jornal (+4.000 impressos)</option>
                                <option value="web">Clipagem de Web (+1M sites/blogs)</option>
                                <option value="social">Monitoramento de Redes Sociais</option>
                                <option value="analise">Análise de Mídia</option>
                                <option value="completo">Pacote Completo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="midias-sociais">Mídias Sociais de interesse</label>
                            <input type="text" id="midias-sociais" name="midias-sociais" placeholder="Ex: Facebook, Instagram, Twitter, LinkedIn...">
                        </div>
                        
                        <div class="form-group">
                            <label for="mensagem">Observações</label>
                            <textarea id="mensagem" name="mensagem" rows="4" placeholder="Conte-nos mais sobre suas necessidades e objetivos de monitoramento..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            <span>Enviar Solicitação</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection