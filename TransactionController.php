<?php
class TransactionController {
    private Transaction $transaction;
    private Item $item;

    public function __construct() {
        $this->transaction = new Transaction();
        $this->item        = new Item();
    }

    public function requestPickup(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $itemId     = (int) ($_POST['item_id']     ?? 0);
        $pickupDate = $_POST['pickup_date'] ?? '';
        $notes      = $_POST['notes']       ?? '';
        $umkmId     = $_SESSION['user_id'];

        $item = $this->item->getItemById($itemId);

        if (!$item || $item['status'] !== 'available') {
            Session::flash('error', 'Item tidak tersedia.');
            redirect('dashboard');
            return;
        }

        if ($this->transaction->hasActiveRequest($itemId, $umkmId)) {
            Session::flash('error', 'Anda sudah pernah request item ini.');
            redirect('dashboard');
            return;
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $this->transaction->create($itemId, $umkmId, $item['user_id'], $pickupDate, $notes);
            $this->item->updateStatus($itemId, 'requested');
            $db->commit();
            Session::flash('success', 'Request pickup berhasil diajukan!');
        } catch (Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        redirect('dashboard');
    }

    public function respond(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $trxId  = (int) ($_POST['transaction_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        $trx = $this->transaction->findById($trxId);
        if (!$trx || $trx['mahasiswa_id'] !== $_SESSION['user_id']) {
            Session::flash('error', 'Transaksi tidak valid.');
            redirect('dashboard');
            return;
        }

        if ($action === 'accept') {
            $this->transaction->updateStatus($trxId, 'accepted');
            Session::flash('success', 'Request disetujui!');
        } elseif ($action === 'reject') {
            $this->transaction->updateStatus($trxId, 'cancelled');
            $this->item->updateStatus($trx['item_id'], 'available');
            Session::flash('success', 'Request ditolak.');
        }

        redirect('dashboard');
    }

    public function confirmPickup(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $trxId = (int) ($_POST['transaction_id'] ?? 0);
        $trx   = $this->transaction->findById($trxId);

        if (!$trx || $trx['umkm_id'] !== $_SESSION['user_id']) {
            Session::flash('error', 'Transaksi tidak valid.');
            redirect('dashboard');
            return;
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $this->transaction->updateStatus($trxId, 'completed');
            $this->item->updateStatus($trx['item_id'], 'completed');
            $db->commit();
            Session::flash('success', 'Pickup dikonfirmasi, transaksi selesai!');
        } catch (Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        redirect('dashboard');
    }
}