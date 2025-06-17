<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../database/calendar.php";
require "./EncryptionDecription.php";
require "./redirectUser.php";

if (
    isset($_POST["eventName"]) &&
    isset($_POST["start"]) &&
    isset($_POST["end"]) &&
    isset($_POST["date"]) &&
    isset($_POST["action"]) &&
    isset($_SESSION["user"])
) {
    $connection = calendar();
    $eventName = Secure::encryption(htmlspecialchars($_POST["eventName"]));
    $start = htmlspecialchars($_POST["start"]);
    $end = htmlspecialchars($_POST["end"]);
    $date = htmlspecialchars($_POST["date"]);
    $person = $_SESSION["user"];
    $action = $_POST["action"];
    $eventId = isset($_POST["event_id"]) ? intval($_POST["event_id"]) : null;

    if ($action === "save") {
        (isset($_SESSION["id"])) ? $id = $_SESSION["id"] : $id = 0;
        $sql = "INSERT INTO info (acountID,eventTitle, date, timeStart, timeEnd, person) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isssss",$id, $eventName, $date, $start, $end, $person);
            if (mysqli_stmt_execute($stmt)) {
                RedirectUser::redirectUser("event-created");
            } else {
                 RedirectUser::redirectUser("error");
            }
        }
    } elseif ($action === "delete") {
        if ($eventId === null) {
             RedirectUser::redirectUser("error");
            exit;
        }
        $sql = "DELETE FROM info WHERE id = ? AND person = ?";
        $stmt = mysqli_prepare($connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $eventId, $person);
            if (mysqli_stmt_execute($stmt)) {
                 RedirectUser::redirectUser("event-deleted");
            } else {
                echo "error: " . mysqli_stmt_error($stmt);
            }
        }
    } elseif ($action === "update") {
        if ($eventId === null) {
             RedirectUser::redirectUser("error");
            exit;
        }
        echo $eventId;
        $sql = "UPDATE info SET eventTitle = ?, date = ?, timeStart = ?, timeEnd = ? WHERE id = ? AND person = ?";
        $stmt = mysqli_prepare($connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssis", $eventName, $date, $start, $end, $eventId, $person);
            if (mysqli_stmt_execute($stmt)) {
                RedirectUser::redirectUser("event-updated");
            } else {
               RedirectUser::redirectUser("error");
            }
        }
    }
} else {
   RedirectUser::redirectUser("missing-required");
}
?>
