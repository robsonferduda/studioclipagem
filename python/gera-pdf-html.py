import pdfkit

# Gera PDF a partir de uma string HTML
html = """
<h1>Olá Mundo!</h1>
<p>Este é um exemplo de PDF gerado com Python.</p>
"""

pdfkit.from_string(html, 'saida.pdf')