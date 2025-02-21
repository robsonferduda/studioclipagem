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
                    $label_dias = array('SEG','TER','QUA','QUI','SEX','SAB','DOM');
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
                <h6 class="mt-4">{{ $emissora->nome_emissora }} - Horários de Gravação</h6>
                <div>
                    @foreach($horarios as $key => $horario)
                        <div class="box-horario box-horario-{{ $horario->id }}">
                            <h5>
                                <span class="badge badge-default" style="background: #4CAF50 !important; border-color: #4CAF50 !important;"> {{ $horario->horario_start }}</span> 
                                <span class="badge badge-danger" style="">{{ $horario->horario_end }}</span>
                                <a title="Excluir" href="{{ url('radio/emissora/horario/excluir', $horario->id) }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                            </h5>
                            @php 
                                if($horario->dias_da_semana != ""){
                                    $dias = explode(',',trim($horario->dias_da_semana));
                                    $flag = true;
                                }else{
                                    $flag = false;
                                }
                            @endphp
                            @if($flag)
                                @for($i=0; $i < 7; $i++)                        
                                    <div class="form-check" style="display: inline; float: left;">
                                        <label class="form-check-label" style="padding-right: 15px; padding-left: 30px; font-size: 16px;">
                                            <input class="form-check-input atualiza-dia-gravacao" type="checkbox" data-dia="{{ $i }}" data-horario="{{ $horario->id }}" name="resetar_situacao" value="true" {{ in_array( $i, $dias) ? 'checked' : '' }}>
                                                {{  $label_dias[$i] }} 
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>                        
                                @endfor
                            @else
                                @for($i=0; $i < 7; $i++)                        
                                        <div class="form-check" style="display: inline; float: left;">
                                            <label class="form-check-label" style="padding-right: 15px; padding-left: 30px; font-size: 16px;">
                                                <input class="form-check-input atualiza-dia-gravacao" type="checkbox" data-dia="{{ $i }}" data-horario="{{ $horario->id }}" name="resetar_situacao" value="true">
                                                    {{  $label_dias[$i] }} 
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>                        
                                @endfor
                            @endif
                            <div class="clear"></div>
                        </div>                    
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host = $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');  

            $(".atualiza-dia-gravacao").click(function(){

                var horario = $(this).data("horario");
                var dia = $(this).data("dia");

                var box = ".box-horario-"+horario;

                $.ajax({
                        url: host+'/radio/emissora/horario/atualizar',
                        type: 'POST',
                        data: { "_token": token,
                                "horario": horario,
                                "dia": dia
                        },
                        beforeSend: function() {
                            $(box).loader('show');
                        },
                        success: function(response) {
                                   
                        },
                        complete: function(){
                            $(box).loader('hide');
                        }
                });

            });

        });
    </script>
@endsection