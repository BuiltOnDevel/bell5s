<?php
  include("./php/conexao.php");

  /*$nome = "1004683AA";
  $cor_ativa = "bg-dark";
  $cor_inativa = "bg-dark";
  $id = 32;
  */
 // Parametro cliente
 $id_cliente = 2;
 $nome = $_POST['nome'];
 $cor_ativa = $_POST['selAtivoCor'];
 $cor_inativa = $_POST['selInativoCor'];
 $nr_terminal = $_POST['nrTerminal'];

  try{
  $sql = "INSERT INTO bel_terminal
          (id_terminal, id_cliente
          , nome, fl_ativo
          , ts_inclusao, terminal_nr
          , botao_ativar, botao_ativar_cor
          , botao_desativar, botao_desativar_cor
          ,  ult_ts, ult_botao)
          VALUES(nextval('bel_terminal_seq'), $id_cliente
          , '$nome', 'S'::character varying
          , now(), '$nr_terminal'
          , '8', '$cor_ativa'
          , '4', '$cor_inativa'
          , now(), 0 )";

		$result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}

?>
