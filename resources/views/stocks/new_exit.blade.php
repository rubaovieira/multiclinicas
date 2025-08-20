<form action="{{ route('product-create-exit') }}" method="POST">
    @csrf

    <label for="product_id" class="form-label">Produto</label>
    <select name="product_id" id="product_id" class="form-select" required>
        <option value="">Selecione</option>
        @foreach($prods as $prod)
            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
        @endforeach 
    </select>
    <div class="mb-3">
        <label for="qtd" class="form-label">Quantidade</label>
        <input type="number" class="form-control" value="1" min="1" id="qtd" name="qtd" required>
    </div>
    <hr>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary">Cadastrar</button>
</form>