<?php
require_once "./EncryptionDecription.php";
require_once "../database/users.php"; 
require "./redirectUser.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function login($email,$password) {
    $connection = users();

    $sql = "SELECT id,name,email,acountID, password FROM user";
    $result = mysqli_query($connection, $sql);

   if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {

          $decrypted= Secure::decryption($row["email"]);
            
            if($decrypted == $email) {
                if(password_verify($password , $row["password"])) { 
                    $code = randomCode(100);   
                      $_SESSION["user"] = $row["name"];
                      $_SESSION["userID"] = $row["id"];
                      $_SESSION["id"] = $row["acountID"];
                      $_SESSION["code"] = $code;
                      setcookie("code" , $code , time() + 60*60*24*30 , "/");

                      RedirectUser::redirectUser("login-200");
                      return;
                }
                else{
                     RedirectUser::redirectUser("wrong-password");
                     return;
                }
            }
        }
        RedirectUser::redirectUser("user-not-found");
    } else {
        RedirectUser::redirectUser("user-not-found");
   }
}
function randomCode($length) {
    $digits = '0123456789';
    $code = '';
    $maxIndex = strlen($digits) - 1;
    for($i = 0; $i<$length ;$i++) {
        $code  .=$digits[rand(0,$maxIndex)];
    }
    return $code;

}
if(isset($_POST["email"]) && isset($_POST["password"])) {
    login($_POST["email"] , $_POST["password"]) ;
}