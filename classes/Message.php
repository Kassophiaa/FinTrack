<?php

require_once __DIR__ . '/../db/myDB.php';

class Message {

    private $db;

    public function __construct() {
        $this->db = new myDB();
    }

    public function sendMessage($sender_id, $receiver_id, $message) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, status) VALUES (?, ?, ?, 'Active')";
        return $this->db->insert($sql, [$sender_id, $receiver_id, $message], "iis");
    }

    public function getMessages($user_id) {
        $sql = "SELECT m.*, u.full_name as sender_name FROM messages m LEFT JOIN users u ON m.sender_id = u.user_id WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.status = 'Active' ORDER BY m.created_at DESC";
        $this->db->select($sql, [$user_id, $user_id], "ii");
        return $this->db->res;
    }

    public function getConversation($user_id, $other_user_id) {
        $sql = "SELECT m.*, u.full_name as sender_name FROM messages m LEFT JOIN users u ON m.sender_id = u.user_id WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)) AND m.status = 'Active' ORDER BY m.created_at ASC";
        $this->db->select($sql, [$user_id, $other_user_id, $other_user_id, $user_id], "iiii");
        return $this->db->res;
    }

    public function getMessageById($message_id) {
        $sql = "SELECT * FROM messages WHERE message_id = ? AND status = 'Active'";
        $this->db->select($sql, [$message_id], "i");
        return $this->db->res->fetch_assoc();
    }

    // public function deleteMessage($message_id) {
    //     $sql = "UPDATE messages SET status = 'Deleted' WHERE message_id = ?";
    //     return $this->db->update($sql, [$message_id], "i");
    // }

    public function searchMessages($user_id, $keyword) {
        $search = "%" . $keyword . "%";
        $sql = "SELECT m.*, u.full_name as sender_name FROM messages m LEFT JOIN users u ON m.sender_id = u.user_id WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.status = 'Active' AND m.message LIKE ? ORDER BY m.created_at DESC";
        $this->db->search($sql, [$user_id, $user_id, $search], "iis");
        return $this->db->res;
    }

    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as total FROM messages WHERE receiver_id = ? AND is_read = 0 AND status = 'Active'";
        $this->db->select($sql, [$user_id], "i");
        $row = $this->db->res->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }

    // Everyone this user has exchanged messages with
    public function getConversationPartners($user_id) {
        $sql = "SELECT DISTINCT u.user_id, u.full_name FROM messages m INNER JOIN users u ON u.user_id = IF(m.sender_id = ?, m.receiver_id, m.sender_id) WHERE (m.sender_id = ? OR m.receiver_id = ?) AND m.status = 'Active' ORDER BY u.full_name ASC";
        $this->db->select($sql, [$user_id, $user_id, $user_id], "iii");
        $rows = [];
        while ($row = $this->db->res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Unread message counts grouped by sender: [sender_id => count]
    public function getUnreadCountsBySender($user_id) {
        $sql = "SELECT sender_id, COUNT(*) as total FROM messages WHERE receiver_id = ? AND is_read = 0 AND status = 'Active' GROUP BY sender_id";
        $this->db->select($sql, [$user_id], "i");
        $counts = [];
        while ($row = $this->db->res->fetch_assoc()) {
            $counts[intval($row['sender_id'])] = intval($row['total']);
        }
        return $counts;
    }

    public function markAsRead($sender_id, $receiver_id) {
        $sql = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        return $this->db->update($sql, [$sender_id, $receiver_id], "ii");
    }

}

?>
