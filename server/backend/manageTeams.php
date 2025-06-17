<?php
require_once '../database/teams.php';
require_once '../backend/EncryptionDecription.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $teamConn = teams(); 

    $user = trim($_POST['user'] ?? '');
    $teamName = trim($_POST['team'] ?? '');
    $role = trim($_POST['role'] ?? 'user'); 

    if (empty($user) || empty($teamName) || empty($role)) {
        die("❌ User, Team, and Role are required.");
    }

    $encryptedTeamName = Secure::encryption($teamName);

    // Get the team by encrypted name
    $sql = "SELECT id, members FROM team WHERE teamName = ?";
    $stmt = mysqli_prepare($teamConn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $encryptedTeamName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $teamId = $row['id'];
            $members = json_decode($row['members'], true) ?? [];

            $newEntry = [$user, $role];


            $alreadyExists = false;
            foreach ($members as $entry) {
                if ($entry[0] === $user) {
                    $alreadyExists = true;
                    break;
                }
            }

            if (!$alreadyExists) {
                $members[] = $newEntry;
                $updatedJson = json_encode($members);

                $updateSql = "UPDATE team SET members = ? WHERE id = ?";
                $updateStmt = mysqli_prepare($teamConn, $updateSql);

                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $updatedJson, $teamId);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                }
            }
        } else {
            echo "Team not found. Possibly a name mismatch or encryption issue.";
            exit;
        }

        mysqli_stmt_close($stmt);
    }

    header("Location: /?teams");
    exit;
}
?>
