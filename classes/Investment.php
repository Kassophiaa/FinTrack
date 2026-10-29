<?php

require_once __DIR__ . '/../db/myDB.php';

class Investment {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addInvestment($user_id, $account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date) {
        $sql = "INSERT INTO investments (user_id, account_id, investment_type, investment_name, amount, current_value, description, investment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$user_id, $account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date], "iissssss");
    }

    public function getInvestment($user_id) {
        $sql = "SELECT inv.*, b.account_name FROM investments inv LEFT JOIN bank_accounts b ON inv.account_id = b.account_id WHERE inv.user_id = ? AND inv.status = 'Active' ORDER BY inv.investment_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getInvestmentById($investment_id) {
        $sql = "SELECT * FROM investments WHERE investment_id = ? AND status = 'Active'";
        $this->db->select($sql, [$investment_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateInvestment($investment_id, $account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date) {
        $sql = "UPDATE investments SET account_id = ?, investment_type = ?, investment_name = ?, amount = ?, current_value = ?, description = ?, investment_date = ? WHERE investment_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date, $investment_id], "issssssi");
    }

    public function deleteInvestment($investment_id) {
        $sql = "UPDATE investments SET status = 'Deleted' WHERE investment_id = ?";
        return $this->db->update($sql, [$investment_id], "i");
    }

    public function searchInvestment($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT inv.*, b.account_name FROM investments inv LEFT JOIN bank_accounts b ON inv.account_id = b.account_id WHERE inv.user_id = ? AND inv.status = 'Active' AND (inv.investment_type LIKE ? OR inv.investment_name LIKE ? OR inv.description LIKE ? OR inv.amount LIKE ? OR b.account_name LIKE ? OR inv.investment_date LIKE ?) ORDER BY inv.investment_date DESC";
        $this->db->search($sql, [$user_id, $search, $search, $search, $search, $search, $search], "issssss");
        return $this->db->res;
    }

    public function getInvestmentSummary($user_id) {
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

    public function getTotalInvestment($user_id) {
        $sql = "SELECT SUM(amount) as total FROM investments WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function getMonthlyInvestment($user_id) {
        $sql = "SELECT SUM(amount) as total FROM investments WHERE user_id = ? AND status = 'Active' AND MONTH(investment_date) = MONTH(CURRENT_DATE()) AND YEAR(investment_date) = YEAR(CURRENT_DATE())";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

}

?>
