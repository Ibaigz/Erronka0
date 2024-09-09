<?php
session_start();

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

<html>
    <body>
        <?php if (isset($_SESSION['uname'])): ?>
            <a href="?logout=true">
                <button>Saioa itxi</button>
            </a>
        <?php else: ?>
            <a href="login.php">
				<button>Saioa hasi</button>
			</a>
        <?php endif; ?>
    </body>
</html>
