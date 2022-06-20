$(document).ready(function() {

    $(".periodo").change(function(){
        var periodo = $(this).val();
        inicializaDatas(periodo);        
    });

    $(".load_expression").change(function(){
        var expression = $('#regra option').filter(':selected').data('expression');
        $(".display_regra").html(expression);
    });

    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            atualizaLabels();
            $("#periodo").val('custom');
        }
    });

    function inicializaDatas(periodo)
    {
        var dataFinal = new Date();
        var dataInicial = new Date();    
            
        if(periodo != 'custom'){
            dataInicial.setDate(dataFinal.getDate() - (periodo - 1));
        }else{
            $(".dt_inicial_relatorio").focus();
        }

        $(".dt_inicial_relatorio").val(formataData(dataInicial));
        $(".dt_final_relatorio").val(formataData(dataFinal));

        $(".label_data_inicial").html(formataData(dataInicial));
        $(".label_data_final").html(formataData(dataFinal));   
    }

    function formataData(data)
    {
        var dia = String(data.getDate()).padStart(2, '0');
        var mes = String(data.getMonth() + 1).padStart(2, '0');
        var ano = data.getFullYear();

        return dia + '/' + mes + '/' + ano;
    }

    function atualizaLabels(dataInicial, dataFinal)
    {
        var data_inicial = $(".dt_inicial_relatorio").val();
        var data_final = $(".dt_final_relatorio").val();

        $(".label_data_inicial").html(data_inicial);
        $(".label_data_final").html(data_final);   
    }

    function calculaPeriodo(dataInicial, dataFinal)
    {
        var dataInicial = $(".dt_inicial_relatorio").val();
        var dataFinal = $(".dt_final_relatorio").val();

        var a = dataInicial.split("/");
        var date1 = new Date(a[2], a[1] - 1, a[0]);

        var b = dataFinal.split("/");
        var date2 = new Date(b[2], b[1] - 1, b[0]);

        var diffTime = Math.abs(date2 - date1);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        diffDays = (diffDays < 1) ? 1 : diffDays;

        return diffDays;
    }
});