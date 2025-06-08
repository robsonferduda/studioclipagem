# generate_pdf.py
import asyncio
import sys
import os
import warnings
from pyppeteer import launch

# Suprime o aviso chato
warnings.filterwarnings("ignore", category=RuntimeWarning, message="coroutine.*was never awaited")

# Define onde o Pyppeteer vai salvar o Chromium
os.environ['PYPPETEER_HOME'] = '/tmp/pyppeteer_cache'

PDF_OUTPUT_DIR = "/var/www/studioclipagem/storage/app/public/relatorios-pdf"

async def main(html_content, filename):
    browser = None
    try:
        os.makedirs(PDF_OUTPUT_DIR, exist_ok=True)
        pdf_path = os.path.join(PDF_OUTPUT_DIR, f"{filename}")

        browser = await launch(headless=True)
        page = await browser.newPage()
        await page.setContent(html_content)

        await page.pdf({
            'path': pdf_path,
            'format': 'A4',
            'printBackground': True
        })

        print(pdf_path)

    except Exception as e:
        print(f"ERRO: {e}", file=sys.stderr)
        sys.exit(1)

    finally:
        if browser:
            await browser.close()


if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("ERRO: Forneça o HTML e o nome do arquivo como argumentos.", file=sys.stderr)
        sys.exit(1)

    html = sys.argv[1]
    filename = sys.argv[2]

    # Garantia adicional de loop
    if sys.platform == "linux" or sys.platform == "darwin":
        policy = asyncio.get_event_loop_policy()
        policy.set_event_loop(policy.new_event_loop())

    asyncio.run(main(html, filename))