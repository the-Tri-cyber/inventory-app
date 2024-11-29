<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/inventory-app/bootstrap-5.3.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/inventory-app/bootstrap-5.3.3/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="/inventory-app/public/assets/css/style.css" />
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
    <!-- layout start -->
    <div class="d-flex">
        <?php 
        // Memeriksa jenis pengguna yang sedang login
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];
            
            // Menentukan sidebar yang akan digunakan berdasarkan peran pengguna
            switch ($role) {
                case 'admin':
                    include 'partials/sidebar_admin.php';
                    break;
                case 'manager':
                    include 'partials/sidebar_manager.php';
                    break;
                case 'user':
                    include 'partials/sidebar_user.php';
                    break;
                default:
                    include 'partials/sidebar_guest.php'; // Sidebar untuk pengguna yang tidak terautentikasi
                    break;
            }
        } else {
            include 'partials/sidebar_guest.php'; // Sidebar untuk pengguna yang tidak terautentikasi
        }
        ?>
        <div class="main">
            <?php include 'partials/header.php'; ?>
            <div class="content">
                <?php 
                // Memeriksa apakah pengguna berada di dalam direktori 'account'
                if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
                    include 'partials/account/layout.php'; // Memasukkan layout sekunder
                }
                ?>
                <?php echo $content; ?>
            </div>
            <?php include 'partials/footer.php'; ?>
        </div>
    </div>
    <!-- layout end -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <?php
    // Menentukan path untuk file JavaScript berdasarkan kondisi
    if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
        // Jika berada di dalam folder 'account', hilangkan '/inventory-app'
        echo '<script src="bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>';
        echo '<script src="public/assets/js/script.js"></script>';
    } else {
        // Jika tidak, tetap menggunakan path lengkap
        echo '<script src="/inventory-app/bootstrap-5.3.3/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>';
        echo '<script src="/inventory-app/public/assets/js/script.js"></script>';
    }
    ?>
</body>
</html>