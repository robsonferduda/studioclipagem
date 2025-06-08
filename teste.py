import asyncio
from playwright.async_api import async_playwright

async def gerar_pdf():
    # Lê o conteúdo HTML
    with open("relatorio.html", "r", encoding="utf-8") as f:
        conteudo_html = f.read()

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        # Define o conteúdo HTML e espera o carregamento completo
        await page.set_content(conteudo_html, wait_until='networkidle')

        # Gera o PDF
        await page.pdf(
            path="relatorio.pdf",
            format="A4",
            margin={"top": "20mm", "bottom": "20mm", "left": "10mm", "right": "10mm"},
            print_background=True
        )

        await browser.close()

# Executa o script
asyncio.run(gerar_pdf())
