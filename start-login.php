<?php
  include("./php/conexao.php");
  session_start();
 $senha = $_POST['senhaUsuario'];
 $usuario = $_POST['nomeUsuario'];
  try{
  $sql = "SELECT login 
          FROM bel_usuario
          WHERE login = '$usuario'
          AND senha = '$senha'";
          

        $result = $conn->query( $sql );
        $row = $result->fetch();

        if($row){
           $response = array("success" => true);
           echo json_encode($response);

           $_SESSION['autenticado'] = true;
           $_SESSION['usuario'] = $usuario;
           var_dump($_SESSION);
	         header('Location: index.html');
	         exit();
        }
        else{
            session_destroy();
            $_SESSION['autenticado'] = false;
            var_dump($_SESSION);
            header('Location: login.php');
            exit();
        }
        
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
