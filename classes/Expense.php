<?php

require_once __DIR__ . '/../db/myDB.php';

class Expense {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addExpense($user_id, $account_id, $expense_type, $amount, $description, $expense_date) {
        $sql = "INSERT INTO expenses (user_id, account_id, expense_type, amount, description, expense_date, status) VALUES (?, ?, ?, ?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$user_id, $account_id, $expense_type, $amount, $description, $expense_date], "iissss");
    }

    public function getExpense($user_id) {
        $sql = "SELECT e.*, b.account_name FROM expenses e LEFT JOIN bank_accounts b ON e.account_id = b.account_id WHERE e.user_id = ? AND e.status = 'Active' ORDER BY e.expense_date DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getExpenseById($expense_id) {
        $sql = "SELECT * FROM expenses WHERE expense_id = ? AND status = 'Active'";
        $this->db->select($sql, [$expense_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function updateExpense($expense_id, $account_id, $expense_type, $amount, $description, $expense_date) {
        $sql = "UPDATE expenses SET account_id = ?, expense_type = ?, amount = ?, description = ?, expense_date = ? WHERE expense_id = ? AND status = 'Active'";
        return $this->db->update($sql, [$account_id, $expense_type, $amount, $description, $expense_date, $expense_id], "issssi");
    }

    public function deleteExpense($expense_id) {
        $sql = "UPDATE expenses SET status = 'Deleted' WHERE expense_id = ?";
        return $this->db->update($sql, [$expense_id], "i");
    }

    public function searchExpense($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT e.*, b.account_name FROM expenses e LEFT JOIN bank_accounts b ON e.account_id = b.account_id WHERE e.user_id = ? AND e.status = 'Active' AND (e.expense_type LIKE ? OR e.description LIKE ? OR e.amount LIKE ? OR e.expense_date LIKE ?) ORDER BY e.expense_date DESC";
        $this->db->search($sql, [$user_id, $search, $search, $search, $search], "issss");
        return $this->db->res;
    }

    public function getTotalExpense($user_id) {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    // Totals per month for the last $months months: ['YYYY-MM' => total]
    public function getMonthlyTotals($user_id, $months = 6) {
        $sql = "SELECT DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total FROM expenses WHERE user_id = ? AND status = 'Active' AND expense_date >= DATE_SUB(DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01'), INTERVAL ? MONTH) GROUP BY ym";
        $this->db->select($sql, [$user_id, $months - 1], "ii");
        $totals = [];
        while ($row = $this->db->res->fetch_assoc()) {
            $totals[$row['ym']] = floatval($row['total']);
        }
        return $totals;
    }

    // Spending per category for the current month, largest first
    public function getCategoryTotalsThisMonth($user_id) {
        $sql = "SELECT expense_type, SUM(amount) as total FROM expenses WHERE user_id = ? AND status = 'Active' AND MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE()) GROUP BY expense_type ORDER BY total DESC";
        $this->db->select($sql, [$user_id], "i");
        $rows = [];
        while ($row = $this->db->res->fetch_assoc()) {
            $rows[] = ['type' => $row['expense_type'], 'total' => floatval($row['total'])];
        }
        return $rows;
    }

    public function getMonthlyExpense($user_id) {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND status = 'Active' AND MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

}
