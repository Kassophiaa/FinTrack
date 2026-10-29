<?php

require_once __DIR__ . '/../db/myDB.php';

class Advice {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addAdvice($advisor_id, $user_id, $title, $content) {
        $sql = "INSERT INTO advices (advisor_id, user_id, title, content, created_at, status) VALUES (?, ?, ?, ?, NOW(), 'Active')";
        return $this->db->insert($sql, [$advisor_id, $user_id, $title, $content], "iiss");
    }

    public function getAdviceByAdvisor($advisor_id) {
        $sql = "SELECT a.*, u.full_name as client_name FROM advices a LEFT JOIN users u ON a.user_id = u.user_id WHERE a.advisor_id = ? AND a.status = 'Active' ORDER BY a.advice_id DESC";
        $this->db->select($sql, [$advisor_id], "i");
        return $this->db->res;
    }

    public function getAdviceByUser($user_id) {
        $sql = "SELECT a.*, u.full_name as advisor_name FROM advices a LEFT JOIN users u ON a.advisor_id = u.user_id WHERE a.user_id = ? AND a.status = 'Active' ORDER BY a.advice_id DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getAdviceById($advice_id) {
        $sql = "SELECT * FROM advices WHERE advice_id = ? AND status = 'Active'";
        $this->db->select($sql, [$advice_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateAdvice($advice_id, $title, $content) {
        $sql = "UPDATE advices SET title = ?, content = ? WHERE advice_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$title, $content, $advice_id], "ssi");
    }

    public function deleteAdvice($advice_id) {
        $sql = "UPDATE advices SET status = 'Deleted' WHERE advice_id = ?";
        return $this->db->update($sql, [$advice_id], "i");
    }

    public function searchAdvice($advisor_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT a.*, u.full_name as client_name FROM advices a LEFT JOIN users u ON a.user_id = u.user_id WHERE a.advisor_id = ? AND a.status = 'Active' AND (a.title LIKE ? OR a.content LIKE ?) ORDER BY a.advice_id DESC";
        $this->db->search($sql, [$advisor_id, $search, $search], "iss");
        return $this->db->res;
    }

    public function searchAdviceByUser($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT a.*, u.full_name as advisor_name FROM advices a LEFT JOIN users u ON a.advisor_id = u.user_id WHERE a.user_id = ? AND a.status = 'Active' AND (a.title LIKE ? OR a.content LIKE ?) ORDER BY a.advice_id DESC";
        $this->db->search($sql, [$user_id, $search, $search], "iss");
        return $this->db->res;
    }

}

?>
