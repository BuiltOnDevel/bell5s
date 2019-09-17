<?php
  include("./php/conexao.php");

 $id = $_GET['id'];

  try{
  $sql = "delete from bel_usuario
          where id_usuario = $id";
          
		    $result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
         header("Location: register-user.php");
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
