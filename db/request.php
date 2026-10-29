<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/Income.php';
require_once '../classes/Expense.php';
require_once '../classes/Investment.php';
require_once '../classes/BankAccount.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $module = isset($_POST['module']) ? trim($_POST['module']) : '';
    $user_id = intval($_SESSION['user_id']);

    $bankAccount = new BankAccount();

    // INCOME
    if ($module == "income") {

        $income = new Income();

        if ($action == "add") {
            $account_id = intval($_POST['account_id'] ?? 0);
            $income_type = trim($_POST['income_type'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $income_date = trim($_POST['income_date'] ?? '');

            if ($account_id <= 0 || empty($income_type) || $amount <= 0 || empty($income_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($income_type) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $income->addIncome($user_id, $account_id, $income_type, $amount, $description, $income_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Income added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add income"]);
            }
            exit;
        }

        if ($action == "display") {
            $result = $income->getIncome($user_id);
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "edit") {
            $income_id = intval($_POST['income_id'] ?? 0);
            $account_id = intval($_POST['account_id'] ?? 0);
            $income_type = trim($_POST['income_type'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $income_date = trim($_POST['income_date'] ?? '');

            if ($income_id <= 0 || $account_id <= 0 || empty($income_type) || $amount <= 0 || empty($income_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($income_type) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $existing = $income->getIncomeById($income_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Income record not found or access denied"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $income->updateIncome($income_id, $account_id, $income_type, $amount, $description, $income_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Income updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update income"]);
            }
            exit;
        }

        if ($action == "delete") {
            $income_id = intval($_POST['income_id'] ?? 0);

            if ($income_id <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid income ID"]);
                exit;
            }

            $existing = $income->getIncomeById($income_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Income record not found or access denied"]);
                exit;
            }

            $result = $income->softDelete($income_id);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Income deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete income"]);
            }
            exit;
        }

        if ($action == "search") {
            $keyword = trim($_POST['keyword'] ?? '');

            if (empty($keyword)) {
                $result = $income->getIncome($user_id);
            } else {
                $result = $income->searchIncome($user_id, $keyword);
            }
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

    }

    // EXPENSE
    if ($module == "expense") {

        $expense = new Expense();

        if ($action == "add") {
            $account_id = intval($_POST['account_id'] ?? 0);
            $expense_type = trim($_POST['expense_type'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $expense_date = trim($_POST['expense_date'] ?? '');

            if ($account_id <= 0 || empty($expense_type) || $amount <= 0 || empty($expense_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($expense_type) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $expense->addExpense($user_id, $account_id, $expense_type, $amount, $description, $expense_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Expense added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add expense"]);
            }
            exit;
        }

        if ($action == "display") {
            $result = $expense->getExpense($user_id);
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "edit") {
            $expense_id = intval($_POST['expense_id'] ?? 0);
            $account_id = intval($_POST['account_id'] ?? 0);
            $expense_type = trim($_POST['expense_type'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $expense_date = trim($_POST['expense_date'] ?? '');

            if ($expense_id <= 0 || $account_id <= 0 || empty($expense_type) || $amount <= 0 || empty($expense_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($expense_type) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $existing = $expense->getExpenseById($expense_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Expense record not found or access denied"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $expense->updateExpense($expense_id, $account_id, $expense_type, $amount, $description, $expense_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Expense updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update expense"]);
            }
            exit;
        }

        if ($action == "delete") {
            $expense_id = intval($_POST['expense_id'] ?? 0);

            if ($expense_id <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid expense ID"]);
                exit;
            }

            $existing = $expense->getExpenseById($expense_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Expense record not found or access denied"]);
                exit;
            }

            $result = $expense->deleteExpense($expense_id);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Expense deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete expense"]);
            }
            exit;
        }

        if ($action == "search") {
            $keyword = trim($_POST['keyword'] ?? '');

            if (empty($keyword)) {
                $result = $expense->getExpense($user_id);
            } else {
                $result = $expense->searchExpense($user_id, $keyword);
            }
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

    }

    // INVESTMENT
    if ($module == "investment") {

        $investment = new Investment();

        if ($action == "add") {
            $account_id = intval($_POST['account_id'] ?? 0);
            $investment_type = trim($_POST['investment_type'] ?? '');
            $investment_name = trim($_POST['investment_name'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $current_value = $_POST['current_value'] !== '' && $_POST['current_value'] !== null ? floatval($_POST['current_value']) : null;
            $description = trim($_POST['description'] ?? '');
            $investment_date = trim($_POST['investment_date'] ?? '');

            if ($account_id <= 0 || empty($investment_type) || $amount <= 0 || empty($investment_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($investment_type) > 100 || strlen($investment_name) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $investment->addInvestment($user_id, $account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Investment added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add investment"]);
            }
            exit;
        }

        if ($action == "display") {
            $result = $investment->getInvestment($user_id);
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "summary") {
            $summary = $investment->getInvestmentSummary($user_id);
            echo json_encode(["status" => "success", "data" => $summary]);
            exit;
        }

        if ($action == "edit") {
            $investment_id = intval($_POST['investment_id'] ?? 0);
            $account_id = intval($_POST['account_id'] ?? 0);
            $investment_type = trim($_POST['investment_type'] ?? '');
            $investment_name = trim($_POST['investment_name'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $current_value = $_POST['current_value'] !== '' && $_POST['current_value'] !== null ? floatval($_POST['current_value']) : null;
            $description = trim($_POST['description'] ?? '');
            $investment_date = trim($_POST['investment_date'] ?? '');

            if ($investment_id <= 0 || $account_id <= 0 || empty($investment_type) || $amount <= 0 || empty($investment_date)) {
                echo json_encode(["status" => "error", "message" => "Please fill all required fields and enter a valid amount"]);
                exit;
            }

            if (strlen($investment_type) > 100 || strlen($investment_name) > 100 || strlen($description) > 500) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            $existing = $investment->getInvestmentById($investment_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Investment not found or access denied"]);
                exit;
            }

            $account = $bankAccount->getAccountById($account_id);
            if (!$account || $account['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Invalid bank account selected"]);
                exit;
            }

            $result = $investment->updateInvestment($investment_id, $account_id, $investment_type, $investment_name, $amount, $current_value, $description, $investment_date);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Investment updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update investment"]);
            }
            exit;
        }

        if ($action == "delete") {
            $investment_id = intval($_POST['investment_id'] ?? 0);

            if ($investment_id <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid investment ID"]);
                exit;
            }

            $existing = $investment->getInvestmentById($investment_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Investment not found or access denied"]);
                exit;
            }

            $result = $investment->deleteInvestment($investment_id);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Investment deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete investment"]);
            }
            exit;
        }

        if ($action == "search") {
            $keyword = trim($_POST['keyword'] ?? '');

            if (empty($keyword)) {
                $result = $investment->getInvestment($user_id);
            } else {
                $result = $investment->searchInvestment($user_id, $keyword);
            }
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

    }

    // BANK ACCOUNT
    if ($module == "account") {

        $account = new BankAccount();

        if ($action == "add") {
            $account_name = trim($_POST['account_name'] ?? '');
            $account_type = trim($_POST['account_type'] ?? '');
            $balance = floatval($_POST['balance'] ?? 0);

            if (empty($account_name) || empty($account_type)) {
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            if (strlen($account_name) > 100 || strlen($account_type) > 100) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            if ($balance < 0) {
                echo json_encode(["status" => "error", "message" => "Balance cannot be negative"]);
                exit;
            }

            $result = $account->addAccount($user_id, $account_name, $account_type, $balance);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Account added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add account"]);
            }
            exit;
        }

        if ($action == "display") {
            $result = $account->getAccounts($user_id);
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "edit") {
            $account_id = intval($_POST['account_id'] ?? 0);
            $account_name = trim($_POST['account_name'] ?? '');
            $account_type = trim($_POST['account_type'] ?? '');
            $balance = floatval($_POST['balance'] ?? 0);

            if ($account_id <= 0 || empty($account_name) || empty($account_type)) {
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            if (strlen($account_name) > 100 || strlen($account_type) > 100) {
                echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
                exit;
            }

            if ($balance < 0) {
                echo json_encode(["status" => "error", "message" => "Balance cannot be negative"]);
                exit;
            }

            $existing = $account->getAccountById($account_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Account not found or access denied"]);
                exit;
            }

            $result = $account->updateAccount($account_id, $account_name, $account_type, $balance);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Account updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update account"]);
            }
            exit;
        }

        if ($action == "delete") {
            $account_id = intval($_POST['account_id'] ?? 0);

            if ($account_id <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid account ID"]);
                exit;
            }

            $existing = $account->getAccountById($account_id);
            if (!$existing || $existing['user_id'] != $user_id) {
                echo json_encode(["status" => "error", "message" => "Account not found or access denied"]);
                exit;
            }

            $result = $account->deleteAccount($account_id);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Account deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete account"]);
            }
            exit;
        }

        if ($action == "search") {
            $keyword = trim($_POST['keyword'] ?? '');

            if (empty($keyword)) {
                $result = $account->getAccounts($user_id);
            } else {
                $result = $account->searchAccounts($user_id, $keyword);
            }
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

    }

    // ADVISORS
    if ($module == "advisors") {

        require_once '../classes/Advisor.php';
        require_once '../classes/Rating.php';

        if ($action == "display_advisors") {

            $adv = new Advisor();
            $rate = new Rating();
            $result = $adv->getAdvisors();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $row['avg_rating'] = $rate->getAverageRating($row['user_id']);
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "search_advisors") {
            $keyword = trim($_POST['keyword'] ?? '');

            $adv = new Advisor();
            $rate = new Rating();

            if (empty($keyword)) {
                $result = $adv->getAdvisors();
            } else {
                $result = $adv->searchAdvisors($keyword);
            }
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $row['avg_rating'] = $rate->getAverageRating($row['user_id']);
                $data[] = $row;
            }

            echo json_encode(["status" => "success", "data" => $data]);
            exit;
        }

        if ($action == "get_advisor_profile") {
            $advisor_id = intval($_POST['advisor_id'] ?? 0);

            if ($advisor_id <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
                exit;
            }

            $adv = new Advisor();
            $rate = new Rating();
            $result = $adv->getAdvisorById($advisor_id);

            if ($result) {
                $result['avg_rating'] = $rate->getAverageRating($advisor_id);
                echo json_encode(["status" => "success", "data" => $result]);
            } else {
                echo json_encode(["status" => "error", "message" => "Advisor not found"]);
            }
            exit;
        }

    }

    echo json_encode(["status" => "error", "message" => "Invalid module"]);
    exit;
}

?>
