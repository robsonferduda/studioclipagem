@if(\Session::has('flash_notification'))
    @foreach (Session::get('flash_notification') as $message)
        <div class="alert alert-{{ $message['level'] }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! $message['message'] !!}
        </div>
    @endforeach
@endif    
{{ Session::forget('flash_notification') }}

@if ($errors->any())
	@foreach($errors->all() as $error)
	
		<div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <i class="fa fa-warning"></i> {!! $error !!}
    	</div>

	@endforeach
@endif
