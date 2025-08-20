 @extends('layouts.app')

 @section('title', 'Cadastro de Usuários')

 @section('content')
     <div class="d-flex justify-content-center align-items-center">
         <div class="card border-0 shadow-lg rounded-3" style="width: 100%;">
             <div class="card-body p-4">
                 <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <div>
                         <label for="name">Name*</label>
                         <input type="text" name="name" class="form-control" required>
                     </div>
                     <div>
                         <label for="cpf">CPF*</label>
                         <input type="text" name="cpf" class="form-control cpf" required>
                     </div>
                     <div>
                         <label for="address">Endereço*</label>
                         <input type="text" name="address" class="form-control" required>
                     </div>
                     <div>
                         <label for="telephone">Telefone*</label>
                         <input type="text" name="telephone" class="form-control phone" required>
                     </div>
                     <div>
                         <label for="advice">Número de conselho*</label>
                         <input type="text" name="advice" class="form-control " required>
                     </div>
                     <div>
                         <label for="perfil">Perfil*</label>
                         <select name="perfil" class="form-control" required>
                             <option value="">Selecione</option>
                             <option value="admin">Admin Total</option>
                             <option value="medico">Médico</option>
                             <option value="efermeria">Efermeria</option>
                             <option value="equipe-tecnica">Equipe técnica</option>
                             <option value="equipe-multidisciplinar">Equipe multidisciplinar</option>
                         </select>
                     </div>
                     <div>
                         <label for="email">Email*</label>
                         <input type="email" name="email" class="form-control" required>
                     </div>
                     <div>
                         <label for="password">Password*</label>
                         <input type="password" name="password" class="form-control" required>
                     </div>
                     <div>
                         <label for="observation">Obsevação</label>
                         <textarea name="observation" class="form-control"></textarea>
                     </div>
                     <div>
                         <label for="carimbo">Carimbo do médico</label>
                         <input type="file" name="carimbo" class="form-control">
                     </div>
                     <br>
                     <button type="submit" class="btn btn-primary w-100">Register</button>
                 </form>
             </div>
         </div>
     </div>
 @endsection
