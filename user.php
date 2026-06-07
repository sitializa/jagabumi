<?php
class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, phone, institution)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            sanitize($data['name']),
            sanitize($data['email']),
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
            sanitize($data['phone'] ?? ''),
            sanitize($data['institution'] ?? ''),
        ]);
    }

    public function countAll(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
}