<?php
  include("./php/conexao.php");

 $senha = $_POST['senhaUsuario'];
 $usuario = $_POST['nomeUsuario'];
 
 


  try{
  $sql = "SELECT nome 
          FROM bel_usuario
          WHERE nome = '$usuario'
          AND senha = '$senha'";

        $result = $conn->query( $sql );
        $row = $result->fetch();
        if($row == 1){
           $response = array("success" => true);
           echo json_encode($response);

           $_SESSION['usuario'] = $usuario;
	       header('Location: index.php');
	       exit();
        }
        else{
            $_SESSION['nao_autenticado'] = true;
	        header('Location: index.php');
	        exit();
        }
        }
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
