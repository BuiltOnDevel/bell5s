<?php
  include("./php/conexao.php");

 $unidade_nome = $_POST['unidadeNome'];
 $cliente_id = $_POST['selCliente'];

  try{
  $sql = "insert into bel_unidade
         (id_unidade, id_cliente, nome) 
         values(
         nextval('bel_unidade_seq'), $cliente_id,'$unidade_nome')";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
