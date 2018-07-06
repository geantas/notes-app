<?php
    $notes = new Notes();

    session_start();
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }

    if (isset($_POST['newnote'])) {
        $notes->postANote($_POST['newnote']);
    }

    class Notes {
        var $error_message;

        function postANote($postdata) {
            $username = $_SESSION['username'];

            $errors = array();
            $data = array();
            $notetext = preg_replace( "/\r|\n/", "", $postdata); // replace line breaks //

            if (empty($notetext)) { // validating if note field is not empty //
                $errors['newnote'] = "Note cannot be empty."; 
            } else if(strlen($notetext) <= 1) { // validating if note is not too short (2 characters at least) //
                $errors['newnote'] = "Note is too short (min. 2 characters)."; 
            } else if(strlen($notetext) >= 301) { // validating if note is not too long (300 characters max)
                $errors['newnote'] = "Note is too long (max. 300 characters)."; 
            }

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

            // if there are any errors //
            if (!empty($errors)) {
                // return errors //
                $data['success'] = false;
                $data['errors']  = $errors;
            } else {
                // if there are no errors, process with adding data to database //
                // set variables //
                $currentTime = date('d/m-Y H:i:s');
                $notetext = mysqli_real_escape_string($link, $notetext);
        
                // add note to database //
                $addNotestmt = $link->prepare("INSERT INTO `mxperten`.`notes` (`noteadded`, `notetext`) VALUES (?, ?);");
                $addNotestmt->bind_param("ss", $currentTime, $notetext);
                $addNotestmt->execute();

                // if database error occurs //
                if(!$addNotestmt) {
                    $this->throwError("Error inserting data to database (1).");
                    return false;
                }

                $addNotestmt->close();

                $selectNoteIdstmt = $link->prepare("SELECT noteid FROM `mxperten`.`notes` WHERE `noteadded` = ?");
                $selectNoteIdstmt->bind_param("s", $currentTime);
                $selectNoteIdstmt->execute();

                // if database error //
                if(!$selectNoteIdstmt) {
                    $this->throwError("Could not find note (2).");
                    return false;
                }

                $selectNoteIdResult = $selectNoteIdstmt->get_result();
                // if query return any results AND there are more than 0 results, throw error //
                if(!$selectNoteIdResult && mysqli_num_rows($selectNoteIdResult) < 0) {
                    $this->throwError("Could not return any notes (2.1).");
                    return false;
                }

                // fetch all data //
                $noteRow = mysqli_fetch_assoc($selectNoteIdResult);
                $noteid = $noteRow['noteid'];

                $selectNoteIdstmt->close();

                // check if user exists in database //
                $checkUserInDBstmt = $link->prepare("SELECT `userid` FROM `mxperten`.`users` WHERE `username`= ? ");
                $checkUserInDBstmt->bind_param("s", $username);
                $checkUserInDBstmt->execute();
                $checkUserInDBResult = $checkUserInDBstmt->get_result();

                // if username or password did not match (if query did not return any results or result quantity is equals or less than 0) //
                if(!$checkUserInDBResult || mysqli_num_rows($checkUserInDBResult) <= 0) {
                    $this->throwError("User was not found (4).");
                    return false;
                }

                $userRow = mysqli_fetch_assoc($checkUserInDBResult);
                $userid = $userRow['userid'];

                $checkUserInDBstmt->close();

                $addUserNotestmt = $link->prepare("INSERT INTO `mxperten`.`user_notes` (`userid`, `noteid`) VALUES (?, ?);");
                $addUserNotestmt->bind_param("ss", $userid, $noteid);
                $addUserNotestmt->execute();

                // if database error //
                if(!$addUserNotestmt) {
                    $this->throwError("Error inserting data to database (3).");
                    return false;
                }

                $addUserNotestmt->close();
                $link->close();

                // show success message //
                $data['success'] = true;
                $data['message'] = "Note has been added to database";
                $data['notetext'] = $notetext;
                $data['noteadded'] = $currentTime;
            }

            // return all our data to an AJAX call
            echo json_encode($data);
        }

        function getAllNotes($username) {
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

            // get all notes for the user from database //
            $getAllNotesstmt = $link->prepare(
            "SELECT `mxperten`.`notes`.`noteid`, `noteadded`, `notetext` 
            FROM `mxperten`.`notes` 
            JOIN `mxperten`.`user_notes` ON `mxperten`.`user_notes`.`noteid` = `mxperten`.`notes`.`noteid`
            JOIN `mxperten`.`users` ON `mxperten`.`users`.`userid` = `mxperten`.`user_notes`.`userid`
            WHERE `mxperten`.`users`.`username`= ? 
            ORDER BY `noteadded` DESC");
            $getAllNotesstmt->bind_param("s", $username);
            $getAllNotesstmt->execute();
            $getAllNotesResult = $getAllNotesstmt->get_result();

            // if username or password did not match (if query did not return any results or result quantity is equals or less than 0) //
            if(!$getAllNotesResult || mysqli_num_rows($getAllNotesResult) <= 0) {
                $this->throwError("No notes have been found (4).");
                return false;
            }

            $notesRow = ($getAllNotesResult);

            while($notesRow = mysqli_fetch_assoc($getAllNotesResult)) {
                echo "<div class='single_note'>";
                echo "<p class='addedontext'><span class='addedonvalue'>Added on: </span>" .$notesRow['noteadded']. "</span></p>";
                echo "<br />";
                echo "<div class='notevalue'>";
                echo $notesRow['notetext'];
                echo "</div>";
                echo "</div>";
            }  

            $link->close();
            $getAllNotesstmt->close();
            return $notesRow;
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