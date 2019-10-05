<!DOCTYPE html>
<html lang="en">

<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");
include("./php/conexao.php");
include("./php/classes.php");




	try {

		$sql = "select t.nome as teminal_nome, t.terminal_nr
, ( select d.nome from bel_unidade d where d.id_unidade = m.unidade_id ) as unidade
, ( select d.nome from bel_estacao d where d.id_estacao = m.estacao_id ) as estacao
from 
  bel_monitoramento m
  , bel_terminal t
where t.id_terminal = " . $_REQUEST['idTerminal'] . "
  and m.terminal_id = t.id_terminal";

		    $result = $conn->query( $sql );
        $row   = $result->fetchAll();
        $item = "<tr><th>Unidade</th><th>Estacao</th></tr>";
        foreach($row as $r){
          $item .= "<tr>
                        <td>".$r['unidade']."</td>
                        <td>".$r['estacao']."</td>
                      </tr>";
        }
        /*$nome_cor = $row['cor'];
        $id_cor = $row['id_cores_terminal'];
        $codigo = $row['codigo'];
        $fl_ativo = $row['fl_ativo'];*/

	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
  }
  


?>


                      <?=$item;?>
                      