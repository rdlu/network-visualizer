/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){

    $.validator.addMethod('regexp', function(value, element, param) {
        return this.optional(element) || value.match(param);
    }, 'A entrada possui caracteres inválidos');

    $.validator.addMethod("notEqualTo", function(value, element, param) {
        return this.optional(element) || value != param;
    }, "Valores de dois campos não podem ser iguais");
   
    $.validator.addMethod("tooStupid", function(value, element, param) {
        return this.optional(element) || param == 'admin' || param == 'guest' || param == 'root' || param == 'system';
    }, "Impossível cadastrar senha padrão");

    $("#validate").validate({

            //submitHandler: function(form) {
            //    $(form).submit(function(){
            //        alert('todos os dados foram preenchidos corretamente');
            //        return false;

            //    });
            //},
            rules: {
                username: {
                    required: true,
                    minlength: 4,
                    maxlength: 32,
                    regexp: /^[a-z](?=[\w.]{3,31}$)\w*\.?\w*$/i
                    //notEqualTo: 'admin'
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 127
                },
                password: {
                    required: false,
                    minlength: 5,
                    notEqualTo: $('#username').attr('value')                    
                },
                password_confirm: {
                    equalTo: "#password"
                }
            },
            messages: {
                username: {
                    required: 'Preencha o nome de usuário',
                    minlength: "O nome do usuário deve conter no mínimo 4 caracteres",
                    maxlength: "O nome do usuário deve conter no máximo 32 caracteres",
                    regexp: "Nome de usuário inválido",
                    notEqualTo: "Não é possível criar esse usuário"
                },
                email: {
                    required: 'Voc&ecirc; precisa preencher um e-mail',
                    email: 'Endere&ccedil;o de e-mail inv&aacute;lido',
                    maxlength: "O endereço de email deve ter no máximo 127 caracteres"
                },
                password: {
                    minlength: "A senha deve conter no mínimo 5 caracteres",
                    notEqualTo: "A senha não pode ser igual ao nome de usuário"
                },
                password_confirm: {
                    equalTo: "O campo confirmação de senha deve ser idêntico ao campo senha."
                }
            }
        });
});


