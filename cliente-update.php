<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");

  include("./php/conexao.php");


  $id_cliente = $_POST['fldid_cliente'];
  $nome = $_POST['fldnome'];
//  $ts_inclusao = $_POST['fldts_inclusao'];
//  $url_logo = $_POST['fldurl_logo'];
  $nivel_critico = $_POST['fldnivel_critico'];
  $nivel_alerta = $_POST['fldnivel_alerta'];
//  $chamados_abertos_media = $_POST['fldchamados_abertos_media'];
  $tma_geral = $_POST['fldtma_geral'];
  $fl_ativo = $_POST['fldfl_ativo'];

  try{

  if( $id_cliente == 0 ){
  	$sql = "INSERT INTO bel_cliente(
            id_cliente
            , nome
            , fl_ativo
            , nivel_critico
            , nivel_alerta, tma_geral)
values(
            nextval('bel_cliente_seq')
            , '$nome'
            , '$fl_ativo'
            , '$nivel_critico'
            , '$nivel_alerta', '$tma_geral' )";
  }

  if( $id_cliente > 0 ){
  	$sql = "UPDATE bel_cliente set
        nome = '$nome', fl_ativo = '$fl_ativo'
       , nivel_critico = '$nivel_critico', nivel_alerta= '$nivel_alerta'
       , tma_geral= '$tma_geral'
 WHERE id_cliente = $id_cliente
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
