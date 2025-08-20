<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>

    <link rel="icon" href="{{ config('app.logo_url') }}">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="w-100" style="max-width: 600px;">
        <div class="text-center mb-4">
            <picture>
                <img src="{{ config('app.logo_url') }}" alt="Login Icon" width="80px" class="img-fluid">
            </picture>
        </div>
        <h2 class="text-center mb-4">Cadastro</h2>
        @if ($clinica)
            <p class="text-center mb-4 text-muted">{{ $clinica->nome }}</p>
        @endif

        <form action="{{ route('register-client', ['slug' => $slug ?? 'master']) }}" method="POST">
            @csrf
            @if ($clinica)
                <input type="hidden" name="clinica_id" value="{{ $clinica->id }}">
            @endif

            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" placeholder="Digite seu nome completo" value="{{ old('name') }}">
            </div>

            {{ $errors->has('name') ? $errors->first('name') : '' }}

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" class="form-control cpf @error('cpf') is-invalid @enderror" id="cpf"
                    name="cpf" required placeholder="Digite seu CPF" value="{{ old('cpf') }}">
            </div>

            {{ $errors->has('cpf') ? $errors->first('cpf') : '' }}

            <div class="form-group">
                <label for="date_birth">Data de Nascimento</label>
                <input type="date" class="form-control @error('date_birth') is-invalid @enderror" id="date_birth"
                    name="date_birth" required value="{{ old('date_birth') }}">
            </div>

            {{ $errors->has('date_birth') ? $errors->first('date_birth') : '' }}

            <div class="form-group">
                <label for="telephone">Telefone</label>
                <input type="text" class="form-control phone @error('telephone') is-invalid @enderror" id="telephone"
                    name="telephone" required placeholder="Digite seu telefone" value="{{ old('telephone') }}">
            </div>

            {{ $errors->has('telephone') ? $errors->first('telephone') : '' }}

            <div class="form-group">
                <label for="address">Endereço</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                    name="address" required placeholder="Digite seu endereço" value="{{ old('address') }}">
            </div>

            {{ $errors->has('address') ? $errors->first('address') : '' }}

            {{-- <div class="form-group">
                <label for="diagnosis">Diagnóstico</label>
                <input type="text" class="form-control" id="diagnosis" name="diagnosis" required placeholder="Digite o diagnóstico">
            </div> --}}

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" required placeholder="Digite seu email" value="{{ old('email') }}">
            </div>

            {{ $errors->has('email') ? $errors->first('email') : '' }}

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" required placeholder="Digite sua senha" value="{{ old('password') }}">
            </div>

            {{ $errors->has('password') ? $errors->first('password') : '' }}

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation" name="password_confirmation" required placeholder="Confirme sua senha"
                    value="{{ old('password_confirmation') }}">
            </div>

            {{ $errors->has('password_confirmation') ? $errors->first('password_confirmation') : '' }}

            <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
        </form>
    </div>

    <!-- Bootstrap 4 JS + dependências -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.cpf').mask('000.000.000-00');
            $('.phone').mask('(00) 00000-0000');
        });
    </script>
</body>

</html>
