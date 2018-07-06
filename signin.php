<?php 
    require_once("includes/users.php");
    require_once("includes/notes.php");

    if($users->checkIfLoggedIn()) {
        $users->redirectToURL("index.php");
        exit;
    }

    if(isset($_POST['username'])) {
        if($users->userLogin()) {
            $users->redirectToURL("index.php");
        }
    }

    if(isset($_POST['regUsername'])) {
        if($users->userRegister()) {
            $users->redirectToURL("signin.php");
        }
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
        <link rel='stylesheet' href='stylesheets/style.css'/>

        <!-- google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">

        <!-- javascripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="javascripts/gen_validatorv4.js"></script> <!-- taken from http://javascript-coder.com/html-form/javascript-form-validation.phtml -->
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
                    <h2>Login</h2>
                    <br />
                    <form id="login" method="post" action="<?php echo $users->getSelfScript(); ?>">
                        <div class="">
                            <input type="text" class="" name="username" placeholder="Username">
                        </div>
                        <div class="">
                            <input type="password" class="" name="password" placeholder="Password">
                        </div>
                        <input type="submit" class="" value="Login"></input>
                    </form>
                </div>
                <div class="separator"></div>
                <div class="right-box">
                    <h2>Register</h2>
                    <br />
                    <form id="register" method="post" action="<?php echo $users->getSelfScript(); ?>">
                        <div class="">
                            <input type="text" class="" placeholder="Username" name="regUsername">
                        </div>
                        <div class="">
                            <input type="password" class="" placeholder="Password" name="regPassword">
                        </div>
                        <div class="">
                            <input type="password" class="" placeholder="Repeat password" name="regPassword2">
                        </div>
                        <input type="submit" class="" value="Register"></input>
                    </form>
                </div>
            </section>
            <!-- footer -->
            <?php include 'partials/footer.php'; ?>
        </div>
    </body>
    <!-- additional JavaScripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
    <script type="text/javascript"> // taken from http://javascript-coder.com/html-form/javascript-form-validation.phtml //
        // login and register client side validation //
        var loginValidator  = new Validator("login");
        var registerValidator  = new Validator("register");

        loginValidator.addValidation("username","req","Please enter username.");
        loginValidator.addValidation("password","req", "Please enter password.");

        registerValidator.addValidation("regUsername","req","Please enter username.");
        registerValidator.addValidation("regUsername","alnum","Username can only be alphanumeric.");
        registerValidator.addValidation("regUsername","maxlen=20","Username is too long (max. 20 characters).");
        registerValidator.addValidation("regUsername","minlen=2","Username is too short (min. 2 characters).");
        registerValidator.addValidation("regPassword","req", "Please enter password.");
        registerValidator.addValidation("regPassword","alnum", "Password can only be alphanumeric.");
        registerValidator.addValidation("regPassword","minlen=2","Password is too short (min. 4 characters).");
        registerValidator.addValidation("regPassword2","eqelmnt=regPassword","Passwords do not match.");
    </script>
</html>
