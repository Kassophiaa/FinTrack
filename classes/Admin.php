<?php

require_once __DIR__ . '/../db/myDB.php';

class Admin {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND role = 'admin' AND status = 'Active'";
        $this->db->select($sql, [$username], "s");

        if ($this->db->numRows() > 0) {
            $row = $this->db->res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    public function getUsers() {
        $sql = "SELECT u.* FROM users u ORDER BY u.user_id ASC";
        $this->db->select($sql);
        return $this->db->res;
    }

    public function getUserById($user_id) {
        $sql = "SELECT * FROM users WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function searchUsers($keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT u.* FROM users u WHERE u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?";
        $this->db->search($sql, [$search, $search, $search], "sss");
        return $this->db->res;
    }

    public function toggleUserStatus($user_id) {
        $this->db->select("SELECT status FROM users WHERE user_id = ?", [$user_id], "i");
        if ($this->db->numRows() == 0) return false;
        $row = $this->db->res->fetch_assoc();
        $newStatus = ($row['status'] === 'Active') ? 'Inactive' : 'Active';
        return $this->db->update("UPDATE users SET status = ? WHERE user_id = ?", [$newStatus, $user_id], "si");
    }

    public function updateUserRole($user_id, $role) {
        $sql = "UPDATE users SET role = ? WHERE user_id = ?";
        return $this->db->update($sql, [$role, $user_id], "si");
    }

    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalAdvisors() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'advisor' AND status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalClients() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user' AND status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalPermissions() {
        $sql = "SELECT COUNT(*) as total FROM advisor_permissions WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalRatings() {
        $sql = "SELECT COUNT(*) as total FROM ratings WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalAdvices() {
        $sql = "SELECT COUNT(*) as total FROM advices WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getTotalMessages() {
        $sql = "SELECT COUNT(*) as total FROM messages WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getAverageRating() {
        $sql = "SELECT ROUND(AVG(rating), 1) as avg_rating FROM ratings WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['avg_rating'] ? $row['avg_rating'] : 0;
    }

    public function getRecentUsers($limit = 5) {
        $sql = "SELECT user_id, full_name, username, email, role FROM users WHERE status = 'Active' ORDER BY user_id DESC LIMIT ?";
        $this->db->select($sql, [$limit], "i");
        return $this->db->res;
    }

    public function getRoleBreakdown() {
        $sql = "SELECT role, COUNT(*) as count FROM users WHERE status = 'Active' GROUP BY role";
        $this->db->select($sql);
        $data = [];
        while ($row = $this->db->res->fetch_assoc()) {
            $data[$row['role']] = $row['count'];
        }
        return $data;
    }

}

?>
