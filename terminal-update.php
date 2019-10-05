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
  $sql = "update bel_terminal set
						  id_cliente = '$id_cliente'
						, nome = '$nome'
						, fl_ativo = '$fl_ativo'
						, terminal_nr = '$terminal_nr'
						, botao_ativar = '$botao_ativar'
						, botao_desativar = '$botao_desativar'
--						, id_tipo = '$id_tipo'
          where id_terminal = $id_terminal";

  $retorno->log = $sql;
  
		$result = $conn->query( $sql );

     //   $retorno = array("success" => true);
       //  echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
// $retorno->log = '';
print( json_encode( $retorno ) );


?>
