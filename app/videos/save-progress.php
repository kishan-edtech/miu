<?php
session_start();
  ini_set('display_errors', 1); 
  require '../../includes/db-config.php'; // Your DB config file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $studentId = $_SESSION['ID'];
    $videoId = trim($data['video_id']);
    $totalDuration = $data['total_duration'];
    $watchDuration = $data['pause_time'];
    $progress = $data['progress'];

    // Check if record exists already
    $checkSql = "SELECT * FROM student_progress WHERE students_id = $studentId AND videos_id = $videoId";
    $stmt = $conn->query($checkSql);
    

    if ($stmt->num_rows > 0) {
        // Update existing
        $existProgress = $stmt->fetch_assoc();
        if($existProgress['progress']<$progress)
        {
             $updateSql = "UPDATE student_progress SET total_duration=?, watch_duration=?, progress=?, created_at=NOW() WHERE students_id=? AND videos_id=?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ssssi", $totalDuration, $watchDuration, $progress, $studentId, $videoId);
            $updateStmt->execute();   
        }
    } else {
        // Insert new
        $insertSql = "INSERT INTO student_progress (students_id, videos_id, total_duration, watch_duration, progress) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iisss", $studentId, $videoId, $totalDuration, $watchDuration, $progress);
        $insertStmt->execute();
    }

    echo json_encode(["status" => "success", "progress" => $progress]);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request"]);
}
?>