<?
//
// cursor_config.php
// variaveis de configuração da aplicação.
//

/*##################################################################
                SQL - ACESSO AO BANCO DE DADOS

Para adicionar uma nova conexao, adicione uma linha ABAIXO da 
conexão default( melhor até copiar a default ).
*** ATENÇÃO ABAIXO DA CONEXAO DEFAULT ***

Passe a conexao pela sequencia para a classe Cursor, ou seja, 
a conexão 0( zero ) é a default, da 1( um ) em diante são as que vc 
configurar neste ponto.

##################################################################*/
$sqlca[] = "host=191.252.2.252 dbname=bell5s user=delta password=DeltaDb@10 "; // conexao default
//$sqlca[] = "host=xxxdnn0456.locaweb.com.br dbname=advance user=advance password=advance "; // conexao default
// $sqlca[] = "--> parametros aqui <-- "; // conexao adicional, acessada como 1( um )
// $sqlca[] = "--> parametros aqui <-- "; // conexao adicional, acessada como 2(dois )



?>