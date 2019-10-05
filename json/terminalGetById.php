<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


class Registro{
 var $id_terminal = '';
 var $id_cliente = '';
 var $nome = '';
 var $fl_ativo = '';
 var $ts_inclusao = '';
 var $terminal_nr = '';
 var $botao_ativar = '';
 var $botao_ativar_cor = '';
 var $botao_desativar = '';
 var $botao_desativar_cor = '';
 var $ult_botao = '';
 var $ult_ts = '';
 var $id_tipo = '';
}


$reg = new Registro();



	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select * 
		from bel_terminal
		where id_terminal = ". $_REQUEST['id'] ;

		
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
		 $reg->id_terminal = $r['id_terminal'];
		 $reg->id_cliente = $r['id_cliente'];
		 $reg->nome = $r['nome'];
		 $reg->fl_ativo = $r['fl_ativo'];
		 $reg->ts_inclusao = $r['ts_inclusao'];
		 $reg->terminal_nr = $r['terminal_nr'];
		 $reg->botao_ativar = $r['botao_ativar'];
		 $reg->botao_ativar_cor = $r['botao_ativar_cor'];
		 $reg->botao_desativar = $r['botao_desativar'];
		 $reg->botao_desativar_cor = $r['botao_desativar_cor'];
		 $reg->ult_botao = $r['ult_botao'];
		 $reg->ult_ts = $r['ult_ts'];
		 $reg->id_tipo = $r['id_tipo'];			
		 
			$retorno->itens[] = $reg;
		}	
	
	
	
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
	
/*========================================================================
                    RETORNO AO CLIENTE
========================================================================*/

$retorno->log = '';
print( json_encode( $retorno ) );



?>