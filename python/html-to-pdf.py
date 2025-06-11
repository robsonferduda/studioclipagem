from playwright.sync_api import sync_playwright

def html_to_pdf_playwright(html, filename):
    with sync_playwright() as p:

        browser = p.chromium.launch()
        page = browser.new_page()
        
        # Injeta CSS para controlar imagens
        css_inject = """
        <style>
        img {
            max-width: 100% !important;
            height: auto !important;
            page-break-inside: avoid !important;
        }
        
        @media print {
            img {
                max-height: 90vh !important;
                width: auto !important;
                object-fit: contain !important;
            }
        }
        </style>
        """
        
        # Adiciona CSS ao HTML
        html_with_css = html_content.replace('</head>', f'{css_inject}</head>')
        
        page.set_content(html_with_css)
        
        # Aguarda todas as imagens carregarem
        page.wait_for_load_state('networkidle')

        PDF_OUTPUT_DIR = "/var/www/studioclipagem/storage/app/public/relatorios-pdf"+filename
        
        # Gera PDF com configurações específicas
        page.pdf(
            path=PDF_OUTPUT_DIR,
            format='A4',
            margin={
                'top': '1cm',
                'right': '1cm',
                'bottom': '1cm',
                'left': '1cm'
            },
            print_background=True
        )
        
        browser.close()


# Exemplo de uso
if __name__ == "__main__":

    html = sys.argv[1]
    filename = sys.argv[2]
    
    html_to_pdf_playwright(html, filename)    