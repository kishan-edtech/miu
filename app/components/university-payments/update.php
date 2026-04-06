<?php
  session_start();
  if(isset($_POST['Fee_Head']) && isset($_SESSION['university_id']) && isset($_POST['id'])){
    require '../../../includes/db-config.php';
    $Fee_Head = $_POST['Fee_Head'];
    $id = $_POST['id'];
    $university_id = intval($_SESSION['university_id']);

    $add = $conn->query("UPDATE `University_Fee_Head` SET `Fee_Head` = '$Fee_Head' WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>true, 'message'=>'Fee Head updated successlly!']);
    }else{
      echo json_encode(['status' =>false, 'message' => 'Something went wrong!']);
    }
  }
