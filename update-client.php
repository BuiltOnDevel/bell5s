<?php
  include("./php/conexao.php");
 $id = $_POST['clienteId'];
 $nome = $_POST['clienteNome'];
 $fl_ativo = $_POST['selAtivo'];

  try{
  $sql = "update bel_cliente
         set nome = '$nome'
         , fl_ativo = '$fl_ativo'
         where id_cliente = $id";
         
        $result = $conn->query( $sql );
        $response = array("success" => true);
        echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
