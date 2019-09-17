<?php
  include("./php/conexao.php");

 $id = $_GET['id'];

  try{
  $sql = "delete from bel_cores_terminal
          where id_cores_terminal = $id";
          
		    $result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
         header("Location: register-color.php");
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
