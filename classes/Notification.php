<?php

require_once __DIR__ . '/../db/myDB.php';

class Notification {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function addNotification($user_id, $title, $message) {
        $sql = "INSERT INTO notifications (user_id, title, message, is_read, status) VALUES (?, ?, ?, 0, 'Active')";
        return $this->db->insert($sql, [$user_id, $title, $message], "iss");
    }

    public function getNotifications($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? AND status = 'Active' ORDER BY notification_id DESC";
        $this->db->select($sql, [$user_id], "i");
        return $this->db->res;
    }

    public function getNotificationById($notification_id) {
        $sql = "SELECT * FROM notifications WHERE notification_id = ? AND status = 'Active'";
        $this->db->select($sql, [$notification_id], "i");
        return $this->db->res->fetch_assoc();
    }

    public function deleteNotification($notification_id) {
        $sql = "UPDATE notifications SET status = 'Deleted' WHERE notification_id = ?";
        return $this->db->update($sql, [$notification_id], "i");
    }

    public function markAsRead($notification_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
        return $this->db->update($sql, [$notification_id], "i");
    }

    public function markAllAsRead($user_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        return $this->db->update($sql, [$user_id], "i");
    }

    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0 AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    public function searchNotifications($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT * FROM notifications WHERE user_id = ? AND status = 'Active' AND (title LIKE ? OR message LIKE ?) ORDER BY notification_id DESC";
        $this->db->search($sql, [$user_id, $search, $search], "iss");
        return $this->db->res;
    }

}

?>
