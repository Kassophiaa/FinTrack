<?php

require_once __DIR__ . '/../db/myDB.php';

class Income {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addIncome($user_id, $account_id, $income_type, $amount, $description, $income_date) {
        $sql = "INSERT INTO income (user_id, account_id, income_type, amount, description, income_date, status) VALUES (?, ?, ?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$user_id, $account_id, $income_type, $amount, $description, $income_date], "iissss");
    }

    public function getIncome($user_id) {
        $sql = "SELECT i.*, b.account_name FROM income i LEFT JOIN bank_accounts b ON i.account_id = b.account_id WHERE i.user_id = ? AND i.status = 'Active' ORDER BY i.income_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getIncomeById($income_id) {
        $sql = "SELECT * FROM income WHERE income_id = ? AND status = 'Active'";
        $this->db->select($sql, [$income_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateIncome($income_id, $account_id, $income_type, $amount, $description, $income_date) {
        $sql = "UPDATE income SET account_id = ?, income_type = ?, amount = ?, description = ?, income_date = ? WHERE income_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$account_id, $income_type, $amount, $description, $income_date, $income_id], "issssi");
    }

    public function softDelete($income_id) {
        $sql = "UPDATE income SET status = 'Deleted' WHERE income_id = ?";
        return $this->db->update($sql, [$income_id], "i");
    }

    public function searchIncome($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT i.*, b.account_name FROM income i LEFT JOIN bank_accounts b ON i.account_id = b.account_id WHERE i.user_id = ? AND i.status = 'Active' AND (i.income_type LIKE ? OR i.description LIKE ? OR i.amount LIKE ? OR i.income_date LIKE ?) ORDER BY i.income_date DESC";
        $this->db->search($sql, [$user_id, $search, $search, $search, $search], "issss");
        return $this->db->res;
    }

    public function getTotalIncome($user_id) {
        $sql = "SELECT SUM(amount) as total FROM income WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getMonthlyIncome($user_id) {
        $sql = "SELECT SUM(amount) as total FROM income WHERE user_id = ? AND status = 'Active' AND MONTH(income_date) = MONTH(CURRENT_DATE()) AND YEAR(income_date) = YEAR(CURRENT_DATE())";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

}

?>
