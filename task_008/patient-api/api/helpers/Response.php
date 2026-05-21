<?php
class Response {
    public static function send($statusCode, $status, $message, $data = []) {
        http_response_code($statusCode);
        echo json_encode([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ]);
        exit();
    }
}
?>
