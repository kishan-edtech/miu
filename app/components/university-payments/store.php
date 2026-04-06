<?php
  session_start();
  if(isset($_POST['Fee_Head']) && isset($_SESSION['university_id'])){
    
    require '../../../includes/db-config.php';

    $Fee_Head = $_POST['Fee_Head'];
    $university_id = intval($_SESSION['university_id']);

    $add = $conn->query("INSERT INTO University_Fee_Head (`Fee_Head`, `University_ID`) VALUES ('$Fee_Head', $university_id)");
    if($add){
      echo json_encode(['status'=>true, 'message'=>'Fee Head added successlly!']);
    }else{
      echo json_encode(['status' =>false, 'message' => 'Something went wrong!']);
    }
  }
