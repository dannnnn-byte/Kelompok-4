<?php
ob_start();
session_start();
include '../koneksi.php';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    if($admin && password_verify($password, $admin['password'])){
        $_SESSION['admin'] = $admin['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <h2 class="text-center mb-4">Login Admin</h2>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php ob_end_flush(); ?>
