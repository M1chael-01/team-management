<?php
require_once "./EncryptionDecription.php";
require_once "../database/users.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ManageUser {
    private $conn;

    public function __construct() {
        $this->conn = users(); 
    }

    public function insertUser(): string {
        if (!$this->checkTokens($_GET["add"])) {
            return "Invalid or missing token.";
        }

        if (!isset($_POST["name"], $_POST["email"], $_POST["password"], $_SESSION["id"])) {
            return "Missing user data.";
        }

        if ($this->userExist($_POST["email"])) {
            return "This email is already taken.";
        }

        $name  = htmlspecialchars(trim(Secure::encryption($_POST["name"])));
        $email  = htmlspecialchars(trim(Secure::encryption($_POST["email"])));
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $role = "user";
        $accountID = $_SESSION["id"];
        $lastActive = date("Y-m-d H:i:s");

        $sql = "INSERT INTO user (name, email, password, role, acountID, last_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssis", $name, $email, $password, $role, $accountID, $lastActive);
            if (mysqli_stmt_execute($stmt)) {
                return "User inserted successfully.";
            } else {
                return "Insert failed: " . mysqli_stmt_error($stmt);
            }
        } else {
            return "SQL error: " . mysqli_error($this->conn);
        }
    }

    public function deleteUser(int $id): string {
        if (!$this->checkTokens($_GET["delete"])) {
            return "Invalid or missing token.";
        }

        $sql = "DELETE FROM user WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $this->logAction("User ID $id deleted by account ID: " . $_SESSION["id"]);
                return "User deleted successfully.";
            } else {
                return "Delete failed: " . mysqli_stmt_error($stmt);
            }
        }
        return "SQL error: " . mysqli_error($this->conn);
    }

    public function editUser(): string {
    if (!isset($_GET["edit"]) || !$this->checkTokens($_GET["edit"])) {
        return "Invalid or missing token.";
    }

    if (!isset($_POST["user_id"], $_POST["name"], $_POST["email"], $_POST["role"])) {
        return "Missing user data.";
    }

    $id    = intval($_POST["user_id"]);
    $name  = htmlspecialchars(trim(Secure::encryption($_POST["name"])));
    $email = htmlspecialchars(trim(Secure::encryption($_POST["email"])));
    $role  = $_POST["role"];

    $sqlCheck = "SELECT id FROM user WHERE email = ? AND id != ?";
    $stmtCheck = mysqli_prepare($this->conn, $sqlCheck);
    if ($stmtCheck) {
        mysqli_stmt_bind_param($stmtCheck, "si", $email, $id);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_store_result($stmtCheck);
        if (mysqli_stmt_num_rows($stmtCheck) > 0) {
            return "This email is already taken by another user.";
        }
    }

    $sql = "UPDATE user SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = mysqli_prepare($this->conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $role, $id);
        if (mysqli_stmt_execute($stmt)) {
            $this->logAction("User ID $id edited by account ID: " . $_SESSION["id"]);
             
            return "User updated successfully.";
        } else {
            return "Update failed: " . mysqli_stmt_error($stmt);
        }
    }
    return "SQL error: " . mysqli_error($this->conn);
}


    private function userExist(string $email): bool {
        $encryptedEmail = Secure::encryption($email);

        $sql = "SELECT email FROM user";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                if ($encryptedEmail === $row["email"]) {
                    return true;
                }
            }
        }

        return false;
    }

    private function checkTokens(string $token): bool {
        return isset($_SESSION["code"]) && Secure::encryption($_SESSION["code"]) === $token;
    }

    private function logAction(string $message): void {
        $logLine = "[" . date("Y-m-d H:i:s") . "] $message\n";
        file_put_contents(__DIR__ . "../../logs/actions.log", $logLine, FILE_APPEND);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $manager = new ManageUser();
    if(isset($_GET["add"])) {
    $result = $manager->insertUser();
    echo $result;
    }
    elseif(isset($_GET["delete"]) && isset($_POST["user_id"])) {
        $id = $_POST["user_id"];
        $result = $manager->deleteUser($id);
        echo $result;
    }
    elseif (isset($_GET["edit"])) {
        echo $manager->editUser();
    }
}
?>
