<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regra Processada</title>
</head>
<body>
    @component('mail::message')
        @foreach ($introLines as $line)
            <p>{!! $line !!}</p>
        @endforeach
    @endcomponent
</body>
</html>