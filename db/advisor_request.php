<?php

require_once __DIR__ . '/api_auth.php';

require_once '../classes/Advisor.php';
require_once '../classes/Advice.php';
require_once '../classes/AdvisorPermission.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $advisor_id = intval($_SESSION['user_id']);

    if ($_SESSION['role'] != 'advisor') {
        echo json_encode(["status" => "error", "message" => "Access denied"]);
        exit;
    }

    $perm = new AdvisorPermission();

    function filterClientData($client) {
        if (!$client) return null;
        unset($client['password']);
        return $client;
    }

    function verifyAdvisorClientRelationship($perm, $advisor_id, $client_id) {
        $existing = $perm->getPermission($client_id, $advisor_id);
        return $existing !== null;
    }

    // CLIENTS
    if ($action == "display_clients" || $action == "search_clients") {
        $keyword = $action == "search_clients" ? strtolower(trim($_POST['keyword'] ?? '')) : '';
        $data = [];

        $result = $perm->getPermittedClientsForAdvisor($advisor_id);
        while ($result && ($row = $result->fetch_assoc())) {
            if (empty($row['user_id'])) continue;
            if ($keyword !== '') {
                $haystack = strtolower(($row['full_name'] ?? '') . ' ' . ($row['username'] ?? '') . ' ' . ($row['email'] ?? ''));
                if (strpos($haystack, $keyword) === false) continue;
            }
            $data[] = [
                'user_id' => intval($row['user_id']),
                'full_name' => $row['full_name'] ?? '',
                'username' => $row['username'] ?? '',
                'email' => $row['email'] ?? '',
                'since' => $row['created_at'],
                'view_income' => intval($row['view_income']),
                'view_expenses' => intval($row['view_expenses']),
                'view_investments' => intval($row['view_investments']),
                'view_bank_accounts' => intval($row['view_bank_accounts']),
                'can_message' => intval($row['can_message']),
                'can_give_advice' => intval($row['can_give_advice']),
            ];
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view_client") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID"]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client"]);
            exit;
        }

        $advisor = new Advisor();
        $client = $advisor->getClientById($user_id);

        if (!$client) {
            echo json_encode(["status" => "error", "message" => "Client not found"]);
            exit;
        }

        $income = $perm->canAdvisorViewIncome($advisor_id, $user_id) ? $advisor->getClientTotalIncome($user_id) : 0;
        $expense = $perm->canAdvisorViewExpenses($advisor_id, $user_id) ? $advisor->getClientTotalExpense($user_id) : 0;
        $investment = $perm->canAdvisorViewInvestments($advisor_id, $user_id) ? $advisor->getClientTotalInvestment($user_id) : 0;

        $permission = $perm->getPermission($user_id, $advisor_id);

        echo json_encode([
            "status" => "success",
            "client" => filterClientData($client),
            "total_income" => $income,
            "total_expense" => $expense,
            "total_investment" => $investment,
            "permissions" => $permission
        ]);
        exit;
    }

    if ($action == "view_client_income") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID", "data" => []]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client", "data" => []]);
            exit;
        }

        if (!$perm->canAdvisorViewIncome($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "The client has not shared income information with you", "data" => []]);
            exit;
        }

        $advisor = new Advisor();
        $result = $advisor->getClientIncome($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view_client_expense") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID", "data" => []]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client", "data" => []]);
            exit;
        }

        if (!$perm->canAdvisorViewExpenses($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "The client has not shared expense information with you", "data" => []]);
            exit;
        }

        $advisor = new Advisor();
        $result = $advisor->getClientExpense($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view_client_investment") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID", "data" => []]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client", "data" => []]);
            exit;
        }

        if (!$perm->canAdvisorViewInvestments($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "The client has not shared investment information with you", "data" => []]);
            exit;
        }

        $advisor = new Advisor();
        $result = $advisor->getClientInvestment($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view_client_investment_summary") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID"]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client"]);
            exit;
        }

        if (!$perm->canAdvisorViewInvestments($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "The client has not shared investment information with you"]);
            exit;
        }

        $advisor = new Advisor();
        $summary = $advisor->getClientInvestmentSummary($user_id);

        echo json_encode(["status" => "success", "data" => $summary]);
        exit;
    }

    if ($action == "view_client_bank_accounts") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid client ID", "data" => []]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to view this client", "data" => []]);
            exit;
        }

        if (!$perm->canAdvisorViewBankAccounts($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "The client has not shared bank account information with you", "data" => []]);
            exit;
        }

        require_once '../classes/BankAccount.php';
        $bankAccount = new BankAccount();
        $result = $bankAccount->getAccounts($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // ADVICE
    if ($action == "add_advice") {
        $user_id = intval($_POST['user_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($user_id <= 0 || empty($title) || empty($content)) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        if (strlen($title) > 100 || strlen($content) > 2000) {
            echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
            exit;
        }

        if (!verifyAdvisorClientRelationship($perm, $advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "You do not have permission to give advice to this client"]);
            exit;
        }

        if (!$perm->canGiveAdvice($advisor_id, $user_id)) {
            echo json_encode(["status" => "error", "message" => "No permission to give advice to this client"]);
            exit;
        }

        $advice = new Advice();
        $result = $advice->addAdvice($advisor_id, $user_id, $title, $content);

        if ($result) {
            require_once '../classes/Notification.php';
            (new Notification())->addNotification($user_id, 'New advice received', ($_SESSION['full_name'] ?? 'Your advisor') . ' sent you advice: "' . $title . '"');
            echo json_encode(["status" => "success", "message" => "Advice sent successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send advice"]);
        }
        exit;
    }

    if ($action == "display_advice") {

        $advice = new Advice();
        $result = $advice->getAdviceByAdvisor($advisor_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "edit_advice") {
        $advice_id = intval($_POST['advice_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($advice_id <= 0 || empty($title) || empty($content)) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        if (strlen($title) > 100 || strlen($content) > 2000) {
            echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
            exit;
        }

        $advice = new Advice();
        $existing = $advice->getAdviceById($advice_id);

        if (!$existing || $existing['advisor_id'] != $advisor_id) {
            echo json_encode(["status" => "error", "message" => "Advice not found or access denied"]);
            exit;
        }

        $result = $advice->updateAdvice($advice_id, $title, $content);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Advice updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update advice"]);
        }
        exit;
    }

    if ($action == "delete_advice") {
        $advice_id = intval($_POST['advice_id'] ?? 0);

        if ($advice_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advice ID"]);
            exit;
        }

        $advice = new Advice();
        $existing = $advice->getAdviceById($advice_id);

        if (!$existing || $existing['advisor_id'] != $advisor_id) {
            echo json_encode(["status" => "error", "message" => "Advice not found or access denied"]);
            exit;
        }

        $result = $advice->deleteAdvice($advice_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Advice deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete advice"]);
        }
        exit;
    }

    if ($action == "search_advice") {
        $keyword = trim($_POST['keyword'] ?? '');

        $advice = new Advice();

        if (empty($keyword)) {
            $result = $advice->getAdviceByAdvisor($advisor_id);
        } else {
            $result = $advice->searchAdvice($advisor_id, $keyword);
        }
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

?>
