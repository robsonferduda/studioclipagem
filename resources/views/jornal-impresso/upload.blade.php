@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Upload 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-newspaper-o"></i> Dashboard</a>
                    <a href="{{ url('jornal-impresso/processamento') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-cogs"></i> Processamento</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-lg-12 col-md-3 mb-12">
                <div class="form-group" style="">
                    <div class='content'>
                        <span>Busque ou arraste os arquivos</span>
                        {{ Form::open(array('url' => 'jornal-impresso/upload', 'method' => 'POST', 'name'=>'product_images', 'id'=>'dropzone', 'class'=>'dropzone', 'files' => true)) }}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

    Dropzone.options.dropzone =
         {
	    maxFiles: 5, 
            maxFilesize: 4,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            timeout: 500,
            init:function() {

				// Get images
				var myDropzone = this;
				$.ajax({
					url: host+'/public/jornal-impresso/pendentes',
					type: 'GET',
					dataType: 'json',
					success: function(data){
					console.log(data);
					$.each(data, function (key, value) {

						var file = {name: value.name, size: value.size};
						myDropzone.options.addedfile.call(myDropzone, file);
						myDropzone.options.thumbnail.call(myDropzone, file, value.path);
						myDropzone.emit("complete", file);
					});
					}
				});
			},
                
            success: function(file, response) 
            {
				file.previewElement.id = response.success;
				console.log(file); 
				//set new images names in dropzone’s preview box.
                var olddatadzname = file.previewElement.querySelector("[data-dz-name]");   
				file.previewElement.querySelector("img").alt = response.success;
				olddatadzname.innerHTML = response.success;
            },
            error: function(file, response)
            {
               if($.type(response) === "string")
					var message = response; //dropzone sends it's own error messages in string
				else
					var message = response.message;
				file.previewElement.classList.add("dz-error");
				_ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
				_results = [];
				for (_i = 0, _len = _ref.length; _i < _len; _i++) {
					node = _ref[_i];
					_results.push(node.textContent = message);
				}
				return _results;
            }
            
};
});
</script>
@endsection