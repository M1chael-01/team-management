<?php
require "./EncryptionDecription.php";
require "../database/tasks.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['task_id'], $_POST['task_title'], $_POST['task_status'], $_POST['task_team'])) {
    $task_id = (int)$_POST['task_id'];
    $title = Secure::encryption($_POST['task_title']);
    $status = Secure::encryption($_POST['task_status']);
    $team_id = (int)$_POST['task_team'];
    $accID = $_SESSION["id"];
    $connection = tasks();

 

    $sql = "UPDATE task SET title = ?, status = ?, teamID = ? WHERE id = ? AND acountID = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("ssiii", $title, $status, $team_id, $task_id, $accID);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $connection->error]);
    }
}
?>
