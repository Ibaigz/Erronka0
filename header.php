<?php

function logout() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
if (isset($_GET['logout'])) {
    logout();
}
?>
<head>
    <link rel="stylesheet" type="text/css" href="header.css">
</head>

<html>
    <body>
        <?php if (isset($_SESSION['uname'])): ?>
            <a href="?logout=true">
                <button class="login-btn" >Saioa itxi</button>
            </a>
        <?php else: ?>
            <a href="login.php">
				<button class="login-btn">Saioa hasi</button>
			</a>
        <?php endif; ?>
    </body>
</html>
