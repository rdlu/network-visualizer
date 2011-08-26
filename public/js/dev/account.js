/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    var check = $('#check').val();
    var type = $('#type').val();
    var formName = $('#formName').val();
    if(check){
        switch(type){
            case 'register': {
                formName = 'register';
                break;
            }
            case 'edit': {
                formName = 'edit';
                break;
            }
        }
        //pega a tag do form
        //valida o form

        $("#validate").validate({

            submitHandler: function(form) {
                $(form).submit(function(){
                    alert('todos os dados foram preenchidos corretamente');
                    return false

                });
            },
            rules: {
                nome: {
                    required: true,
                    minlength: 5
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    
                },
                password_confirm: {

                }
            },
            messages: {
                nome: 'Voc&ecirc; n&atilde;o preencheu seu nome',                
                email: {
                    required: 'Voc&ecirc; precisa preencher um e-mail',
                    email: 'Endere&ccedil;o de e-mail n&atilde;o v&aacute;lido'
                }
            }

        });

    }

});
