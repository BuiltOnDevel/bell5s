<?php
  include("./php/conexao.php");

 $id = $_POST['idUsuario'];
 $senha = $POST['senhaUsuario'];

  try{
  $sql = "update bel_usuario
          set senha = '".$senha."'
          where id_usuario = ".$id."
          ";
		    $result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
         header("Location: register-user.php");
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
