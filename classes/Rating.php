<?php

require_once __DIR__ . '/../db/myDB.php';

class Rating {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addRating($user_id, $advisor_id, $rating, $comment) {
        $sql = "INSERT INTO ratings (user_id, advisor_id, rating, comment, status) VALUES (?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$user_id, $advisor_id, $rating, $comment], "iiis");
    }

    public function getRatingsByAdvisor($advisor_id) {
        $sql = "SELECT r.*, u.full_name as client_name FROM ratings r LEFT JOIN users u ON r.user_id = u.user_id WHERE r.advisor_id = ? AND r.status = 'Active' ORDER BY r.rating_id DESC";
        $this->db->select($sql, [$advisor_id], "i");
        return $this->db->res;
    }

    public function getRatingsByUser($user_id) {
        $sql = "SELECT r.*, u.full_name as advisor_name FROM ratings r LEFT JOIN users u ON r.advisor_id = u.user_id WHERE r.user_id = ? AND r.status = 'Active' ORDER BY r.rating_id DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getRatingById($rating_id) {
        $sql = "SELECT * FROM ratings WHERE rating_id = ? AND status = 'Active'";
        $this->db->select($sql, [$rating_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateRating($rating_id, $rating, $comment) {
        $sql = "UPDATE ratings SET rating = ?, comment = ? WHERE rating_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$rating, $comment, $rating_id], "isi");
    }

    public function deleteRating($rating_id, $user_id = null) {
        if ($user_id !== null) {
            $sql = "UPDATE ratings SET status = 'Deleted' WHERE rating_id = ? AND user_id = ?";
            return $this->db->update($sql, [$rating_id, $user_id], "ii");
        }
        $sql = "UPDATE ratings SET status = 'Deleted' WHERE rating_id = ?";
        return $this->db->update($sql, [$rating_id], "i");
    }

    public function getAverageRating($advisor_id) {
        $sql = "SELECT AVG(rating) as average FROM ratings WHERE advisor_id = ? AND status = 'Active'";
        $this->db->select($sql, [$advisor_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['average'] ? round($row['average'], 1) : 0;
    }

    public function getRatingCount($advisor_id) {
        $sql = "SELECT COUNT(*) as total FROM ratings WHERE advisor_id = ? AND status = 'Active'";
        $this->db->select($sql, [$advisor_id], "i");
        $row = $this->db->res->fetch_assoc();
        return intval($row['total']);
    }

    public function searchRatings($advisor_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT r.*, u.full_name as client_name FROM ratings r LEFT JOIN users u ON r.user_id = u.user_id WHERE r.advisor_id = ? AND r.status = 'Active' AND (u.full_name LIKE ? OR r.comment LIKE ?) ORDER BY r.rating_id DESC";
        $this->db->search($sql, [$advisor_id, $search, $search], "iss");
        return $this->db->res;
    }

}

?>
