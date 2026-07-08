<?php

function response($status, $message, $data = null, $code = 200) {
    http_response_code($code);
    header("Content-Type: application/json; charset=UTF-8");

    $res = [
        "status" => $status,
        "message" => $message
    ];

    if ($data !== null) {
        $res["data"] = $data;
    }

    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}
?>