<section class="messages">
    <?php 
        if($users->getErrorMessage()) {
            echo "<div class='message error'>" .$users->getErrorMessage()."</div>";
        }
        if($notes->getErrorMessage()) {
            echo "<div class=' message error'>" .$notes->getErrorMessage()."</div>";
        }
        if(isset($_SESSION['message'])) {
            echo "<div class='message success'>" .$_SESSION['message']."</div>";
            unset($_SESSION['message']);
        }
    ?>
</section>