<?php

require_once __DIR__ . '/../db/myDB.php';

class Advisor {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND role = 'advisor' AND status = 'Active'";
        $this->db->select($sql, [$username], "s");

        if ($this->db->numRows() > 0) {
            $row = $this->db->res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    public function getClients() {
        $sql = "SELECT * FROM users WHERE role = 'user' AND status = 'Active' ORDER BY user_id ASC";
        $this->db->select($sql);
        return $this->db->res;
    }

    public function getClientById($user_id) {
        $sql = "SELECT * FROM users WHERE user_id = ? AND role = 'user' AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function searchClients($keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT * FROM users WHERE role = 'user' AND status = 'Active' AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
        $this->db->search($sql, [$search, $search, $search], "sss");
        return $this->db->res;
    }

    public function getClientIncome($user_id) {
        $sql = "SELECT * FROM income WHERE user_id = ? AND status = 'Active' ORDER BY income_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getClientExpense($user_id) {
        $sql = "SELECT * FROM expenses WHERE user_id = ? AND status = 'Active' ORDER BY expense_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getClientInvestment($user_id) {
        $sql = "SELECT inv.*, b.account_name FROM investments inv LEFT JOIN bank_accounts b ON inv.account_id = b.account_id WHERE inv.user_id = ? AND inv.status = 'Active' ORDER BY inv.investment_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getClientInvestmentSummary($user_id) {
        $sql = "SELECT COUNT(*) as total_count, COALESCE(SUM(amount), 0) as total_invested, COALESCE(SUM(current_value), 0) as total_current_value FROM investments WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        $total_invested = floatval($row['total_invested']);
        $total_current_value = floatval($row['total_current_value']);
        $total_gain_loss = $total_current_value - $total_invested;
        $return_pct = $total_invested > 0 ? ($total_gain_loss / $total_invested) * 100 : 0;
        return [
            'total_count' => intval($row['total_count']),
            'total_invested' => $total_invested,
            'total_current_value' => $total_current_value,
            'total_gain_loss' => $total_gain_loss,
            'return_pct' => $return_pct
        ];
    }

    public function getClientTotalIncome($user_id) {
        $sql = "SELECT SUM(amount) as total FROM income WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getClientTotalExpense($user_id) {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getClientTotalInvestment($user_id) {
        $sql = "SELECT SUM(amount) as total FROM investments WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getAdvisors() {
        $sql = "SELECT * FROM users WHERE role = 'advisor' AND status = 'Active' ORDER BY user_id ASC";
        $this->db->select($sql);
        return $this->db->res;
    }

    public function getAdvisorById($user_id) {
        $sql = "SELECT * FROM users WHERE user_id = ? AND role = 'advisor' AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function searchAdvisors($keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT * FROM users WHERE role = 'advisor' AND status = 'Active' AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
        $this->db->search($sql, [$search, $search, $search], "sss");
        return $this->db->res;
    }

}

?>
