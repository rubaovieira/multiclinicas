<form action="{{ route('service') }}" method="POST">
    @csrf
    <input type="hidden" name="service_id" value='${$id}'>
    <input type="hidden" name="type" value="medicine">
    <div class="mb-3">
        <label for="medicamento" class="form-label">Cliente <button class="btn btn-primary btn-sm" 
            onclick="window.location.href='{{ route('new-client') }}';" type="button"
        >+ Novo cliente</button></label>
         <select class="form-control" name="client_id">
            @foreach($clients as $client)
                <option value="{{ $client->id }}"  {{ (isset($client_id) && $client_id === $client->id ) ?'selected':'' }}>{{ $client->name }}</option>
            @endforeach
        </select>
    </div> 
    <div class="mb-3" hidden>
        <label for="diagnostico" class="form-label">Diagnóstico</label>
        <textarea class="form-control" id="diagnostico"  name="diagnostico" required>-</textarea>
    </div>
    <hr>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary">Iniciar</button>
</form>