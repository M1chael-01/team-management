<?php
require "../database/tasks.php";
require "./EncryptionDecription.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class AddTask {
    public static function addTask($title, $status, $teamRaw) {
        $connection = tasks();

        $accID = $_SESSION["id"];
        list($teamID, $teamName) = explode('-', $teamRaw, 2);

        $encryptedTitle = Secure::encryption($title);
        $encryptedStatus = Secure::encryption($status);
        $encryptedTeamName = Secure::encryption($teamName);

        $sql = "INSERT INTO task (acountID, teamID, title, status, team) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param("iisss", $accID, $teamID, $encryptedTitle, $encryptedStatus, $encryptedTeamName);

            if ($stmt->execute()) {
                return "Task added successfully!";
            } else {
                return "Error adding task: " . $stmt->error;
            }

            $stmt->close();
        } else {
            return "Error preparing the query: " . $connection->error;
        }
    }
}

if (isset($_POST['task_title'], $_POST['task_status'], $_POST['task_team'])) {
    $task_title = htmlspecialchars(trim($_POST['task_title']));
    $task_status = htmlspecialchars(trim($_POST['task_status']));
    $task_team = htmlspecialchars(trim($_POST['task_team'])); 

    $result = AddTask::addTask($task_title, $task_status, $task_team);

    if ($result == "Task added successfully!") {
        echo "<script>alert('Task added successfully!'); window.location.href = 'your_redirect_page.php';</script>";
    } else {
        echo "<script>alert('Error: " . $result . "'); window.location.href = 'your_redirect_page.php';</script>";
    }
}
?>
