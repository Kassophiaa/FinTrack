<?php

require_once __DIR__ . '/../db/myDB.php';

class AdvisorPermission {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function grantPermission($client_id, $advisor_id, $view_income, $view_expenses, $view_investments, $view_bank_accounts, $can_message, $can_give_advice, $can_rate) {
        $sql = "INSERT INTO advisor_permissions (client_id, advisor_id, view_income, view_expenses, view_investments, view_bank_accounts, can_message, can_give_advice, can_rate, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active') ON DUPLICATE KEY UPDATE view_income = VALUES(view_income), view_expenses = VALUES(view_expenses), view_investments = VALUES(view_investments), view_bank_accounts = VALUES(view_bank_accounts), can_message = VALUES(can_message), can_give_advice = VALUES(can_give_advice), can_rate = VALUES(can_rate), status = 'Active'";
        return $this->db->update($sql, [$client_id, $advisor_id, $view_income, $view_expenses, $view_investments, $view_bank_accounts, $can_message, $can_give_advice, $can_rate], "iiiiiiiii");
    }

    public function getPermission($client_id, $advisor_id) {
        $sql = "SELECT * FROM advisor_permissions WHERE client_id = ? AND advisor_id = ? AND status = 'Active'";
        $this->db->select($sql, [$client_id, $advisor_id], "ii");
        return $this->db->res->fetch_assoc();
    }

    public function getPermissionsByClient($client_id) {
        $sql = "SELECT ap.*, u.full_name as advisor_name FROM advisor_permissions ap LEFT JOIN users u ON ap.advisor_id = u.user_id WHERE ap.client_id = ? AND ap.status = 'Active' ORDER BY ap.permission_id DESC";
        $this->db->select($sql, [$client_id], "i");
        return $this->db->res;
    }

    public function getPermissionsByAdvisor($advisor_id) {
        $sql = "SELECT ap.*, u.full_name as client_name FROM advisor_permissions ap LEFT JOIN users u ON ap.client_id = u.user_id WHERE ap.advisor_id = ? AND ap.status = 'Active' ORDER BY ap.permission_id DESC";
        $this->db->select($sql, [$advisor_id], "i");
        return $this->db->res;
    }

    public function getAllPermissions() {
        $sql = "SELECT ap.*, u.full_name as client_name, a.full_name as advisor_name FROM advisor_permissions ap LEFT JOIN users u ON ap.client_id = u.user_id LEFT JOIN users a ON ap.advisor_id = a.user_id WHERE ap.status = 'Active' ORDER BY ap.permission_id DESC";
        $this->db->select($sql);
        return $this->db->res;
    }

    public function hasPermission($client_id, $advisor_id, $permission_type) {
        $allowed = ['view_income', 'view_expenses', 'view_investments', 'view_bank_accounts', 'can_message', 'can_give_advice', 'can_rate'];
        if (!in_array($permission_type, $allowed)) {
            return false;
        }
        $sql = "SELECT $permission_type FROM advisor_permissions WHERE client_id = ? AND advisor_id = ? AND status = 'Active'";
        $this->db->select($sql, [$client_id, $advisor_id], "ii");
        $row = $this->db->res->fetch_assoc();
        return $row ? (bool)$row[$permission_type] : false;
    }

    public function canAdvisorViewIncome($advisor_id, $client_id) {
        return $this->hasPermission($client_id, $advisor_id, 'view_income');
    }

    public function canAdvisorViewExpenses($advisor_id, $client_id) {
        return $this->hasPermission($client_id, $advisor_id, 'view_expenses');
    }

    public function canAdvisorViewInvestments($advisor_id, $client_id) {
        return $this->hasPermission($client_id, $advisor_id, 'view_investments');
    }

    public function canAdvisorViewBankAccounts($advisor_id, $client_id) {
        return $this->hasPermission($client_id, $advisor_id, 'view_bank_accounts');
    }

    public function canMessage($client_id, $advisor_id) {
        return $this->hasPermission($client_id, $advisor_id, 'can_message');
    }

    public function canGiveAdvice($advisor_id, $client_id) {
        return $this->hasPermission($client_id, $advisor_id, 'can_give_advice');
    }

    public function canRate($client_id, $advisor_id) {
        return $this->hasPermission($client_id, $advisor_id, 'can_rate');
    }

    public function revokePermission($client_id, $advisor_id) {
        $sql = "UPDATE advisor_permissions SET status = 'Deleted' WHERE client_id = ? AND advisor_id = ?";
        return $this->db->update($sql, [$client_id, $advisor_id], "ii");
    }

    public function getPermittedAdvisorsForClient($client_id) {
        $sql = "SELECT ap.advisor_id, u.full_name, u.email FROM advisor_permissions ap LEFT JOIN users u ON ap.advisor_id = u.user_id WHERE ap.client_id = ? AND ap.status = 'Active' ORDER BY u.full_name ASC";
        $this->db->select($sql, [$client_id], "i");
        return $this->db->res;
    }

    public function getPermittedClientsForAdvisor($advisor_id) {
        $sql = "SELECT ap.client_id, u.user_id, u.full_name, u.username, u.email FROM advisor_permissions ap LEFT JOIN users u ON ap.client_id = u.user_id WHERE ap.advisor_id = ? AND ap.status = 'Active' ORDER BY u.full_name ASC";
        $this->db->select($sql, [$advisor_id], "i");
        return $this->db->res;
    }

    public function getTotalPermissions() {
        $sql = "SELECT COUNT(*) as total FROM advisor_permissions WHERE status = 'Active'";
        $this->db->select($sql);
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

}

?>
