<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");

 include("./php/conexao.php");

  $id_terminal = $_POST['fldIdTerminal'];
  $id_cliente = $_POST['selCliente'];
  $nome = $_POST['fldNome'];
  $fl_ativo = $_POST['fldAtivo'];

  $terminal_nr = $_POST['fldTerminalNr'];
  $botao_ativar = $_POST['fldAtivarBotao'];
  $botao_desativar = $_POST['fldDesativarBotao'];

  $id_tipo = $_POST['selTipo'];

  try{
  $sql = "INSERT INTO bel_terminal(
            id_terminal, id_cliente
            , nome, fl_ativo
            , terminal_nr
            , botao_ativar,  botao_desativar )
      values(
            nextval('bel_terminal_seq'), $id_cliente
            , '$nome', '$fl_ativo'
            , '$terminal_nr'
            , '$botao_ativar',  '$botao_desativar' ) ";


    $retorno->log .= $sql;
		$result = $conn->query( $sql );
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
// $retorno->log = '';
print( json_encode( $retorno ) );

?>
