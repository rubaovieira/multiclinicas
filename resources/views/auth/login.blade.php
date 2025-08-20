@extends('layouts.app')

@section('title', 'Login')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px;">
            <div class="card-body p-5">
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

                <h4 class="text-center fw-bold mb-4 text-black">Login</h4>
                <form action="{{ route('login', ['slug' => $slug ?? 'master']) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email" class="form-label text-black">Usuário</label>
                        <input id="email" type="email" name="email" class="form-control" required
                            placeholder="Digite seu email">
                    </div>

                    <div class="form-group mb-4 password-container">
                        <label for="password" class="form-label text-black">Senha</label>
                        <input id="password" type="password" name="password" class="form-control" required
                            placeholder="Digite sua senha">
                        <i class="bi bi-eye" id="togglePassword" style="cursor: pointer; margin-top:15px"></i>
                    </div>

                    <button type="button" id="loginButton" class="btn btn-primary w-100">Entrar</button>

                </form>

                <div class="text-center mt-3">
                    {{-- <a href="#" class="text-decoration-none text-white">Esqueceu sua senha?</a> --}}
                    @if($slug)
                        <a href="{{ route('register-client', ['slug' => $slug]) }}" class="text-decoration-none text-blue">Registrar-se</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para código de verificação -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalLabel">Verificação de Código</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Um código foi enviado para o seu email. Insira o código abaixo para continuar.</p>
                    <div class="form-group">
                        <label for="verificationCode" class="form-label">Código de Verificação</label>
                        <input id="verificationCode" type="text" class="form-control" required
                            placeholder="Digite o código">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="verifyCodeButton" class="btn btn-success ">Verificar</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Exibir/ocultar senha
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('bi-eye bi-eye-slash');
            });

            // Mostrar modal ao clicar em "Entrar"
            $('#loginButton').click(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('send-email') }}",
                    type: "POST",
                    data: {
                        email: $('#email').val(), // Captura o email digitado
                        _token: "{{ csrf_token() }}" // Token CSRF para segurança
                    },
                    success: function(response) {
                        $('#verificationModal').modal('show'); // Mostra o modal
                    },
                    error: function(error) {
                        console.error("Erro ao enviar o email:", error);
                        alert('Erro ao enviar o código de verificação. Tente novamente.');
                    }
                });

                $('#verificationModal').modal('show');
            });

            $('#verifyCodeButton').click(function() {
                const code = $('#verificationCode').val();
                const email = $('#email').val();

                $.ajax({
                    url: '{{ route('verificar-email') }}',
                    method: 'POST',
                    data: {
                        code: code,
                        email: email,
                        _token: '{{ csrf_token() }}' // Inclua o token CSRF para segurança
                    },
                    success: function(response) {
                        $('#verificationModal').modal('hide');
                        $('form').submit(); // Submete o formulário de login
                    },
                    error: function(xhr) {
                        alert('Código incorreto. Tente novamente.');
                    }
                });
            });
        });
    </script>
@endsection
