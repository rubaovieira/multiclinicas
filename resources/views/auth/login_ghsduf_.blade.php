<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="w-100" style="max-width: 400px; border: 1px solid #000; padding: 20px; border-radius: 10px;">
        <div class="text-center mb-4">
            <picture>
                <img src="{{ config('app.logo_url') }}" alt="Login Icon" width="80px" class="img-fluid">
            </picture>
        </div>

        @if (session('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        <h2 class="text-center mb-4">Login</h2>
        <p class="text-center mb-4 text-muted">{{ $clinica->nome }}</p>

        <form action="{{ route('login', ['slug' => $clinica->slug]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Usuário</label>
                <input id="email" type="email" name="email" class="form-control" required
                    placeholder="Digite seu email" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input id="password" type="password" name="password" class="form-control" required
                    placeholder="Digite sua senha">
            </div>

            <button type="button" id="loginButton" class="btn btn-primary btn-block">Entrar</button>

            <div class="text-center mt-3">
                {{-- <a href="#" class="text-decoration-none">Esqueceu sua senha?</a><br> --}}
                <a href="{{ route('register', ['slug' => $clinica->slug]) }}"
                    class="text-decoration-none text-primary">Registrar-se</a>
            </div>
        </form>
    </div>

    <!-- Modal para código de verificação -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalLabel">Verificação de Código</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Um código foi enviado para o seu email. Insira o código abaixo para continuar.</p>
                    <div class="form-group">
                        <label for="verificationCode">Código de Verificação</label>
                        <input id="verificationCode" type="text" class="form-control" required
                            placeholder="Digite o código">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="verifyCodeButton" class="btn btn-success">Verificar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 4 JS + dependências -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Se quiser usar jQuery normal (não slim) para Ajax -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginButton').click(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('send-email') }}",
                    type: "POST",
                    data: {
                        email: $('#email').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#verificationModal').modal('show');
                    },
                    error: function(error) {
                        console.error("Erro ao enviar o email:", error);
                        alert('Erro ao enviar o código de verificação. Tente novamente.');
                    }
                });
            });

            $('#verifyCodeButton').click(function() {
                const code = $('#verificationCode').val();
                const email = $('#email').val();

                $.ajax({
                    url: "{{ route('verificar-email') }}",
                    type: "POST",
                    data: {
                        code: code,
                        email: email,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#verificationModal').modal('hide');
                        $('form').submit();
                    },
                    error: function(xhr) {
                        alert('Código incorreto. Tente novamente.');
                    }
                });
            });
        });
    </script>

</body>

</html>
