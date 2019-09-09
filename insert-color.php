<?php
  include("./php/conexao.php");

 // Parametro cliente
 $id_cliente = 2;

 $cor_nome = $_POST['corNome'];
 $codigo_cor = $_POST['corCodigo'];
 

  try{
  $sql = "insert into bel_cores_terminal
         (id_cores_terminal, cor
         , codigo) 
         values(
         nextval('bel_cores_terminal_seq'), '$cor_nome'
         , '$codigo_cor')";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
