<?php
class Item {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAvailableItems(?string $category = null, int $limit = 12, int $offset = 0): array {
        $sql = "SELECT i.*, u.name AS mahasiswa_name, u.institution, u.phone AS mahasiswa_phone
                FROM items i
                JOIN users u ON i.user_id = u.id
                WHERE i.status = 'available'";
        $params = [];

        if ($category) {
            $sql .= " AND i.category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getItemsByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getItemById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createItem(int $userId, array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO items (user_id, title, description, category, weight_kg, photo, location)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $userId,
            sanitize($data['title']),
            sanitize($data['description'] ?? ''),
            $data['category'],
            (float) $data['weight_kg'],
            $data['photo'] ?? null,
            sanitize($data['location'] ?? ''),
        ]);
    }

    public function updateStatus(int $itemId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE items SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $itemId]);
    }

    public function getStatsByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total_items,
                SUM(weight_kg) AS total_weight,
                SUM(CASE WHEN status = 'available'  THEN 1 ELSE 0 END) AS available,
                SUM(CASE WHEN status = 'requested'  THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'completed'  THEN 1 ELSE 0 END) AS completed
             FROM items WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getStatsByCategory(): array {
        $stmt = $this->db->query(
            "SELECT category,
                    COUNT(*) AS total_items,
                    SUM(weight_kg) AS total_weight,
                    SUM(CASE WHEN status IN ('picked_up','completed') THEN weight_kg ELSE 0 END) AS weight_saved
             FROM items GROUP BY category ORDER BY total_weight DESC"
        );
        return $stmt->fetchAll();
    }

    public function getMonthlySummary(): array {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS total_items,
                    SUM(weight_kg) AS total_weight
             FROM items
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
               AND status IN ('picked_up','completed')
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        );
        return $stmt->fetchAll();
    }

    public function countAll(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM items")->fetchColumn();
    }

    public function totalWeightSaved(): float {
        return (float) $this->db->query(
            "SELECT SUM(weight_kg) FROM items WHERE status IN ('picked_up','completed')"
        )->fetchColumn();
    }
}