<?php
  include("./php/conexao.php");
 $id = $_POST['usuarioId'];
 $nome = $_POST['usuarioNome'];
 $login = $_POST['usuarioLogin'];
 $email = $_POST['usuarioEmail'];
 $cliente_id = $_POST['selCliente'];
 $fl_ativo = $_POST['selAtivo'];

  try{
  $sql = "update bel_usuario
         set id_cliente = $cliente_id
         , nome = '$nome'
         , login = '$login'
         , email = '$email'
         , fl_ativo = '$fl_ativo'
         where id_usuario = $id";
       
        $result = $conn->query( $sql );
        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
