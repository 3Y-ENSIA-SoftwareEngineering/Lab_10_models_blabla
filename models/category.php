<?php
require_once __DIR__ . '/BaseModel.php'; 

class Category extends Model {
    /**
     * Retrieve all categories, ordered by creation date.
     *
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new category.
     *
     * @param string $name
     */
    public function add($name) {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    }

    /**
     * Update a category by ID.
     *
     * @param int $id
     * @param string $name
     */
    public function update($id, $name) {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
?>
