<?php
  if(isset($_POST['id'])){
    require '../../../includes/db-config.php';
    session_start();
    $id = $_POST['id'];
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    $select = $conn->query("SELECT internal_id FROM Sub_Courses WHERE ID = $id");
    $abc_id = $select->fetch_assoc();
    echo json_encode(['status'=>200]);
  }
?>