<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 4px; text-align: left; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Relatório de Compra</h1>
    <p><strong>Nome:</strong> {{ $data['nome'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>

    <h2>Itens Comprados</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['itens'] as $item)
                <tr>
                    <td>{{ $item['produto'] }}</td>
                    <td>{{ $item['quantidade'] }}</td>
                    <td>R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item['quantidade'] * $item['preco'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>