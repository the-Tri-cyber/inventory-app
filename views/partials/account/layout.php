<?php
    include '../../config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>bootstrap-5.3.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>bootstrap-5.3.3/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/style.css" />
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
    <header class="bg-primary text-center py-3 mb-2">
        <h1 class="text-white"><?php echo htmlspecialchars($title); ?></h1>
        <div class="container">
            <nav class="bg-primary">
                <div class="container d-flex justify-content-center flex-wrap py-2">
                    <a href="<?php echo BASE_URL; ?>modules/account/" class="btn btn-outline-light me-2 mb-2">Details Account</a>
                    <a href="<?php echo BASE_URL; ?>modules/account/change_email.php" class="btn btn-outline-light me-2 mb-2">Change Email</a>
                    <a href="<?php echo BASE_URL; ?>modules/account/reset_password.php" class="btn btn-outline-light me-2 mb-2">Change Password</a>
                </div>
            </nav>
        </div>
    </header>

    <script src="<?php echo BASE_URL; ?>bootstrap-5.3.3/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_URL; ?>public/assets/js/script.js"></script>
</body>
</html>