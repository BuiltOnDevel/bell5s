<?php
  include("./php/conexao.php");

 $usuario_nome = $_POST['usuarioNome'];
 $login_nome = $_POST['usuarioLogin'];
 $email_nome = $_POST['usuarioEmail'];
 $senha_nome = $_POST['usuarioSenha'];
 $cliente_id = $_POST['selCliente'];


  try{
  $sql = "insert into bel_usuario
         (id_usuario, id_cliente, nome, login, email, senha) 
         values(
         nextval('bel_usuario_seq'), $cliente_id,'$usuario_nome','$login_nome','$email_nome','$senha_nome')";
         
        $result = $conn->query( $sql );
        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
