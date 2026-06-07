<?php
class Transaction {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(int $itemId, int $umkmId, int $mahasiswaId, string $pickupDate, string $notes = ''): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (item_id, umkm_id, mahasiswa_id, status, pickup_date, notes)
             VALUES (?, ?, ?, 'pending', ?, ?)"
        );
        return $stmt->execute([$itemId, $umkmId, $mahasiswaId, $pickupDate, $notes]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByUmkm(int $umkmId): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, i.title AS item_title, i.category, i.weight_kg, i.location,
                    u.name AS mahasiswa_name, u.phone AS mahasiswa_phone
             FROM transactions t
             JOIN items i ON t.item_id = i.id
             JOIN users u ON t.mahasiswa_id = u.id
             WHERE t.umkm_id = ?
             ORDER BY t.created_at DESC"
        );
        $stmt->execute([$umkmId]);
        return $stmt->fetchAll();
    }

    public function getByMahasiswa(int $mahasiswaId): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, i.title AS item_title, i.category,
                    u.name AS umkm_name, u.phone AS umkm_phone, u.institution AS umkm_institution
             FROM transactions t
             JOIN items i ON t.item_id = i.id
             JOIN users u ON t.umkm_id = u.id
             WHERE t.mahasiswa_id = ?
             ORDER BY t.created_at DESC"
        );
        $stmt->execute([$mahasiswaId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE transactions SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function hasActiveRequest(int $itemId, int $umkmId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM transactions
             WHERE item_id = ? AND umkm_id = ? AND status NOT IN ('cancelled','completed')"
        );
        $stmt->execute([$itemId, $umkmId]);
        return $stmt->fetchColumn() > 0;
    }

    public function countAll(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM transactions WHERE status = 'completed'"
        )->fetchColumn();
    }
}