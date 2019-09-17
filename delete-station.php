<?php
  include("./php/conexao.php");

 $id = $_GET['id'];

  try{
  $sql = "delete from bel_estacao
          where id_estacao = $id";
          
		    $result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
         header("Location: register-station.php");
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
