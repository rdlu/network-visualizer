/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/* Brazilian initialisation for the jQuery UI date picker plugin. */
/* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pt-BR'] = {
		closeText: 'Fechar',
		prevText: '&#x3c;Anterior',
		nextText: 'Pr&oacute;ximo&#x3e;',
		currentText: 'Hoje',
		monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
		'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Out','Nov','Dez'],
		dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
});



$(document).ready(function(){
    $(function() {
	$( "#tabs" ).tabs();
    });

    //coloca o datepicker
	$('.datepicker').datepicker();

	//transforma em botões os elementos que enviam os forms
	$('.submit').button();
	//valida os dados do formulário

        //$('tr:nth-child(odd)').invoke('addClassName', 'odd');

        $("tr:odd").addClass("odd");

        $('.dialog').dialog({
            autoOpen: false,
            modal: true,
            bgiframe: true,
            width: 450,
            height: 220,
            resizable: false
	});
        $('.rota').each(function(){
            $(this).click(function(event){
                var id = $(this).attr('id');
                console.log(id);
                console.log($("#rota_"+id));
                $("#rota_"+id).dialog('option', 'buttons', {
                    "Fechar" : function() {
                        $(this).dialog("close");
                    }
                });
                $("#rota_"+id).dialog("open");
            });
        });

        $('.button').button();

	$('.validate').validate({
		rules: {
			
			inicio: {
				required: true,
				dpDate: true,
				dpCompareDate: ['before', '#fim']
			},
			fim: {
				required: true,
				dpDate: true,
				dpCompareDate: ['after', '#inicio']
			}
		},
		messages: {
			nome: {
				required: "Este campo é obrigatório",
				maxlength: "Número máximo de caracteres excedido",
				regexp: "O nome deve começar com uma letra ou um número"
			},
			inicio: {
				required: "Este campo é obrigatório",
				dpDate: "A data deve estar no formato dd/mm/aaaa",
				dpCompareDate: "Escolha uma data anterior à data de término"
			},
			fim: {
				required: "Este campo é obrigatório",
				dpDate: "A data deverá estar no formato dd/mm/aaaa",
				dpCompareDate: "Escolha uma data posterior à data de início"
			}
		}
        });

});
