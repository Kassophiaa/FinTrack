<?php

require_once __DIR__ . '/../db/myDB.php';

class BankAccount {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addAccount($user_id, $account_name, $account_type, $balance) {
        $sql = "INSERT INTO bank_accounts (user_id, account_name, account_type, balance, status) VALUES (?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$user_id, $account_name, $account_type, $balance], "isss");
    }

    public function getAccounts($user_id) {
        $sql = "SELECT * FROM bank_accounts WHERE user_id = ? AND status = 'Active' ORDER BY account_name ASC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getAccountById($account_id) {
        $sql = "SELECT * FROM bank_accounts WHERE account_id = ? AND status = 'Active'";
        $this->db->select($sql, [$account_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateAccount($account_id, $account_name, $account_type, $balance) {
        $sql = "UPDATE bank_accounts SET account_name = ?, account_type = ?, balance = ? WHERE account_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$account_name, $account_type, $balance, $account_id], "sssi");
    }

    public function deleteAccount($account_id) {
        $sql = "UPDATE bank_accounts SET status = 'Deleted' WHERE account_id = ?";
        return $this->db->update($sql, [$account_id], "i");
    }

    public function searchAccounts($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT * FROM bank_accounts WHERE user_id = ? AND status = 'Active' AND (account_name LIKE ? OR account_type LIKE ?) ORDER BY account_name ASC";
        $this->db->search($sql, [$user_id, $search, $search], "iss");
        return $this->db->res;
    }

    // Accounts in creation order with this month's money in/out and total transaction count
    public function getAccountsWithActivity($user_id, $keyword = '') {
        $sql = "SELECT b.*,
                    (SELECT COALESCE(SUM(i.amount), 0) FROM income i WHERE i.account_id = b.account_id AND i.status = 'Active' AND i.income_date >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')) AS month_in,
                    (SELECT COALESCE(SUM(e.amount), 0) FROM expenses e WHERE e.account_id = b.account_id AND e.status = 'Active' AND e.expense_date >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')) AS month_out,
                    (SELECT COUNT(*) FROM income i WHERE i.account_id = b.account_id AND i.status = 'Active')
                  + (SELECT COUNT(*) FROM expenses e WHERE e.account_id = b.account_id AND e.status = 'Active') AS tx_count
                FROM bank_accounts b
                WHERE b.user_id = ? AND b.status = 'Active'";
        $params = [$user_id];
        $types = "i";
        if ($keyword !== '') {
            $search = "%" . $keyword . "%";
            $sql .= " AND (b.account_name LIKE ? OR b.account_type LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }
        $sql .= " ORDER BY b.account_id ASC";
        $this->db->select($sql, $params, $types);
        return $this->db->res;
    }

    public function getTotalBalance($user_id) {
        $sql = "SELECT SUM(balance) as total FROM bank_accounts WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

}

?>
