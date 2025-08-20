<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'aplicação')</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

    <link rel="icon" href="{{ config('app.logo_url') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- FilePond -->
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
        rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">


    <!-- CSS do Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- CSS geral -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- CSS breadcrumb -->
    <link rel="stylesheet" href="{{ asset('css/breadcrumb.css') }}">
    @yield('styles')
</head>


@if ($errors->any())
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast show bg-{{ session('toastType', 'danger') }} text-white" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Aviso</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                @foreach ($errors->all() as $error)
                    <div class='text-black'>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    </div>
@endif


<body>

    {{-- @auth
        @include('layouts/menu')
    @endauth --}}
    {{-- 

    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Sucesso</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        @yield('content')
    </div> 
    --}}
    


    @if (!request()->is('login*'))
        @include('layouts/menu')
    @endif

    @if (request()->is('login*'))
        <div class="container">
            @yield('content')
        </div>
    @endif





    <!-- Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- FilePond -->
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js">
    </script>
    <script
        src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <!-- JS do Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- JS da mascara -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.toast').delay(3000).fadeOut(500);
            FilePond.registerPlugin(FilePondPluginImagePreview);
            FilePond.create(document.querySelector('.filepond'));
            $('.select2').select2({
                tags: false, // Permite a adição de novas opções
                placeholder: "Selecione ou adicione um Morador",
                allowClear: true
            });
        });

        //mascara para o campo de telefone $(".phone").mask("(00) 0000-00009");
        $(".phone").mask("(00) 90000-0000", {
            translation: {
                9: {
                    pattern: /[0-9]/,
                    optional: true
                } // O dígito 9 é opcional
            }
        });

        //mascara para cpf $(".cpf").mask("000.000.000-00");
        $(".cpf").mask("000.000.000-00");

        //mascara para data $(".date").mask("00/00/0000");
        $(".date").mask("00/00/0000");

        //mascara para hora $(".time").mask("00:00");
        $(".time").mask("00:00");
    </script>

    @yield('scripts')
</body>

</html>
