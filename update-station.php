<?php
  include("./php/conexao.php");
 $id = $_POST['estacaoId'];
 $nome = $_POST['estacaoNome'];
 $cliente_id = $_POST['selCliente'];
 $fl_ativo = $_POST['selAtivo'];

  try{
  $sql = "update bel_estacao
         set id_cliente = $cliente_id
         , nome = '$nome'
         , fl_ativo = '$fl_ativo'
         where id_estacao = $id";
         
        $result = $conn->query( $sql );
        $response = array("success" => true);
        echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
