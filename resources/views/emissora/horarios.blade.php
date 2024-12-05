@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Horários 
                    </h4>
                </div>
                <div class="col-md-4">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                @php 
                    $label_dias = array('DOM','SEG','TER','QUA','QUI','SEX','SAB');
                @endphp
                {!! Form::open(['id' => 'frm_emissora_horarios', 'class' => 'form-horizontal', 'url' => ['emissoras/horario/adicionar']]) !!}
                    <input type="hidden" name="id_emissora" value="{{ $id_emissora }}" />
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Horário Inicial <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" class="form-control duracao" name="hora_inicial" id="hora_inicial" required placeholder="00:00:00" value="">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Horário Final <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" class="form-control duracao" name="hora_final" id="hora_final" required placeholder="00:00:00" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6" style="margin-top: 32px;">
                                    @for($i=0; $i < 7; $i++)                        
                                        <div class="form-check" style="display: inline; float: left;">
                                            <label class="form-check-label" style="padding-right: 15px; padding-left: 30px; font-size: 16px;">
                                                <input class="form-check-input" type="checkbox" name="dia_{{ $i }}" value="true">
                                                    {{  $label_dias[$i] }} 
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>                        
                                    @endfor
                                </div>
                                <div class="col-md-1 col-sm-1" style="margin-top: 32px;">
                                    <button type="submit" id="" class="btn btn-success" style="margin-top: -5px;"><i class="fa fa-plus"></i> </button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 
            </div>
            <div class="col-md-12">
                <h6 class="mt-4">Horários de Gravação</h6>
                @foreach($horarios as $key => $horario)
                    <div>
                        <h5>
                            <span class="badge badge-default" style="background: #4CAF50 !important; border-color: #4CAF50 !important;"> {{ $horario->horario_start }}</span> 
                            <span class="badge badge-danger" style="">{{ $horario->horario_end }}</span>
                        </h5>
                        @php 
                            $dias = explode(',',$horario->dias_da_semana);
                        @endphp
                        @for($i=0; $i < 7; $i++)                        
                            <div class="form-check" style="display: inline; float: left;">
                                <label class="form-check-label" style="padding-right: 15px; padding-left: 30px; font-size: 16px;">
                                    <input class="form-check-input" type="checkbox" name="resetar_situacao" value="true" {{ in_array( $i, $dias) ? 'checked' : '' }}>
                                        {{  $label_dias[$i] }} 
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>                        
                        @endfor
                    </div>
                    <div class="clear"></div>
                    @if($key < (count($horarios) -1))
                        <hr/>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div> 
@endsection