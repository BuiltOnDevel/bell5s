<?php
  include("./php/conexao.php");
  session_start();
 $senha = $_POST['senhaUsuario'];
 $usuario = $_POST['nomeUsuario'];
  try{
  $sql = "SELECT login, id_cliente 
          FROM bel_usuario
          WHERE login = '$usuario'
          AND senha = '$senha'";
          

        $result = $conn->query( $sql );
        $row = $result->fetch();

        /*INICIO DA SESSÃO */
        $id_cliente = $row['id_cliente'];

        if($row){
           $response = array("success" => true);
           echo json_encode($response);

           $_SESSION['autenticado'] = true;
           $_SESSION['usuario'] = $usuario;
           $_SESSION['id_cliente_g'] = $id_cliente;
           var_dump($_SESSION);
	         header('Location: index.php');
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
