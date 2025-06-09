import asyncio
from pathlib import Path
from playwright.async_api import async_playwright

async def gerar_pdf():
    html_path = Path("clipagem_ajustada.html")
    output_path = Path("clipagem-ajustada.pdf")

    html = html_path.read_text(encoding="utf-8")

    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.set_content(html, wait_until="networkidle")
        await page.pdf(
            path=str(output_path),
            format="A4",
            print_background=True,
            margin={"top": "20mm", "bottom": "20mm", "left": "10mm", "right": "10mm"}
        )
        await browser.close()

asyncio.run(gerar_pdf())
