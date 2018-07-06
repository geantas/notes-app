<?php
    require_once("includes/users.php");
    require_once("includes/notes.php");

    if(!$users->checkIfLoggedIn()) {
        $users->redirectToURL("signin.php");
        exit;
    } else {
        $username = $_SESSION['username'];
    }

    if (isset($_POST['newnote'])) {
        $notes->postANote($_POST['newnote']);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Gintas Pociunas</title>

        <!-- favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="media/icon.ico" /><!-- IE -->
        <link rel="icon" type="image/x-icon" href="media/icon.ico" /><!-- other browsers -->

        <!-- stylesheets -->
        <link rel="stylesheet" type="text/css" href="./stylesheets/style.css" />

        <!-- google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">

        <!-- javascripts -->
        <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="javascripts/js.js"></script>
    </head>
    <body>
        <div class="container">
            <!-- navbar  -->
            <?php include 'partials/header.php'; ?>
            <!-- messages -->
            <?php include 'partials/messages.php'; ?>
            <!-- main content -->
            <section class="content">
                <div class="left-box">
                    <h2>Add a new note</h2>
                    <br />
                    <form id="addnew" name="addnew" method="post" action="includes/notes.php">
                        <div>
                            <textarea type="text" id="newnote" name="newnote" placeholder="Enter a new note here"></textarea>
                        </div>
                        <input type="submit" class="" value="Submit"></input>
                    </form>
                </div>
                <div class="separator"></div>
                <div class="right-box">
                    <h2>User's notes</h2>
                    <br />
                    <div class="notes_list">
                        <?php 
                            if($notes->getAllNotes($username)) {} // get all user's notes //
                        ?>
                    </div>
                </div>
            </section>
            <!-- footer -->
            <?php include 'partials/footer.php'; ?>
        </div>
    </body>
    <!-- additional JavaScripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</html>