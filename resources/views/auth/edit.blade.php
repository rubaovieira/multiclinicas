@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="d-flex justify-content-center align-items-center"> 
    <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
        <div class="card-body p-4">   
            <form action="{{ route('edit-users', ['id' => $user->id ]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="name">Name*</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div>
                    <label for="cpf">CPF*</label>
                    <input type="text" name="cpf" class="form-control cpf" value="{{ $user->cpf }}" required>
                </div>
                <div>
                    <label for="address">Endereço*</label>
                    <input type="text" name="address" class="form-control" value="{{ $user->address }}" required>
                </div>
                <div>
                    <label for="telephone">Telefone*</label>
                    <input type="text" name="telephone" class="form-control phone" value="{{ $user->telephone }}" required>
                </div>
                <div>
                    <label for="advice">Número de conselho*</label>
                    <input type="text" name="advice" class="form-control" value="{{ $user->advice }}" required>
                </div>
                <div>
                    <label for="perfil">Perfil*</label>
                    <select name="perfil" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="admin" {{ ($user->perfil == 'admin') ? 'selected' : ''  }}>Admin</option>
                        <option value="medico" {{ ($user->perfil == 'medico') ? 'selected' : ''  }}>Médico</option>
                        <option value="efermeria" {{ ($user->perfil == 'efermeria') ? 'selected' : ''  }}>Efermeria</option>
                        <option value="equipe-tecnica" {{ ($user->perfil == 'equipe-tecnica') ? 'selected' : ''  }}>Equipe técnica</option>
                        <option value="equipe-multidisciplinar" {{ ($user->perfil == 'equipe-multidisciplinar') ? 'selected' : ''  }}>Equipe multidisciplinar</option>
                    </select> 
                </div> 
                <div>
                    <label for="email">Email*</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="*********">
                </div> 
                <div>
                    <label for="observation">Observação</label>
                    <textarea name="observation" class="form-control">{{ $user->observation }}</textarea>
                </div>  
                <div>
                    <label for="carimbo">Carimbo do médico</label>
                    <input type="file" name="carimbo" class="form-control" id="carimboInput">
                </div>
                <br>
                
                <!-- Preview da imagem selecionada -->
                <div id="previewContainer" style="display:none;">
                    <p><b>Pré-visualização do Carimbo novo:</b></p>
                    <img id="previewImage" class="img-fluid" style="max-width: 20%;" />
                </div>

                <p><b>Carimbo atual: </b></p>

                @if ($user->carimbo)
                    <img src="{{ asset('storage/' . $user->carimbo) }}" style="max-width: 20%;" alt="Carimbo do médico" class="img-fluid" id="carimboPreview">
                @else
                    <p>Carimbo não cadastrado</p>
                @endif


                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form> 

        </div>
    </div>
</div>

<script>
    // Função para mostrar a pré-visualização da imagem
    document.getElementById('carimboInput').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Exibir a imagem do carimbo
                document.getElementById('previewContainer').style.display = 'block';
                document.getElementById('previewImage').src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Ocultar a área de pré-visualização se nenhum arquivo for selecionado
            document.getElementById('previewContainer').style.display = 'none';
        }
    });
</script>

@endsection
