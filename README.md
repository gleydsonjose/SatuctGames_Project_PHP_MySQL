# SatuctGames_Project_PHP_MySQL
SatuctGames foi criado usando HTML5, CSS3, Jquery, AJAX, Bootstrap 4, PHP e MySQL.

Site funcionando: http://satuctgames-shop.epizy.com/

Instruções para o site funcionar corretamente:

	Antes de tudo faça a importação do banco de dados criado para o site, o 'bd_inteiro.sql' que está no bd_backup, ele já vem com 20 games adicionados.

	Faça as seguintes alterações nos arquivos abaixo:
		* Apenas altere o que está entre aspas.
		* As alterações é para uso do banco de dados e funcionamento do PHPMailer.

		Todos scripts da pasta 'games' tem a seguinte linha de código:							 			
			Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

		Scripts da pasta 'scripts':
			'buscar_ajax.php':
				Linha 4: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'buscar_cupom.php':
				Linha 9: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'buscar_game_ajax.php':
				Linha 4: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'excluir-avaliacao.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'sessao_login.php':
				Linha 12: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 16: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 20: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

		Scripts principais:
			'acao.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'aventura.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'cadastro.php':
				Linha 13: $cadastro = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 28: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 29: $mail->Password = 'Senha do email gmail';
				Linha 31: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 32: $mail->FromName = 'Seu nome ou da empresa';

				* O href está como localhost, faça alterações para funcionar no seu localhost ou web, apenas mude uma parte do link antes do '?', ex:
				Antes: http://localhost/SG_Project/confirmar-email.php?$dados_url
				Depois: http://satuctgames-shop.epizy.com/confirmar-email.php?$dados_url
				Linha 118: $mail->Body = "Olá $_SESSION[nome], confirme seu email para completar seu cadastro. <strong><a href='http://localhost/SG_Project/confirmar-email.php?$dados_url'>Confirmar</a></strong>";

			'compra-finalizada.php':
				Linha 30: $compra_finalizada = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 43: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 44: $mail->Password = 'Senha do email gmail';
				Linha 46: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 47: $mail->FromName = 'Seu nome ou da empresa';

			'confirmar-email.php':
				Linha 33: $cadastro = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 46: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 47: $mail->Password = 'Senha do email gmail';
				Linha 49: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 50: $mail->FromName = 'Seu nome ou da empresa';

			'corrida.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'detalhesdaconta.php':
				Linha 14: $detalhesdaconta = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'esqueci-a-senha.php':
				Linha 12: $esqueci_a_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 29: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 30: $mail->Password = 'Senha do email gmail';
				Linha 32: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 33: $mail->FromName = 'Seu nome ou da empresa';

				* O href está como localhost, faça alterações para funcionar no seu localhost ou web, apenas mude uma parte do link antes do '?', ex:
				Antes: http://localhost/SG_Project/nova-senha.php?$dados_url
				Depois: http://satuctgames-shop.epizy.com/nova-senha.php?$dados_url
				Linha 60: $mail->Body = "Olá $_SESSION[nome], confirme seu email para completar seu cadastro. <strong><a href='http://localhost/SG_Project/nova-senha.php?$dados_url'>Confirmar</a></strong>";
			
			'faleconosco.php':
				Linha 20: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 21: $mail->Password = 'Senha do email gmail';
				Linha 23: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 24: $mail->FromName = 'Seu nome ou da empresa';
				Linha 58: $mail->addAddress('Um email que vai receber as mensagens, pode ser qualquer um');
			
			'index.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
			
			'lancamentos.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				
			'login.php':
				Linha 12: $login = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'luta.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'nova-senha.php':
				Linha 27: $novasenha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 43: $mudar_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
				Linha 60: $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 61: $mail->Password = 'Senha do email gmail';
				Linha 63: $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
				Linha 64: $mail->FromName = 'Seu nome ou da empresa';
			
			'paineldecontrole-cupons-criar-cupom.php':
				Linha 11: $criar_cupom = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'paineldecontrole-cupons.php':
				Linha 12: $metodos_cupons = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'paineldecontrole-games-adicionar-novo-game.php':
				Linha 135: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'paineldecontrole-usuarios-adicionar-usuario.php':
				Linha 12: $adicionar_usuario = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
	
			'paineldecontrole-usuarios-alterar-dados.php':
				Linha 11: $alterar_dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
			
			'paineldecontrole-usuarios-mudar-senha.php':
				Linha 11: $mudar_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

			'promocoes.php':
				Linha 7: $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
