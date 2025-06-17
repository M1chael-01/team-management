<?php
require "./userExist.php";
require_once "./EncryptionDecription.php";
require_once "../database/users.php";  
require "./redirectUser.php";

function createAccount($name, $email, $password) {
    if (UserExist::userExist($name)) {
        echo "user exist";
    } else {
        $connection = users();

        $sql = "INSERT INTO user (name, email, password, role, acountID, last_active, team) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $sql);

        if ($stmt) {
            $last_active = date("Y-m-d H:i:s");
            $role = "admin";
            $accountID = 0;

            $encryptedName = Secure::encryption($name);
            $encryptedEmail = Secure::encryption($email);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "ssssiss", 
                $encryptedName, 
                $encryptedEmail, 
                $hashedPassword, 
                $role, 
                $accountID, 
                $last_active, 
                $name
            );

            if (mysqli_stmt_execute($stmt)) {
                $insertedId = mysqli_insert_id($connection);

                // Update the accountID field with the inserted ID
                $updateSql = "UPDATE user SET acountID = ? WHERE id = ?";
                $updateStmt = mysqli_prepare($connection, $updateSql);
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "ii", $insertedId, $insertedId);
                    mysqli_stmt_execute($updateStmt);
                }

                RedirectUser::redirectUser("profile-created");
            } else {
                echo "Insert failed: " . mysqli_error($connection);
            }
        } else {
            echo "Prepare failed: " . mysqli_error($connection);
        }
    }
}

if (isset($_POST["company"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    createAccount($_POST["company"], $_POST["email"], $_POST["password"]);
}
