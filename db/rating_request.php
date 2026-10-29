<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/Rating.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);

    $rating = new Rating();

    if ($action == "add") {
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

        $rate = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rate < 1 || $rate > 5) {
            echo json_encode(["status" => "error", "message" => "Rating must be between 1 and 5"]);
            exit;
        }

        if (strlen($comment) > 500) {
            echo json_encode(["status" => "error", "message" => "Comment is too long (max 500 characters)"]);
            exit;
        }

        $result = $rating->addRating($user_id, $advisor_id, $rate, $comment);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Rating submitted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to submit rating"]);
        }
        exit;
    }

    if ($action == "display_by_user") {

        $result = $rating->getRatingsByUser($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "display_by_advisor") {
        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $result = $rating->getRatingsByAdvisor($advisor_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view") {
        $rating_id = intval($_POST['rating_id'] ?? 0);

        if ($rating_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid rating ID"]);
            exit;
        }

        $result = $rating->getRatingById($rating_id);

        if (!$result) {
            echo json_encode(["status" => "error", "message" => "Rating not found"]);
            exit;
        }

        if (intval($result['user_id']) != $user_id && intval($result['advisor_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Access denied"]);
            exit;
        }

        echo json_encode(["status" => "success", "data" => $result]);
        exit;
    }

    if ($action == "update") {
        $rating_id = intval($_POST['rating_id'] ?? 0);
        $rate = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid rating ID"]);
            exit;
        }

        $existing = $rating->getRatingById($rating_id);

        if (!$existing || intval($existing['user_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Rating not found or access denied"]);
            exit;
        }

        if ($rate < 1 || $rate > 5) {
            echo json_encode(["status" => "error", "message" => "Rating must be between 1 and 5"]);
            exit;
        }

        if (strlen($comment) > 500) {
            echo json_encode(["status" => "error", "message" => "Comment is too long (max 500 characters)"]);
            exit;
        }

        $result = $rating->updateRating($rating_id, $rate, $comment);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Rating updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update rating"]);
        }
        exit;
    }

    if ($action == "delete") {
        $rating_id = intval($_POST['rating_id'] ?? 0);

        if ($rating_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid rating ID"]);
            exit;
        }

        $existing = $rating->getRatingById($rating_id);

        if (!$existing || intval($existing['user_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Rating not found or access denied"]);
            exit;
        }

        $result = $rating->deleteRating($rating_id, $user_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Rating deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete rating"]);
        }
        exit;
    }

    if ($action == "average") {
        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $result = $rating->getAverageRating($advisor_id);

        echo json_encode(["status" => "success", "average" => $result]);
        exit;
    }

    if ($action == "search") {
        $advisor_id = intval($_POST['advisor_id'] ?? 0);
        $keyword = trim($_POST['keyword'] ?? '');

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        if (empty($keyword)) {
            $result = $rating->getRatingsByAdvisor($advisor_id);
        } else {
            $result = $rating->searchRatings($advisor_id, $keyword);
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
