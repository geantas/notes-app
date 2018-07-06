<header>
    <div class="top-nav">
        <nav class="ayanEffects ayanHoverEffect_3">
            <ul>
            <?php
                if(isset($_SESSION['username'])) {
                    echo "<li><span>Logged in as: " .$_SESSION['username']. "</span></li>";
                    echo "<li><a href='index.php'>Notes</a></li>";
                    echo "<li><a href='signout.php'>Log Out</a></li>";
                } else {
                    echo "<li><a class='navigation-link' href='signin.php'>Sign In</a></li>";
                }
            ?>
            </ul>
        </nav>
    </div>
</header>