<?php

require_once __DIR__ . '/../db/myDB.php';

class User {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND status = 'Active'";
        $this->db->select($sql, [$username], "s");

        if ($this->db->numRows() > 0) {
            $row = $this->db->res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    public function register($username, $password, $email, $full_name, $phone, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = ($role == 'advisor') ? 'advisor' : 'user';

        $sql = "INSERT INTO users (username, password, email, full_name, phone, role, status) VALUES (?, ?, ?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$username, $hashedPassword, $email, $full_name, $phone, $role], "ssssss");
    }

    public function updateProfile($user_id, $email, $full_name, $phone) {
        $sql = "UPDATE users SET email = ?, full_name = ?, phone = ? WHERE user_id = ?";
        return $this->db->update($sql, [$email, $full_name, $phone, $user_id], "sssi");
    }

    public function updateBioExperience($user_id, $bio, $experience) {
        $sql = "UPDATE users SET bio = ?, experience = ? WHERE user_id = ?";
        return $this->db->update($sql, [$bio, $experience, $user_id], "ssi");
    }

    public function searchUsers($keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT * FROM users WHERE (username LIKE ? OR email LIKE ? OR full_name LIKE ?) AND status = 'Active'";
        $this->db->search($sql, [$search, $search, $search], "sss");
        return $this->db->res;
    }

    public function softDelete($user_id) {
        $sql = "UPDATE users SET status = 'Deleted' WHERE user_id = ?";
        return $this->db->update($sql, [$user_id], "i");
    }

    public function getUsers() {
        $sql = "SELECT * FROM users WHERE status = 'Active'";
        $this->db->select($sql);
        return $this->db->res;
    }

    public function getUserById($user_id) {
        $sql = "SELECT * FROM users WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res->fetch_assoc();
    }

}

?>