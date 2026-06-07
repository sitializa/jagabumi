<?php
class AuthController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->user->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['user']    = $user;
                redirect('dashboard');
            } else {
                Session::flash('error', 'Email atau password salah.');
            }
        }
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');

            if ($this->user->findByEmail($email)) {
                Session::flash('error', 'Email sudah terdaftar.');
                return;
            }

            $created = $this->user->create([
                'name'        => $_POST['name']        ?? '',
                'email'       => $email,
                'password'    => $_POST['password']    ?? '',
                'role'        => $_POST['role']        ?? 'mahasiswa',
                'phone'       => $_POST['phone']       ?? '',
                'institution' => $_POST['institution'] ?? '',
            ]);

            if ($created) {
                Session::flash('success', 'Registrasi berhasil! Silakan login.');
                redirect('login');
            } else {
                Session::flash('error', 'Registrasi gagal, coba lagi.');
            }
        }
    }
}