<?php
  include("./php/conexao.php");

 $id = $_GET['id'];

  try{
  $sql = "delete from bel_unidade
          where id_unidade = $id";
          
        $result = $conn->query( $sql );

        $response = array("success" => true);
         echo json_encode($response);
         header("Location: register-unit.php");
  }
  catch(PDOException $e) {
      $retorno->log .= "Error: " . $e->getMessage();
  }

?>
