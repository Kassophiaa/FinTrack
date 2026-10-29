<?php

require_once __DIR__ . '/api_auth.php';

require_once '../classes/Advice.php';
require_once '../classes/Rating.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);

    // DISPLAY ADVICE
    if ($action == "display_advice") {

        $advice = new Advice();
        $result = $advice->getAdviceByUser($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // VIEW ADVICE
    if ($action == "view_advice") {
        $advice_id = intval($_POST['advice_id'] ?? 0);

        if ($advice_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advice ID"]);
            exit;
        }

        $advice = new Advice();
        $result = $advice->getAdviceById($advice_id);

        if (!$result || $result['user_id'] != $user_id) {
            echo json_encode(["status" => "error", "message" => "Advice not found or access denied"]);
            exit;
        }

        echo json_encode(["status" => "success", "data" => $result]);
        exit;
    }

    // SEARCH ADVICE
    if ($action == "search_advice") {
        $keyword = trim($_POST['keyword'] ?? '');

        $advice = new Advice();

        if (empty($keyword)) {
            $result = $advice->getAdviceByUser($user_id);
        } else {
            $result = $advice->searchAdviceByUser($user_id, $keyword);
        }
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // RATE ADVICE
    if ($action == "rate_advice") {
        require_once '../classes/AdvisorPermission.php';
        $perm = new AdvisorPermission();
        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        if (!$perm->canRate($user_id, $advisor_id)) {
            echo json_encode(["status" => "error", "message" => "You need to grant rating permission to this advisor first"]);
            exit;
        }

        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            echo json_encode(["status" => "error", "message" => "Rating must be between 1 and 5"]);
            exit;
        }

        if (strlen($comment) > 500) {
            echo json_encode(["status" => "error", "message" => "Comment is too long (max 500 characters)"]);
            exit;
        }

        $rate = new Rating();
        $result = $rate->addRating($user_id, $advisor_id, $rating, $comment);

        if ($result) {
            require_once '../classes/Notification.php';
            (new Notification())->addNotification($advisor_id, 'New rating', ($_SESSION['full_name'] ?? 'A client') . " rated you $rating out of 5.");
            echo json_encode(["status" => "success", "message" => "Rating submitted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to submit rating"]);
        }
        exit;
    }

    // DISPLAY RATINGS
    if ($action == "display_ratings") {

        $rate = new Rating();
        $result = $rate->getRatingsByUser($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // DELETE RATING
    if ($action == "delete_rating") {
        $rating_id = intval($_POST['rating_id'] ?? 0);

        if ($rating_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid rating ID"]);
            exit;
        }

        require_once '../classes/Rating.php';
        $rate = new Rating();
        $result = $rate->deleteRating($rating_id, $user_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Rating deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete rating"]);
        }
        exit;
    }

    // DISPLAY ADVICE BY SPECIFIC ADVISOR
    if ($action == "display_advisor_advice") {
        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $advice = new Advice();
        $result = $advice->getAdviceByAdvisor($advisor_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['user_id'] == $user_id) {
                $data[] = $row;
            }
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

?>
