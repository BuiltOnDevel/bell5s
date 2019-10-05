<!DOCTYPE html>
<html lang="en">
<?php
    header("Access-Control-Allow-Origin: *");
    include("./json/default.php");
    include("./php/conexao.php");
    include("./validate-login.php");
?>
<?php 
   include("./ga/header.php");
?>
<body id="page-top">
<?php 
   include("./ga/menu.php");
?>
<body>   
   
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <!--<script src="vendor/jquery-easing/jquery.easing.min.js"></script>-->


  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  
  <!-- script src="js/demo/datatables-demo.js"></script -->

</html>
