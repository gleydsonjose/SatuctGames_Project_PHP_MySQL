$(function(){
    // Deixando o menu fixo a partir de um certo ponto do scroll da página.
    $(window).scroll(function(){
        if($(this).scrollTop() >= $('.menu-loja').offset().top){
            $('.menu-loja').addClass('fixed-top');
            $('.menu-loja').addClass('px-3');
        }

        if($(this).scrollTop() < $('.logotipo').outerHeight()){
            $('.menu-loja').removeClass('fixed-top');
            $('.menu-loja').removeClass('px-3');
        }
    })

    // Para abrir a barra de busca do menu
    $('.btn-abrir-busca').click(function(){
        $(this).addClass('d-none');
        $('.form-busca').removeClass('d-none');
        $('.form-busca').addClass('d-flex');
    })

    // Para fechar a barra de busca ao clicar fora do campo de texto
    $(document).click(function(event){
        $target = $(event.target);
        if(!$target.closest('.form-busca').length && !$target.closest('.btn-abrir-busca').length){
            $('.form-busca').addClass('d-none');
            $('.form-busca').removeClass('d-flex');
            $('.btn-abrir-busca').removeClass('d-none');
        }
    })

    // Botão para fechar a barra de busca
    $('#fechar-busca').click(function(){
        $('.form-busca').addClass('d-none');
        $('.form-busca').removeClass('d-flex');
        $('.btn-abrir-busca').removeClass('d-none');
    })

    // Para pegar dados escrito na barra de busca e enviar para um script, nesse script será feito a busca do nome do game no banco de dados para depois retornar numa div.
    $("#buscar").keyup(function(){
        var buscar = $("#buscar").val();
        if(buscar.length > 0){

            $.ajax({
                url: "scripts/buscar_ajax.php",
                type: "POST",
                data: {buscar:buscar},
                success: function(resposta){
                    $('#busca-items').css('display','block');
                    
                    $('#busca-items').html(resposta);
                }
            })
        }else{
            $('#busca-items').css('display','none');
        }
    })

    // Para mostrar ou remover o campo de preço antigo
    $('#produto-promocional-sim').click(function(){
        if($('#produto-promocional-sim').prop('value') == 1 && $('#produto-promocional-sim').prop('checked', true)){
            $('.preco-antigo').removeClass('d-none');
        }  
    })

    $('#produto-promocional-nao').click(function(){
        if($('#produto-promocional-nao').prop('value') == 0 && $('#produto-promocional-nao').prop('checked', true)){
            $('.preco-antigo').addClass('d-none');
        }
    })

    // Se o usuário tirar o foco do campo de cep, será verificado se o cep existe, se sim, o campo cidade, endereço e bairro serão preenchidos, o campo número as vezes pode ser que não exista para algum cep, mas se existir ele será preenchido, se o cep não existir, uma mensagem de erro será mostrada.
    // OBS: Se o numero não existir, o foco vai para o campo número, se não o foco vai parar o campo complemento.
    $('#cep').focusout(function(){
        $.ajax({
            url: 'https://viacep.com.br/ws/'+$(this).val()+'/json/unicode/',
            type: 'get',
            dataType: 'json',
            success: function(dados){
                $('#cidade').val(dados.localidade);
                $('#endereco').val(dados.logradouro);
                $('#bairro').val(dados.bairro);
                $('#numero').val(dados.complemento);

                if(!$('#numero').val()){
                    $('#numero').focus();    
                }else{
                    $('#complemento').focus();
                }

                if(!$('#aviso-erro-cep').hasClass('d-none')){
                    $('#aviso-erro-cep').addClass('d-none');
                }

                $("[name='metodo-entrega']").prop('disabled', false);
            },
            beforeSend: function(){
                $('#carregamento-dados').removeClass('d-none');
            },
            complete: function(){
                $('#carregamento-dados').addClass('d-none');
            },
            error: function(){
                $('#aviso-erro-cep').removeClass('d-none');

                $("[name='metodo-entrega']").prop('disabled', true);
            }
        })
    })

    // Quando metodo de entrega for selecionado ou trocado, o efeito abaixo será ativado:
    // Passando o cep recebido para uma variável, e o método de entrega escolhido será modificado para um formato de url e assim armazenado na variável.
    // O ajax vai enviar os dados recebidos para a url escolhida, se houver sucesso, os dados virão em um array em formato json, assim será decodificado para a forma padrão em um array, para depois distribuir os dados entre os elementos escolhidos.
    // dados[0] = preço do frete
    // dados[1] = prazo de entrega
    // dados[2] = total da compra
    $("[name='metodo-entrega']").change(function(){
        var cep = $("#cep").val();
        var metodo_entrega = $("[name='metodo-entrega']").serialize();

        $.ajax({
            url: "scripts/calculafrete.php",
            type: 'post',
            data: {
                cep:cep,
                metodo_entrega
            },
            success: function(resposta){
                var dados = jQuery.parseJSON(resposta);
                $("#frete-valor").text(dados[0]);
                $("#prazo-entrega").text(dados[1]);
                $("#total-compra").text(dados[2]);
            },
            beforeSend: function(){
                $('#carregamento-dados').removeClass('d-none');
            },
            complete: function(){
                $('#carregamento-dados').addClass('d-none');
            }
        })
    })

    // Quando o botão de atualizar o cupom for clicado, uma variável será criada, ela vai receber o valor digitado no campo de cupom, após tudo isso, esse cupom será enviado pelo ajax para a url escolhida.
    // Como retorno será entegue um array com dados em formato JSON para se decodificado e colocado em um novo array.
    // Dados recebidos:
    // dados[0] = aviso se o cupom existe ou não.
    // dados[1] = total do carrinho.
    // dados[2] = alteração na palavra 'Total'.
    // Se o cupom não existe, será mostrado uma mensagem de erro.
    $("#atualizar-cupom").click(function(){
        var cupom = $("#cupom").val();

        $.ajax({
            url: "scripts/buscar_cupom.php",
            type: 'post',
            data: {
                cupom:cupom
            },
            success: function(resposta){
                var dados = jQuery.parseJSON(resposta);
                
                if(dados[0] == "desconto"){
                    $("#total_carrinho").text(dados[1]);
                    $('#total-nome').html(dados[2]);
                    $('#aviso-erro-cupom').addClass('d-none');
                }else{
                    $("#total_carrinho").text(dados[1]);
                    $('#total-nome').html(dados[2]);
                    $('#aviso-erro-cupom').removeClass('d-none');
                }
            }
        })
    })
})