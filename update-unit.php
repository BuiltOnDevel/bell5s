<?php
  include("./php/conexao.php");
 $id = $_POST['unidadeId'];
 $nome = $_POST['unidadeNome'];
 $cliente_id = $_POST['selCliente'];
 $fl_ativo = $_POST['selAtivo'];

  try{
  $sql = "update bel_unidade
         set id_cliente = $cliente_id
         , nome = '$nome'
         , fl_ativo = '$fl_ativo'
         where id_unidade = $id";
       
        $result = $conn->query( $sql );
        $response = array("success" => true);
        echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
