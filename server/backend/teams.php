<?php
require_once "./EncryptionDecription.php";
require_once "../database/teams.php";

session_start();
$conn = teams();

class Teams {
    public static function createTeam($name) {
        global $conn;
        $id = $_SESSION["id"];
        $teamName = Secure::encryption($name);
        $members = json_encode([]);
        $countTasks = 0;

        $sql = "INSERT INTO team (acountID, teamName, members, countTasks) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issi", $id, $teamName, $members, $countTasks);
            mysqli_stmt_execute($stmt);
        }
    }

    public static function editTeam($id, $name) {
        global $conn;
        $teamName = Secure::encryption($name);

        $sql = "UPDATE team SET teamName = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $teamName, $id);
            mysqli_stmt_execute($stmt);
        }
    }

    public static function deleteTeam($id) {
        global $conn;
        $sql = "DELETE FROM team WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
        }
    }

    public static function verifyToken($submittedToken): bool {
        return isset($_SESSION["code"]) && Secure::encryption($_SESSION["code"]) === $submittedToken;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["code"])) {
    if (!Teams::verifyToken($_POST["code"])) {
        echo "Invalid token.";
        exit;
    }

    if (isset($_POST["team_name_created"])) {
        Teams::createTeam($_POST["team_name_created"]);
        header("Location: /?teams");
        exit;
    }

    if (isset($_POST["edit_team"]) && isset($_POST["team_name_edited"], $_POST["team_id"])) {
        Teams::editTeam((int)$_POST["team_id"], $_POST["team_name_edited"]);
        header("Location: /?teams");
        exit;
    }

    if (isset($_POST["delete_team"]) && isset($_POST["team_id"])) {
        Teams::deleteTeam((int)$_POST["team_id"]);
        header("Location: /?teams");
        exit;
    }
}
?>
