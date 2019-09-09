<?php
  include("./php/conexao.php");

  /*$nome = "1004683AA";
  $cor_ativa = "bg-dark";
  $cor_inativa = "bg-dark";
  $id = 32;
  */

 $nome = $_POST['nome'];
 $cor_ativa = $_POST['selAtivoCor'];
 $cor_inativa = $_POST['selInativoCor'];
 $id = $_POST['id'];

  try{
  $sql = "update bel_terminal
          set nome = '$nome'
            , botao_ativar_cor = '$cor_ativa'
            , botao_desativar_cor = '$cor_inativa'
          where id_terminal = $id";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
