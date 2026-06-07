<?php
class ItemController {
    private Item $item;
    private Transaction $transaction;

    public function __construct() {
        $this->item        = new Item();
        $this->transaction = new Transaction();
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $ext      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo    = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $photo);
        }

        $created = $this->item->createItem($_SESSION['user_id'], [
            'title'       => $_POST['title']       ?? '',
            'description' => $_POST['description'] ?? '',
            'category'    => $_POST['category']    ?? '',
            'weight_kg'   => $_POST['weight_kg']   ?? 0,
            'location'    => $_POST['location']    ?? '',
            'photo'       => $photo,
        ]);

        if ($created) {
            Session::flash('success', 'Item berhasil didaftarkan!');
        } else {
            Session::flash('error', 'Gagal mendaftarkan item.');
        }
        redirect('dashboard');
    }
}