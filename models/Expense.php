<?php
require_once __DIR__ . '/BaseModel.php'; 
class Expense extends Model {
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM expenses ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($title, $amount) {
        $stmt = $this->db->prepare("INSERT INTO expenses (title, amount) VALUES (:title, :amount)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

?>