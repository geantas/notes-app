<?php
    $users = new Users();
    
    // set connection to database parameters //
    $host = 'localhost';
    $dbusername = 'root';
    $dbpassword = '';
    $database = 'mxperten';
    // establish connection to database //
    $link = new mysqli($host, $dbusername, $dbpassword, $database);

    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

    class Users {
        var $error_message;

        function userRegister() {
            $regUsername = $_POST['regUsername'];
            $regPassword = $_POST['regPassword'];
            $regPassword2 = $_POST['regPassword2'];

            if(empty($regUsername)) {
                $this->throwError("Please enter your new username.");
                return false;
            }
            if(empty($regPassword)) {
                $this->throwError("Please create password.");
                return false;
            }
            if(empty($regPassword2)) {
                $this->throwError("Please confirm password.");
                return false;
            }
            if($regPassword != $regPassword2) {
                $this->throwError("Passwords do not match.");
                return false;
            }
            if(!$this->saveToDatabase($regUsername, $regPassword)) {
                return false;
            }

            session_start();
            $_SESSION['message'] = 'User has been successfully registered.';

            return true;
        }

        function saveToDatabase($regUsername, $regPassword) {
            // set connection to database parameters //
            $host = 'localhost';
            $dbusername = 'root';
            $dbpassword = '';
            $database = 'mxperten';

            // establish connection to database //
            $link = new mysqli($host, $dbusername, $dbpassword, $database);

            // check connection to database //
            if ($link->connect_error) {
                die("Connection failed: " . $link->connect_error);
            }

            // escape special characters //
            $regUsername = mysqli_real_escape_string($link, $regUsername);
            $regPassword = mysqli_real_escape_string($link, $regPassword);
            // hash password //
            $regPassword = md5($regPassword);

            // check if user already exists //
            $checkUserstmt = $link->prepare("SELECT `username` FROM `mxperten`.`users` WHERE `username`= ? ");
            $checkUserstmt->bind_param("s", $regUsername);
            $checkUserstmt->execute();
            $checkUserResult = $checkUserstmt->get_result();

            // if query returns any results AND there are more than 0 results, throw error //
            if($checkUserResult && mysqli_num_rows($checkUserResult) > 0) {
                $this->throwError("Username already exists.");
                return false;
            } 

            $checkUserstmt->close();

            // insert user into database //
            $insertstmt = $link->prepare("INSERT INTO `mxperten`.`users` (`username`, `password`) VALUES (?, ?)");
            $insertstmt->bind_param("ss", $regUsername, $regPassword);
            $insertstmt->execute();

            // if error //
            if(!$insertstmt) {
                $this->throwError("Error inserting data to database.");
                return false;
            }

            $insertstmt->close();
            $link->close();
            return true;
        }

        // login was called //
        function userLogin() {
            if(empty($_POST['username'])) {
                $this->throwError("Please enter username.");
                return false;
            }
            if(empty($_POST['password'])) {
                $this->throwError("Please enter password.");
                return false;
            }
            
            // trim form variables //
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            
            // check username & password in database //
            if(!$this->checkCredentials($username,$password)) {
                return false;
            }

            // start session //
            if(!isset($_SESSION)) {
                session_start();
            }

            // successfully logged in //
            $_SESSION['username']  = $username;
            $_SESSION['message'] = 'Welcome, ' .$_SESSION['username'];
            return true;
        }

        // check username and password in database before login //
        function checkCredentials($username,$password) {
            // set connection to database parameters //
            $host = 'localhost';
            $dbusername = 'root';
            $dbpassword = '';
            $database = 'mxperten';

            // establish connection to database //
            $link = mysqli_connect($host, $dbusername, $dbpassword, $database);

            // check connection to database //
            if ($link->connect_error) {
                die("Connection failed: " . $link->connect_error);
            } 

            $username = mysqli_real_escape_string($link, $username);
            $password = mysqli_real_escape_string($link, $password);
            // hash password //
            $passwordmd5 = md5($password);

            // check if user exists in database //
            $checkUserInDBstmt = $link->prepare("SELECT `username` FROM `mxperten`.`users` WHERE `username`= ? AND `password` = ? ");
            $checkUserInDBstmt->bind_param("ss", $username, $passwordmd5);
            $checkUserInDBstmt->execute();
            $checkUserInDBResult = $checkUserInDBstmt->get_result();

            // if username or password did not match (if query did not return any results or result quantity is equals or less than 0) //
            if(!$checkUserInDBResult || mysqli_num_rows($checkUserInDBResult) <= 0) {
                $this->throwError("Invalid username or password.");
                return false;
            }
            
            // fetch all data //
            $row = mysqli_fetch_assoc($checkUserInDBResult);
            // set session variables //
            session_start();
            $_SESSION['username']  = $row['username'];

            // close connection //
            $checkUserInDBstmt->close();
            $link->close();

            return true;
        }

        function userSignOut() {
            session_start();
            session_unset();
            if(session_destroy()) {
                header("Location: signin.php");
            }
            session_write_close();
            setcookie(session_name(),'',0,'/');
            session_regenerate_id(true);
        }

        function checkIfLoggedIn() {
            if(!isset($_SESSION)) {
                session_start();
            }
            
            if(!isset($_SESSION['username'])) {
                return false;
            }

            return true;
        }

        function getSelfScript()  {
            return htmlentities($_SERVER['PHP_SELF']);
        } 

        function redirectToURL($url) {
            header("Location: $url");
            exit;
        }

        function getErrorMessage() {
            if(empty($this->error_message)) {
                return '';
            }

            $errormsg = nl2br(htmlentities($this->error_message));
            return $errormsg;
        } 

        function throwError($err) {
            $this->error_message .= $err."\r\n";
        }
    }
?>