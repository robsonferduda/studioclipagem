from pathlib import Path
from playwright.async_api import async_playwright
import asyncio

# HTML corrigido com estrutura adequada
html_content = """
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .clipagem-item {
            page-break-before: always;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .imagem, .header {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .header h5 {
            text-align: justify;
            margin-bottom: 0px;
            padding-bottom: 5px;
            margin-top: 26px;
            font-size: 17px;
            border-bottom: 1px solid #ccc;
        }
        .header p {
            text-align: justify;
            font-size: 16px;
            margin: 0;
            padding: 0;
            margin-top: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="clipagem-item">
        <div class="header">
            <h5>ND Mais</h5>
            <p>
                08/06/2025
                <a href="https://ndmais.com.br/turismo/estamos-no-radar-do-brasil-praia-grande-lanca-projeto-que-aposta-no-turismo-sustentavel/" target="_blank">
                    <img style="width: 20px; height: 20px;" src="https://studioclipagem.com/img/globe.png" alt="link">
                </a>
            </p>
        </div>
        <div class="imagem">
            <img src="https://studioclipagem.com/img/noticia-web/36323975.jpg" alt="Notícia">
        </div>
    </div>
</body>
</html>
"""

# Função para gerar o PDF com Playwright
async def gerar_pdf(html_content: str, output_path: Path):
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.set_content(html_content, wait_until="networkidle")
        await page.pdf(
            path=str(output_path),
            format="A4",
            print_background=True,
            margin={"top": "20mm", "bottom": "20mm", "left": "10mm", "right": "10mm"}
        )
        await browser.close()

# Caminho para salvar o PDF
output_pdf_path = Path("/mnt/data/clipagem-ajustada.pdf")

# Executar a geração
await gerar_pdf(html_content, output_pdf_path)

output_pdf_path.name
