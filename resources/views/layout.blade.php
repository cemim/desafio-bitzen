<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Painel de Controle</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">    
</head>
<body>
    <div class="title-painel">
        <div>
            <h1>Painel de Controle API V1 - Gerenciar Parceiros</h1>
        </div>        
    </div>    
    <main>
        @hasSection('body')
            @yield('body')
        @endif
    </main>
    <script type="text/javascript" src="{{asset('js/jquery-3.7.1.min.js')}}"></script>
    @hasSection ('javascript')
        @yield('javascript')
    @endif
</body>
</html>