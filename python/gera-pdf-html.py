# generate_pdf.py
import asyncio
import sys
import os
from pyppeteer import launch
from playwright.async_api import async_playwright

# Define onde o Pyppeteer vai salvar o Chromium
os.environ['PYPPETEER_HOME'] = '/tmp/pyppeteer_cache'

PDF_OUTPUT_DIR = "/var/www/studioclipagem/storage/app/public/relatorios-pdf"

async def main(html_content, filename):
    try:
        os.makedirs(PDF_OUTPUT_DIR, exist_ok=True)
        pdf_path = os.path.join(PDF_OUTPUT_DIR, f"{filename}")

        browser = await launch(headless=True)
        page = await browser.newPage()
        await page.setContent(html_content)
        await asyncio.sleep(10)

        await page.pdf({
            'path': pdf_path,
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

        print(pdf_path)

    except Exception as e:
        print(f"ERRO: {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("ERRO: Forneça o HTML e o nome do arquivo como argumentos.", file=sys.stderr)
        sys.exit(1)

    html = sys.argv[1]
    filename = sys.argv[2]

    asyncio.run(main(html, filename))