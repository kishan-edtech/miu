<?php
  if(isset($_POST['name']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
     $vertical = mysqli_real_escape_string($conn, $_POST['vertical']??"");
     $role = $_SESSION['Role'];
    if($role!='Administrator')
    {
        $vertical = $_SESSION['vertical'];
    }
    if(empty($name) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $add = $conn->query("UPDATE `Users` SET `vertical`=$vertical,`Name` = '$name' WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Sub-Center updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
