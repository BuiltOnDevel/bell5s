<?
//
// cursor_config.php
// variaveis de configura��o da aplica��o.
//

/*##################################################################
                SQL - ACESSO AO BANCO DE DADOS

Para adicionar uma nova conexao, adicione uma linha ABAIXO da 
conex�o default( melhor at� copiar a default ).
*** ATEN��O ABAIXO DA CONEXAO DEFAULT ***

Passe a conexao pela sequencia para a classe Cursor, ou seja, 
a conex�o 0( zero ) � a default, da 1( um ) em diante s�o as que vc 
configurar neste ponto.

##################################################################*/
$sqlca[] = "host=191.252.2.252 dbname=bell5s user=delta password=DeltaDb@10 "; // conexao default
//$sqlca[] = "host=xxxdnn0456.locaweb.com.br dbname=advance user=advance password=advance "; // conexao default
// $sqlca[] = "--> parametros aqui <-- "; // conexao adicional, acessada como 1( um )
// $sqlca[] = "--> parametros aqui <-- "; // conexao adicional, acessada como 2(dois )



?>