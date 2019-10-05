<?php
  include("./php/conexao.php");
  include("./php/classes.php");

 $senha = $_POST['senhaUsuario'];
 $loginName = $_POST['nomeUsuario'];
 $flUsuario = 'N';

 
 $usr = new Usuario();

 
  try{
  $sql = "SELECT login, id_cliente , fl_usu
          , nome, id_usuario
          FROM bel_usuario
          WHERE login = '$loginName'
          AND senha = '$senha'
          and fl_ativo = 'S'";
          

        $result = $conn->query( $sql );
        $row = $result->fetch();

        /*INICIO DA SESSÃO */
        $id_cliente = $row['id_cliente'];
        $flUsuario = $row['fl_usu'];
        
        $usr->idCliente = $id_cliente;
        $usr->flUsu = $flUsuario;
        $usr->nome = $row['nome'];

        if($row){
           $response = array("success" => true);
           echo json_encode($response);

           $_SESSION['autenticado'] = true;
           $_SESSION['usuario_nome_g'] = $row['nome'];
           $_SESSION['usuario_id_g'] = $row['id_usuario'];
           $_SESSION['usuario_fl_usu_g'] = $row['fl_usu'];
           $_SESSION['usuario']      = $loginName;
           $_SESSION['id_cliente_g'] = $id_cliente;

    //       var_dump($_SESSION);
           
           if( $flUsuario == 'N' )  header('Location: index.php');
	            
           if( $flUsuario == 'S' ) header('Location: http://bellcloud.businessystem.com.br');

        }
        else{
        	// USUARIO INVALIDO
        	//=================================================
            session_destroy();
            $_SESSION['autenticado'] = false;
            var_dump($_SESSION);
             header('Location: login.php');

        }
        
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
