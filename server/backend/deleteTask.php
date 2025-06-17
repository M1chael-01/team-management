<?php
require "./EncryptionDecription.php";
require "../database/tasks.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['task_id'])) {
    $task_id = (int)$_POST['task_id']; 
    $accID = $_SESSION["id"];
    $connection = tasks();

    $sql = "DELETE FROM task WHERE id = ? AND acountID = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("ii", $task_id, $accID);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $connection->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No task_id provided']);
}
?>
