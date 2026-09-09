<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLoginPage() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// يقبل تسجيل الدخول إما بجلسة المتصفح (الموقع) أو بـ Bearer token (تطبيق الموبايل)
function requireLoginApi($pdo) {
    if (!empty($_SESSION['user_id'])) {
        return; // مسجل دخول من المتصفح
    }

    $token = '';

    // 1) جرّب من ترويسة Authorization (لو السيرفر بيمررها)
    $authHeader = '';
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }
    if ($authHeader && preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
        $token = trim($m[1]);
    }

    // 2) وإلا، جرّب من رابط الطلب نفسه (?token=...) - أضمن ومش بيتأثر
    //    بإعدادات السيرفر
    if ($token === '' && !empty($_GET['token'])) {
        $token = trim($_GET['token']);
    }

    if ($token !== '') {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE api_token = ? LIMIT 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            return;
        }
    }

    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'انتهت الجلسة، سجّل الدخول تاني']);
    exit;
}