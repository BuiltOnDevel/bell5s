<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");

  include("./php/conexao.php");


  $id_estacao = $_POST['fldid_estacao'];
  $id_cliente = $_POST['fldid_cliente'];
  $nome = $_POST['fldnome'];
  $fl_ativo = $_POST['fldfl_ativo'];
  $fl_add_term = $_POST['fldfl_add_term'];
  $id_unidade = $_POST['fldid_unidade'];

  try{

  if( $id_estacao == 0 ){
  	$sql = "INSERT INTO bel_estacao(
            id_estacao, id_cliente
            , nome, fl_ativo
            , fl_add_term
            , id_unidade )
        values(
            nextval('bel_estacao_seq'), $id_cliente
            , '$nome', '$fl_ativo'
            , '$fl_add_term'
            , $id_unidade )            
            ";
  }

  if( $id_estacao > 0 ){
  	$sql = "UPDATE bel_estacao set
     id_cliente=$id_cliente, nome=$nome
     , fl_ativo='$fl_ativo' 
     ,  fl_add_term= '$fl_add_term', id_unidade=$id_unidade
 WHERE id_estacao = $id_estacao

";
  }


  $retorno->log = $sql;
  
		$result = $conn->query( $sql );

     //   $retorno = array("success" => true);
       //  echo json_encode($response);
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
// $retorno->log = '';
print( json_encode( $retorno ) );


?>
