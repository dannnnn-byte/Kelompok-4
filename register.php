<?php
include 'koneksi.php';

if(isset($_POST['register'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (nama,email,password) VALUES ('$nama','$email','$password')";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "Error: ".mysqli_error($conn);
    }
}
?>

<form method="POST">
  <input type="text" name="nama" placeholder="Nama" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit" name="register">Daftar</button>
</form>
