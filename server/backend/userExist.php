<?php
class UserExist {
    public static function userExist($name) {
        $connection = users();

        $sql = "SELECT name FROM user";
        $result = mysqli_query($connection, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $decryptedName = Secure::decryption($row["name"]);
                
                if ($decryptedName === $name) {
                    return true; // user exists
                }
            }
        }

        // No match found
        return false;
    }
}
