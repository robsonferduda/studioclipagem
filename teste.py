import asyncio
from pyppeteer import launch

async def gerar_pdf():
    # Lê o HTML local
    with open("relatorio.html", "r", encoding="utf-8") as f:
        conteudo_html = f.read()

    # Inicia o navegador
    browser = await launch(headless=True, args=['--no-sandbox'])
    page = await browser.newPage()

    # Define o conteúdo da página
    await page.setContent(conteudo_html)
    await asyncio.sleep(10) 


    # Gera o PDF
    await page.pdf({
        'path': 'relatorio.pdf',
        'format': 'A4',
        'printBackground': True,
        'margin': {
            'top': '20mm',
            'bottom': '20mm',
            'left': '10mm',
            'right': '10mm'
        }
    })

    await browser.close()

# Executa a função
asyncio.get_event_loop().run_until_complete(gerar_pdf())