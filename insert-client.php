<?php
  include("./php/conexao.php");

 $cliente_nome = $_POST['clienteNome'];

  try{
  $sql = "insert into bel_cliente
         (id_cliente, nome) 
         values(
         nextval('bel_cliente_seq'), ' $cliente_nome')";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
