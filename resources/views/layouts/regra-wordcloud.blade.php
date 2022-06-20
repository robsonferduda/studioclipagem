<div class="row">
    <div class="col-lg-12 col-md-12 mb-2">
        <div class="form-group">
            <label>Selecione uma regra</label>
            <select class="form-control load_expression" name="regra" id="regra">
                <option value="">Selecione uma regra</option>
                @foreach($rules as $rule)
                    <option value="{{ $rule->id }}" data-expression="{{ $rule->getExpression() }}">{{ $rule->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 mb-2">
        <label>Expressão</label>
        <span class="d-block mb-2 display_regra">Nenhuma regra selecionada</span>
    </div>
</div>
