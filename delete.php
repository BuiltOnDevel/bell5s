<?php
  include("./php/conexao.php");

  /*$nome = "1004683AA";
  $cor_ativa = "bg-dark";
  $cor_inativa = "bg-dark";
  $id = 32;
  */

 $id = $_POST['id'];

  try{
  $sql = "delete from bel_terminal
          where id_terminal = $id";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
