<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Clipagem - Monitoramento de Mídia Profissional</title>
    <meta name="description" content="Studio Clipagem oferece serviços profissionais de monitoramento e análise de mídia: TV, Rádio, Jornal, Web e Mídias Sociais.">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <span class="brand-text">Studio</span>
                <span class="brand-accent">Clipagem</span>
            </div>
            <ul class="nav-menu" id="nav-menu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#servicos" class="nav-link">Serviços</a></li>
                <li><a href="#sobre" class="nav-link">Sobre</a></li>
                <li><a href="#contato" class="nav-link">Contato</a></li>
                <li><a href="{{ env('BASE_URL').'/login' }}" class="nav-link nav-cta">Área Restrita</a></li>
            </ul>
            <div class="nav-toggle" id="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

   @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="brand-logo">
                        <span class="brand-text">Studio</span>
                        <span class="brand-accent">Clipagem</span>
                    </div>
                    <p>Monitoramento profissional de mídia com tecnologia avançada e expertise humana.</p>
                </div>
                
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Serviços</h4>
                        <ul>
                            <li><a href="#servicos">Clipagem de TV</a></li>
                            <li><a href="#servicos">Clipagem de Rádio</a></li>
                            <li><a href="#servicos">Clipagem de Jornal</a></li>
                            <li><a href="#servicos">Clipagem de Web</a></li>
                            <li><a href="#servicos">Mídias Sociais</a></li>
                            <li><a href="#servicos">Análise Estratégica</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Empresa</h4>
                        <ul>
                            <li><a href="#sobre">Sobre Nós</a></li>
                            <li><a href="#contato">Contato</a></li>
                            <li>
                                <a href="https://studioclipagem.com/login">Área Restrita</a>
                            </li>
                            <li>
                                <a href="{{ url('politica-de-privacidade') }}">Política de Privacidade</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Contato</h4>
                        <div class="footer-contact">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Florianópolis</strong>
                                    <p>Rua Bento Gonçalves, 183 - Sala 602<br>Centro - Florianópolis/SC</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Blumenau</strong>
                                    <p>Rua Padre Jacobs, 75<br>Centro - Blumenau/SC</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>E-mail</strong>
                                    <p>contato@studioclipagem.com.br</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Telefone</strong>
                                    <p>(48) 3333-4444</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 Studio Clipagem. Todos os direitos reservados.</p>
                    <div class="footer-legal">
                        <span>Desenvolvido com ❤️ para monitoramento de mídia</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5548988553212" target="_blank" class="whatsapp-float" id="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-tooltip">Fale conosco no WhatsApp</span>
    </a>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html> 