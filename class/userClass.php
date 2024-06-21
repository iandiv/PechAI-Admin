<?php
class userClass {
     /* User Login */


     public function adminLogin($usernameEmail, $password, $secret) {

          $db = pdo_init();
          // $hash_password =  $password;
          $stmt = $db->prepare("SELECT aid,arole FROM admin WHERE  ausername=:usernameEmail  AND  apassword=:hash_password");
          $stmt->bindParam("usernameEmail", $usernameEmail, PDO::PARAM_STR);
          $stmt->bindParam("hash_password", $password, PDO::PARAM_STR);
          $stmt->execute();
          $count = $stmt->rowCount();
          $data = $stmt->fetch(PDO::FETCH_OBJ);
          $db = null;
          if ($count) {
               
               $_SESSION['aid'] = $data->aid;
               $_SESSION['arole'] = $data->arole;

               // $_SESSION['google_auth_code'] = $google_auth_code;
               setcookie("a_id", $_SESSION['aid'] , time() + 3600 * 24 * 30, "/"); // Cookie lasts for 30 days

               return true;
          } else {
               return false;
          }
     }

     public function userLogin($usernameEmail, $password, $secret) {

          $db = pdo_init();
           $hash_password = hash('sha256', $password);
          $stmt = $db->prepare("SELECT uid FROM users WHERE  (username=:usernameEmail or email=:usernameEmail) AND  password=:hash_password");
          $stmt->bindParam("usernameEmail", $usernameEmail, PDO::PARAM_STR);
          $stmt->bindParam("hash_password", $hash_password, PDO::PARAM_STR);
          $stmt->execute();
          $count = $stmt->rowCount();
          $data = $stmt->fetch(PDO::FETCH_OBJ);
          $db = null;
          if ($count) {
               $_SESSION['uid'] = $data->uid;
               // $_SESSION['google_auth_code'] = $google_auth_code;
               return true;
          } else {
               return false;
          }
     }

     /* User Registration */
     public function userRegistration($username, $password, $email, $name, $bio, $ocar, $osmartphone, $secret) {
          try {
               $db = pdo_init();
               $st = $db->prepare("SELECT uid FROM users WHERE username=:username OR email=:email");
               $st->bindParam("username", $username, PDO::PARAM_STR);
               $st->bindParam("email", $email, PDO::PARAM_STR);
               $st->execute();
               $count = $st->rowCount();
               if ($count < 1) {
                    $stmt = $db->prepare("INSERT INTO users(username,password,email,name,bio,ocar,osmartphone,google_auth_code) VALUES (:username,:hash_password,:email,:name,:bio,:ocar,:osmartphone,:google_auth_code)");
                    $stmt->bindParam("username", $username, PDO::PARAM_STR);
                     $hash_password = hash('sha256', $password);
                    $stmt->bindParam("hash_password", $hash_password, PDO::PARAM_STR);
                    $stmt->bindParam("email", $email, PDO::PARAM_STR);
                    $stmt->bindParam("name", $name, PDO::PARAM_STR);
                    $stmt->bindParam("bio", $bio, PDO::PARAM_STR);
                    $stmt->bindParam("ocar", $ocar, PDO::PARAM_STR);
                    $stmt->bindParam("osmartphone", $osmartphone, PDO::PARAM_STR);
                    $stmt->bindParam("google_auth_code", $secret, PDO::PARAM_STR);
                    $stmt->execute();
                    $uid = $db->lastInsertId();
                    $db = null;
                    $_SESSION['uid'] = $uid;
                    return true;
               } else {
                    $db = null;
                    return false;
               }
          } catch (PDOException $e) {
               echo '{"error":{"text":' . $e->getMessage() . '}}';
          }
     }

     /* User Details */
     public function userDetails($uid) {
          try {
               $db = pdo_init();
               $stmt = $db->prepare("SELECT email,username,name,bio,ocar,osmartphone,google_auth_code FROM users WHERE uid=:uid");
               $stmt->bindParam("uid", $uid, PDO::PARAM_INT);
               $stmt->execute();
               $data = $stmt->fetch(PDO::FETCH_OBJ);
               return $data;
          } catch (PDOException $e) {
               echo '{"error":{"text":' . $e->getMessage() . '}}';
          }
     }

     public function adminDetails($aid) {
          try {
               $db = pdo_init();
               $stmt = $db->prepare("SELECT ausername,apassword FROM admin WHERE aid=:aid");
               $stmt->bindParam("aid", $aid, PDO::PARAM_INT);
               $stmt->execute();
               $data = $stmt->fetch(PDO::FETCH_OBJ);
               return $data;
          } catch (PDOException $e) {
               echo '{"error":{"text":' . $e->getMessage() . '}}';
          }
     }
}
