<?php
    // Pegando a data e hora de são paulo
    date_default_timezone_set('America/Sao_Paulo');

    // Variável global key e iv para o openssl, quer dizer que vão funcionar em qualquer método dessa classe.
    global $key;
    global $iv;
    
    // key e iv para criptografar e descriptografar dados para o openssl.
    $key = hash('sha256', 'oZCfK1VLQ8owfusIJHNmLtLWSh0k');
    $iv = hash('fnv1a64', '07f20f07b4b4be5d');

    Class Dados{
        // Conexão do banco de dados.
        public function __construct($bdnome, $host, $usuario, $senha){
            try{
                $this->pdo = new PDO("mysql:dbname=".$bdnome.";charset=utf8;host=".$host, $usuario, $senha);
            }catch(PDOException $e){
                echo "Erro com PDO: ".$e->getMessage();
            }catch(Exception $e){
                echo "Erro: ".$e->getMessage();
            }
        }

        // Verificando se existe um usuário com o email recebido.
        public function VerificarEmail($email){
            $sql = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :e");
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->execute();
            if($sql->rowCount() > 0){
                return true;
            }else{
                return false;
            }
        }

        // Método para cadastrar o usuário.
        public function Cadastrar($nome, $sobrenome, $email, $senha){
            global $key;
            global $iv;

            $senha_criptografada = openssl_encrypt($senha, 'aes-256-ctr', $key, 0, $iv);

            $sql = $this->pdo->prepare("INSERT INTO usuarios (nome, sobrenome, email, senha, cep, cidade, endereco, numero, bairro, complemento, estado, telefone, token, novasenha_permissao, datadecadastro) VALUES (:n, :sn, :e, :s, '0', '', '', '0', '', '', '', '0', '', '1',:ddc)");
            $sql->bindValue(':n', $nome, PDO::PARAM_STR);
            $sql->bindValue(':sn', $sobrenome, PDO::PARAM_STR);
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->bindValue(':s', $senha_criptografada, PDO::PARAM_STR);
            $sql->bindValue(':ddc', date('Y-m-d H:i:s'));
            $sql->execute();
            return true;
        }

        // Verificando se existe algum usuário com o email recebido, logo após, a senha do usuário que está no banco de dados será descriptografada para ser comparada com a senha passada na tela de login, se for igual, uma sessão será criada, se não, será retornado false e assim mostrando uma mensagem de erro, se por acaso o email não existir, retornará false também.
        public function Login($email, $senha_recebida){
            global $key;
            global $iv;
            
            $sql = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :e');
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->execute();
            $dados = $sql->fetch();
            $senha_descriptografada = openssl_decrypt($dados['senha'], 'aes-256-ctr', $key, 0, $iv);

            if($senha_recebida == $senha_descriptografada){
                // Escolhendo a TAG por numeração do id.
				if($dados['id'] == 1){
                    $_SESSION['id_administrador'] = 1;
				}elseif($dados['id'] == 2){
					$_SESSION['id_moderador'] = 2;
				}elseif($dados['id'] == 3){
					$_SESSION['id_moderador'] = 3;
				}else{
					$_SESSION['id_usuario'] = $dados['id'];
				}
                return true;
            }else{
                return false;
            }
        }

        // Buscando dados do usuário pelo id recebido.
		public function BuscarDadosUsuarios($id){
			$sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
			$sql->bindValue(":id", $id, PDO::PARAM_INT);
			$sql->execute();
			$dados = $sql->fetch();
			return $dados;
        }

        // Mudar a senha a partir do email recebido.
        public function MudarSenhaPorEmail($email, $senha){
            global $key;
            global $iv;

            $senha_criptografada = openssl_encrypt($senha, 'aes-256-ctr', $key, 0, $iv);

            $sql = $this->pdo->prepare("UPDATE usuarios SET senha = :s WHERE email = :e");
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->bindValue(':s', $senha_criptografada, PDO::PARAM_STR);
            $sql->execute();
            return true;
        }

        // Mudar a senha a partir do id.
        public function MudarSenhaPorId($id, $senha){
            global $key;
            global $iv;

            $senha_criptografada = openssl_encrypt($senha, 'aes-256-ctr', $key, 0, $iv);

            $sql = $this->pdo->prepare("UPDATE usuarios SET senha = :s WHERE id = :id");
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->bindValue(':s', $senha_criptografada, PDO::PARAM_STR);
            $sql->execute();
            return true;
        }

        // Quando o usuário marcar a caixa de lembrar senha, um token será criado e logo após isso, ele será guardado no banco de dados para o usuário que marcou a caixa.
        // Este token será guardado nos cookies do navegador, e com ele será possível resgatar os dados do usuário e colocar nos campos da tela de login.
        public function GuardarToken($email, $token){
            $sql = $this->pdo->prepare('UPDATE usuarios SET token = :th WHERE email = :e');
            $token_hash = hash('sha256', $token);
            $sql->bindValue(':th', $token_hash);
            $sql->bindValue(':e', $email);
            $sql->execute();
        }

        // Pegando o email do usuário a partir do token recebido.
        public function LembrarEmail($token){
            $sql = $this->pdo->prepare('SELECT email FROM usuarios WHERE token = :th');
            $token_hash = hash('sha256', $token);
            $sql->bindValue(':th', $token_hash);
            $sql->execute();
            $email = $sql->fetch();
            return $email['email'];
        }

        // Pegando a senha do usuário a partir do token recebido, e descriptografando a senha.
        public function LembrarSenha($token){
            global $key;
            global $iv;

            $sql = $this->pdo->prepare('SELECT senha FROM usuarios WHERE token = :th');
            $token_hash = hash('sha256', $token);
            $sql->bindValue(':th', $token_hash);
            $sql->execute();
            $senha = $sql->fetch();
            $senha_descriptografada = openssl_decrypt($senha['senha'], 'aes-256-ctr', $key, 0, $iv);
            return $senha_descriptografada;
        }

        // Pegando todos os dados de um game pelo nome da url da página.
        public function BuscarDadosGame($nome_url_pagina){
			$sql = $this->pdo->prepare("SELECT * FROM games WHERE nome_url_pagina = :nup");
			$sql->bindValue(":nup", $nome_url_pagina, PDO::PARAM_STR);
			$sql->execute();
			$dados = $sql->fetch();
			return $dados;
        }

        // Buscando todas avaliações e o nome do usuario que fez a avaliação.
        public function BuscarAvaliacoes(){
            $sql = $this->pdo->prepare('SELECT *,
                                      (SELECT nome FROM usuarios WHERE id = pk_id_usuario) AS nome_usuario
                                       FROM avaliacoes ORDER BY id DESC');
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Inserindo uma avaliação de acordo com o id do game e do usuário.
        public function InserirAvaliacao($id_usuario, $id_game, $avaliacao, $classificacao){
            $sql = $this->pdo->prepare("INSERT INTO avaliacoes (avaliacao, classificacao, horario, pk_id_usuario, pk_id_game) VALUES (:a, :c, :h, :piu, :pig)");
            $sql->bindValue(':a', $avaliacao, PDO::PARAM_STR);
            $sql->bindValue(':c', $classificacao, PDO::PARAM_INT);
            $sql->bindValue(':h', date('Y-m-d H:i:s'));
            $sql->bindValue(':piu', $id_usuario);
            $sql->bindValue(':pig', $id_game);
            $sql->execute();
            return true;
        }

        // Excluindo a avaliação através do id da avaliação recebida.
        public function ExcluirAvaliacao($id_avaliacao){
            $sql = $this->pdo->prepare('DELETE FROM avaliacoes WHERE id = :id');
            $sql->bindValue(':id', $id_avaliacao, PDO::PARAM_INT);
            $sql->execute();
            return true;
        }

        // Criando um cupom de desconto.
        public function CriarCupom($cupom, $desconto){
            $sql = $this->pdo->prepare("INSERT INTO cupons (cupom, desconto, datadecriacao) VALUES (:c, :d, :ddc)");
            $sql->bindValue(':c', $cupom, PDO::PARAM_STR);
            $sql->bindValue(':d', $desconto, PDO::PARAM_INT);
            $sql->bindValue(':ddc', date('Y-m-d H:i:s'));
            $sql->execute();
            return true;
        }

        // Buscando todos cupons no banco de dados.
        public function BuscarCupons(){
            $sql = $this->pdo->prepare('SELECT * FROM cupons');
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Remover cupom apartir de seu id.
        public function RemoverCupom($id){
            $sql = $this->pdo->prepare('DELETE FROM cupons WHERE id = :id');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->execute();
        }

        // Buscar o desconto que o cupom oferece a partir do nome do cupom.
        public function BuscarDescontoCupom($cupom){
            $sql = $this->pdo->prepare('SELECT desconto FROM cupons WHERE cupom = :c');
            $sql->bindValue(':c', $cupom, PDO::PARAM_STR);
            $sql->execute();
            $dados = $sql->fetch();
            return $dados;
        }

        // Buscando todos usuarios no banco de dados.
        public function BuscarUsuarios(){
            $sql = $this->pdo->prepare('SELECT * FROM usuarios');
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Remover o usuario a partir de seu id.
        public function RemoverUsuario($id){
            $sql = $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->execute();
        }

        // Alterando a permissão para alterar senha.
        // 0 = Não pode mudar a senha.
        // 1 = Pode mudar a senha.
        public function AlterandoPermissaoNovaSenha($email, $novasenha_permissao){
            $sql = $this->pdo->prepare('UPDATE usuarios SET novasenha_permissao = :nsp WHERE email = :e');
            $sql->bindValue(':nsp', $novasenha_permissao, PDO::PARAM_INT);
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->execute();
        }

        // Buscando o valor da permissão de alterar a senha a partir do email do usuário.
        public function VerPermissaoNovaSenha($email){
            $sql = $this->pdo->prepare('SELECT novasenha_permissao FROM usuarios WHERE email = :e');
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->execute();
            $ns_permissao = $sql->fetch();
            return $ns_permissao;
        }

        // Esse método simplesmente vai pegar os dados recebidos, vai verificar se o usuário que forneceu os dados existe no banco de dados, se sim, os dados serão alterados. Se não, será verificado se o email fornecido já existe no banco de dados, se existe, será retornado false, se não, os dados serão alterados com o novo email.
        public function AlterarDados($id, $nome, $sobrenome, $email, $cep, $cidade, $endereco, $numero, $bairro, $complemento, $estado, $telefone){
            $sql = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id AND email = :e');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->bindValue(':e', $email, PDO::PARAM_STR);
            $sql->execute();
            if($sql->rowCount() == 1){
                $sql = $this->pdo->prepare('UPDATE usuarios SET nome = :n, sobrenome = :sn, email = :e, cep = :c, cidade = :ci, endereco = :en, numero = :nu, bairro = :b, complemento = :co, estado = :es, telefone = :t WHERE id = :id');
                $sql->bindValue(':id', $id, PDO::PARAM_INT);
                $sql->bindValue(':n', $nome, PDO::PARAM_STR);
                $sql->bindValue(':sn', $sobrenome, PDO::PARAM_STR);
                $sql->bindValue(':e', $email, PDO::PARAM_STR);
                $sql->bindValue(':c', $cep, PDO::PARAM_INT);
                $sql->bindValue(':ci', $cidade, PDO::PARAM_STR);
                $sql->bindValue(':en', $endereco, PDO::PARAM_STR);
                $sql->bindValue(':nu', $numero, PDO::PARAM_INT);
                $sql->bindValue(':b', $bairro, PDO::PARAM_STR);
                $sql->bindValue(':co', $complemento, PDO::PARAM_STR);
                $sql->bindValue(':es', $estado, PDO::PARAM_STR);
                $sql->bindValue(':t', $telefone, PDO::PARAM_INT);
                $sql->execute();
                return true;
            }else{
                $sql = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :e');
                $sql->bindValue(':e', $email, PDO::PARAM_STR);
                $sql->execute();
                if($sql->rowCount() != 0){
                    return false;
                }else{
                    $sql = $this->pdo->prepare('UPDATE usuarios SET nome = :n, sobrenome = :sn, email = :e, cep = :c, cidade = :ci, endereco = :en, numero = :nu, bairro = :b, complemento = :co, estado = :es, telefone = :t WHERE id = :id');
                    $sql->bindValue(':id', $id, PDO::PARAM_INT);
                    $sql->bindValue(':n', $nome, PDO::PARAM_STR);
                    $sql->bindValue(':sn', $sobrenome, PDO::PARAM_STR);
                    $sql->bindValue(':e', $email, PDO::PARAM_STR);
                    $sql->bindValue(':c', $cep, PDO::PARAM_INT);
                    $sql->bindValue(':ci', $cidade, PDO::PARAM_STR);
                    $sql->bindValue(':en', $endereco, PDO::PARAM_STR);
                    $sql->bindValue(':nu', $numero, PDO::PARAM_INT);
                    $sql->bindValue(':b', $bairro, PDO::PARAM_STR);
                    $sql->bindValue(':co', $complemento, PDO::PARAM_STR);
                    $sql->bindValue(':es', $estado, PDO::PARAM_STR);
                    $sql->bindValue(':t', $telefone, PDO::PARAM_INT);
                    $sql->execute();
                    return true;
                }
            }
        }

        // Pegando os dados recebidos da página de finalização da compra, para usar eles como atualização dos dados do usuário no banco de dados.
        public function FinalizarCompraDadosUsuario($id, $cep, $cidade, $endereco, $numero, $bairro, $complemento, $estado, $telefone){
            $sql = $this->pdo->prepare('UPDATE usuarios SET cep = :c, cidade = :ci, endereco = :en, numero = :nu, bairro = :b, complemento = :co, estado = :es, telefone = :t WHERE id = :id');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->bindValue(':c', $cep, PDO::PARAM_INT);
            $sql->bindValue(':ci', $cidade, PDO::PARAM_STR);
            $sql->bindValue(':en', $endereco, PDO::PARAM_STR);
            $sql->bindValue(':nu', $numero, PDO::PARAM_INT);
            $sql->bindValue(':b', $bairro, PDO::PARAM_STR);
            $sql->bindValue(':co', $complemento, PDO::PARAM_STR);
            $sql->bindValue(':es', $estado, PDO::PARAM_STR);
            $sql->bindValue(':t', $telefone, PDO::PARAM_INT);
            $sql->execute();
        }

        // Criando uma transação de compra para o usuário.
        public function InserirTransacao($id_usuario, $nome_usuario, $quantidade, $nome_game, $versao_game, $total_a_pagar, $metodo_pagamento, $metodo_entrega, $frete, $prazo_entrega, $id_game, $id_transacao_carrinho){
            $sql = $this->pdo->prepare("INSERT INTO transacoes (nome_usuario, jogos_comprados, total, metodo_pagamento, metodo_entrega, frete, prazo_entrega, pagamento, datadecompra, pk_id_usuario, pk_id_game, id_transacao_carrinho) VALUES (:nu, :jc, :t, :mp, :me, :f, :pe, '0', :ddc, :piu, :pig, :itc)");
            $sql->bindValue(':nu', $nome_usuario, PDO::PARAM_STR);
            $jogos_comprados = $quantidade."x ".$nome_game." versão ".$versao_game;
            $sql->bindValue(':jc', $jogos_comprados, PDO::PARAM_STR);
            $sql->bindValue(':t', str_replace(',', '.', $total_a_pagar), PDO::PARAM_STR);
            $sql->bindValue(':mp', $metodo_pagamento, PDO::PARAM_STR);
            $sql->bindValue(':me', $metodo_entrega, PDO::PARAM_STR);
            $sql->bindValue(':f', str_replace(',', '.', $frete), PDO::PARAM_STR);
            $sql->bindValue(':pe', $prazo_entrega, PDO::PARAM_STR);
            $sql->bindValue(':ddc', date('Y-m-d H:i:s'));
            $sql->bindValue(':piu', $id_usuario, PDO::PARAM_INT);
            $sql->bindValue(':pig', $id_game, PDO::PARAM_INT);
            $sql->bindValue(':itc', $id_transacao_carrinho, PDO::PARAM_INT);
            $sql->execute();
        }

        // Buscando transações que o usuário fez usando o carrinho.
        public function QuantidadeTransacoesCarrinhoUsuario($id_usuario){
            $sql = $this->pdo->prepare('SELECT id_transacao_carrinho FROM transacoes WHERE pk_id_usuario = :piu');
            $sql->bindValue(':piu', $id_usuario, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Buscando transações do usuário pelo seu id
        public function BuscarTransacoesUsuario($id_usuario){
            $sql = $this->pdo->prepare('SELECT * FROM transacoes WHERE pk_id_usuario = :piu');
            $sql->bindValue(':piu', $id_usuario, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Alterando status de pagamento de uma transação de usuário a partir do id desta transação.
        public function AlterandoStatusPagamento($id_transacao, $status_pagamento){
            $sql = $this->pdo->prepare("UPDATE transacoes SET pagamento = :sp WHERE id = :id");
            $sql->bindValue(':id', $id_transacao, PDO::PARAM_INT);
            $sql->bindValue(':sp', $status_pagamento, PDO::PARAM_INT);
            $sql->execute();
        }

        // Adicionando o game no banco de dados.
        public function AdicionarGame($nome_game, $imagem, $nome_url_pagina, $titulo_pagina, $categoria, $informacoes, $disponibilidade, $promocao, $lancamento, $quantidade, $preco_atual, $preco_antigo){
            $sql = $this->pdo->prepare("INSERT INTO games (nome_game, imagem, nome_url_pagina, titulo_pagina, categoria, informacoes, disponibilidade, promocao, lancamento, quantidade, preco_atual, preco_antigo) VALUES (:ng, :i, :nup, :tp, :c, :info, :d, :p, :l, :q, :pat, :pan)");
            $sql->bindValue(':ng', $nome_game, PDO::PARAM_STR);
            $sql->bindValue(':i', $imagem, PDO::PARAM_STR);
            $sql->bindValue(':nup', $nome_url_pagina, PDO::PARAM_STR);
            $sql->bindValue(':tp', $titulo_pagina, PDO::PARAM_STR);
            $sql->bindValue(':c', $categoria, PDO::PARAM_STR);
            $sql->bindValue(':info', $informacoes, PDO::PARAM_STR);
            $sql->bindValue(':d', $disponibilidade, PDO::PARAM_INT);
            $sql->bindValue(':p', $promocao, PDO::PARAM_INT);
            $sql->bindValue(':l', $lancamento, PDO::PARAM_INT);
            $sql->bindValue(':q', $quantidade, PDO::PARAM_INT);
            $sql->bindValue(':pat', $preco_atual, PDO::PARAM_STR);
            $sql->bindValue(':pan', $preco_antigo, PDO::PARAM_STR);
            $sql->execute();
        }

        // Alterando os dados do game de sua página e banco de dados a partir de seu id.
        public function AlterarDadosGame($id, $nome_game, $titulo_pagina, $categoria, $informacoes, $disponibilidade, $promocao, $lancamento, $quantidade, $preco_atual, $preco_antigo){
            $sql = $this->pdo->prepare("UPDATE games SET nome_game = :ng, titulo_pagina = :tp, categoria = :c, informacoes = :info, disponibilidade = :d, promocao = :p, lancamento = :l, quantidade = :q, preco_atual = :pat, preco_antigo = :pan WHERE id = :id");
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->bindValue(':ng', $nome_game, PDO::PARAM_STR);
            $sql->bindValue(':tp', $titulo_pagina, PDO::PARAM_STR);
            $sql->bindValue(':c', $categoria, PDO::PARAM_STR);
            $sql->bindValue(':info', $informacoes, PDO::PARAM_STR);
            $sql->bindValue(':d', $disponibilidade, PDO::PARAM_INT);
            $sql->bindValue(':p', $promocao, PDO::PARAM_INT);
            $sql->bindValue(':l', $lancamento, PDO::PARAM_INT);
            $sql->bindValue(':q', $quantidade, PDO::PARAM_INT);
            $sql->bindValue(':pat', $preco_atual, PDO::PARAM_STR);
            $sql->bindValue(':pan', $preco_antigo, PDO::PARAM_STR);
            $sql->execute();
        }

        // Verificando se o game já existe no banco de dados, se sim, vai retornar true, se não, retorna false.
        public function VerificarExistenciaGame($nome_url_pagina){
            $sql = $this->pdo->prepare("SELECT * FROM games WHERE nome_url_pagina = :nup");
            $sql->bindValue(':nup', $nome_url_pagina, PDO::PARAM_STR);
            $sql->execute();
            if($sql->rowCount() > 0){
                return true;
            }else{
                return false;
            }
        }

        // Buscando todos games existentes no banco de dados.
        public function BuscarGames(){
            $sql = $this->pdo->prepare('SELECT * FROM games');
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Remover o game a partir de seu id.
        public function RemoverGame($id){
            $sql = $this->pdo->prepare('DELETE FROM games WHERE id = :id');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->execute();
        }

        // Buscando dados do game pelo id recebido.
		public function BuscarDadosGames($id){
			$sql = $this->pdo->prepare("SELECT * FROM games WHERE id = :id");
			$sql->bindValue(":id", $id, PDO::PARAM_INT);
			$sql->execute();
			$dados = $sql->fetch();
			return $dados;
        }

        // Mudando imagem do game pelo id.
        public function MudarImagemGame($id, $imagem_game){
            $sql = $this->pdo->prepare('UPDATE games SET imagem = :ig WHERE id = :id');
            $sql->bindValue(':id', $id, PDO::PARAM_INT);
            $sql->bindValue(':ig', $imagem_game, PDO::PARAM_STR);
            $sql->execute();
        }

        // Alterando a disponibilidade e quantidade do game pelo seu id.
        public function AlterarDisponibilidadeQuantidadeGame($id_game, $disponibilidade, $quantidade){
            $sql = $this->pdo->prepare("UPDATE games SET disponibilidade = :d, quantidade = :q WHERE id = :id");
            $sql->bindValue(':id', $id_game, PDO::PARAM_INT);
            $sql->bindValue(':d', $disponibilidade, PDO::PARAM_INT);
            $sql->bindValue(':q', $quantidade, PDO::PARAM_INT);
            $sql->execute();
        }

        // Para buscar o nome do game pelos dados recebido pela barra de busca.
        public function BarraBuscaGame($buscar){
            $sql = $this->pdo->prepare("SELECT * FROM games WHERE nome_game LIKE :b");
            $sql->bindValue(':b', "$buscar%");
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Buscando games por categoria e limite, ex: se o inicial for 0 e o por pagina for 8, vai ser do registro 0 até 7, se o inicial for alterado para 8, vai ser do registro 8 até 15...
        public function BuscarGameCategoria($game_inicial_pagina, $games_por_pagina, $categoria){
            $sql = $this->pdo->prepare("SELECT * FROM games WHERE categoria = :c LIMIT :gip, :gpp");
            $sql->bindValue(':gip', $game_inicial_pagina, PDO::PARAM_INT);
            $sql->bindValue(':gpp', $games_por_pagina, PDO::PARAM_INT);
            $sql->bindValue(':c', $categoria, PDO::PARAM_STR);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Buscando games que são lancamentos e por limite, ex: se o inicial for 0 e o por pagina for 8, vai ser do registro 0 até 7, se o inicial for alterado para 8, vai ser do registro 8 até 15...
        public function BuscarGameLancamento($game_inicial_pagina, $games_por_pagina){
            $sql = $this->pdo->prepare("SELECT * FROM games WHERE lancamento = '1' LIMIT :gip, :gpp");
            $sql->bindValue(':gip', $game_inicial_pagina, PDO::PARAM_INT);
            $sql->bindValue(':gpp', $games_por_pagina, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }

        // Buscando games que estão em promoção e por limite, ex: se o inicial for 0 e o por pagina for 8, vai ser do registro 0 até 7, se o inicial for alterado para 8, vai ser do registro 8 até 15...
        public function BuscarGamePromocao($game_inicial_pagina, $games_por_pagina){
            $sql = $this->pdo->prepare("SELECT * FROM games WHERE promocao = '1' LIMIT :gip, :gpp");
            $sql->bindValue(':gip', $game_inicial_pagina, PDO::PARAM_INT);
            $sql->bindValue(':gpp', $games_por_pagina, PDO::PARAM_INT);
            $sql->execute();
            $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        }
    }
?>