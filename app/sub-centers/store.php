<?php
  if(isset($_POST['name']) && isset($_POST['reporting'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $reporting = intval($_POST['reporting']);
     $vertical = mysqli_real_escape_string($conn, $_POST['vertical']??"");
     $role = $_SESSION['Role'];
    if($role!='Administrator')
    {
        $vertical = $_SESSION['vertical'];
    }
    if(empty($name) || empty($reporting)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $center_code = $conn->query("SELECT Code FROM Users WHERE ID = $reporting");
    $center_code = mysqli_fetch_array($center_code);
    $center_code = $center_code['Code'];

    $all_reporting_user = $conn->query("SELECT Users.Code FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Sub_Center = Users.ID WHERE Center = $reporting ORDER BY Center_SubCenter.Sub_Center DESC LIMIT 1");
    if($all_reporting_user->num_rows > 0){
        $code = $all_reporting_user->fetch_assoc();
        // print_r($code);die;
    //   $code = mysqli_fetch_array($all_reporting_user);
      
      $code = $code['Code'] ?? 0;
      $code = str_replace($center_code.'.', '', $code);
      $new_code = $code+1;
      $code = $center_code.'.'.$new_code;
     
    }else{
      $code = $center_code.'.1';
    }
     
    $check = $conn->query("SELECT ID FROM Users WHERE Code like '$code'");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>'Code already exists!', 'code'=>$code]);
      exit();
    }
     

    $password = substr(uniqid('sau'), 0, 8);
    $add = $conn->query("INSERT INTO `Users`(`Name`, `Code`, `Password`, `Role`, `Designation`, `Photo`, `Created_By`,`vertical`) VALUES ('$name', '$code', AES_ENCRYPT('$password','60ZpqkOnqn0UQQ2MYTlJ'), 'Sub-Center', 'Sub-Center', '/assets/img/default-user.png', ".$_SESSION['ID'].",$vertical)");
    $add = $conn->query("INSERT INTO `Center_SubCenter`(`Center`, `Sub_Center`) VALUES ($reporting, $conn->insert_id)");
    
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Sub-Center added successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
