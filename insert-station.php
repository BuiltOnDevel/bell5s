<?php
  include("./php/conexao.php");

 $estacao_nome = $_POST['estacaoNome'];
 $cliente_id = $_POST['selCliente'];
 $addTerm = $_POST['estacaoAddTerminal'];  

  try{
  $sql = "insert into bel_estacao
               (id_estacao, id_cliente
             , nome, fl_add_term ) 
         values(
             nextval('bel_estacao_seq'), $cliente_id
             ,'$estacao_nome', '$addTerm')";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
