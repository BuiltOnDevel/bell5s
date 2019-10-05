<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");

  include("./php/conexao.php");


  $id_unidade = $_POST['fldid_unidade'];
  $id_cliente = $_POST['fldid_cliente'];
  $nome = $_POST['fldnome'];
  $fl_ativo = $_POST['fldfl_ativo'];

  try{

  if( $id_unidade == 0 ){
  	$sql = "INSERT INTO bel_unidade(
            id_unidade, id_cliente
            , nome, fl_ativo )
    VALUES (
           nextval('bel_unidade_seq'), $id_cliente
            , '$nome', '$fl_ativo' )";
  }

  if( $id_unidade > 0 ){
  	$sql = "UPDATE bel_unidade set 
        id_cliente=$id_cliente
        , nome='$nome'
        , fl_ativo='$fl_ativo'
 WHERE id_unidade = $id_unidade;

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
