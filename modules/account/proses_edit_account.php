<?php
session_start();

include '../../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $role = htmlspecialchars(trim($_POST['role']));
    
    // Memeriksa apakah email sudah ada
    $query = $conn->prepare("SELECT id, gambar FROM users WHERE email = ? AND id != ?");
    $query->bind_param("si", $email, $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Jika email sudah ada, alihkan dengan pesan kesalahan
        $error_message = "Email sudah terdaftar.";
        header("Location: edit.php?id=$id&error=" . urlencode($error_message));
        exit;
    }

    // Ambil gambar lama dari database
    $query = $conn->prepare("SELECT gambar FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    $oldImagePath = $user['gambar'];

    // Menangani pengunggahan gambar
    $gambar = null; // Inisialisasi variabel gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validasi ekstensi file
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Buat nama file baru dengan hash
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../uploads/users/'; 
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar = $newFileName; // Simpan hanya nama file baru
            } else {
                $error_message = "Terjadi kesalahan saat mengunggah gambar.";
                header("Location: edit.php?id=$id&error=" . urlencode($error_message));
                exit;
            } 
        } else {
            $error_message = "Hanya file gambar yang diperbolehkan.";
            header("Location: edit.php?id=$id&error=" . urlencode($error_message));
            exit;
        }
    }

    // Hapus gambar lama jika ada
    if ($oldImagePath && file_exists($uploadFileDir . $oldImagePath)) {
        unlink($uploadFileDir . $oldImagePath); // Menghapus file gambar lama
    }

    // Query untuk update data
    if ($gambar) {
        // Jika ada gambar baru, update gambar di database
        $query = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, gambar = ? WHERE id = ?");
        $query->bind_param("ssssi", $username, $email, $role, $gambar, $id);
    } else {
        // Jika tidak ada gambar baru, update tanpa mengubah gambar
        $query = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $query->bind_param("sssi", $username, $email, $role, $id);
    }

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Pengguna berhasil diperbarui."));
        exit;
    } else {
        // Menangani kesalahan saat eksekusi query
        $error_message = "Gagal mengedit pengguna: " . $query->error;
        header("Location: edit.php?id=$id&error=" . urlencode($error_message));
        exit;
    }
}
?>