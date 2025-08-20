<form action="{{ route('product-create') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value='{{ @$id }}'>
    <div class="mb-3">
        <label for="name" class="form-label">Nome</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ @$product->name }}" required>
    </div>  
    <div class="mb-3">
        <label for="qtd" class="form-label">Quantidade Mínima</label>
        <input type="number" class="form-control"  min="1" id="qtd_min" value="{{ (isset($product->qtd_min) && $product->qtd_min)? $product->qtd_min :1 }}" name="qtd_min" required>
    </div>
    <hr>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary">Cadastrar</button>
</form>