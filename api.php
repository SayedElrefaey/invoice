<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/config.php';
require __DIR__ . '/auth.php';

$endpoint = $_GET['endpoint'] ?? '';
$method   = $_SERVER['REQUEST_METHOD'];
$input    = json_decode(file_get_contents('php://input'), true) ?: [];

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// endpoint خاص بتسجيل الدخول من تطبيق الموبايل - متاح من غير تسجيل دخول سابق
if ($endpoint === 'login') {
    if ($method !== 'POST') respond(['error' => 'طريقة غير مسموحة'], 405);
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET api_token=? WHERE id=?")->execute([$token, $user['id']]);
        respond(['token' => $token, 'username' => $user['username']]);
    }
    respond(['error' => 'اسم المستخدم أو كلمة المرور غير صحيحة'], 401);
}

requireLoginApi($pdo);

// تسجيل خروج تطبيق الموبايل - بيلغي الـ token الحالي
if ($endpoint === 'logout_token') {
    if ($method === 'POST') {
        $pdo->prepare("UPDATE users SET api_token=NULL WHERE id=?")->execute([$_SESSION['user_id']]);
        respond(['ok' => true]);
    }
}

switch ($endpoint) {

    case 'sections':
        if ($method === 'GET') {
            $type = $_GET['type'] ?? '';
            if (in_array($type, ['gam3eya','individual'])) {
                $stmt = $pdo->prepare("SELECT * FROM sections WHERE type=? ORDER BY created_at ASC");
                $stmt->execute([$type]);
            } else {
                $stmt = $pdo->query("SELECT * FROM sections ORDER BY created_at ASC");
            }
            respond($stmt->fetchAll());
        }
        if ($method === 'POST') {
            $name = trim($input['name'] ?? '');
            $type = $input['type'] ?? '';
            $hasTurns = !empty($input['hasTurns']) ? 1 : 0;
            if (!$name || !in_array($type, ['gam3eya','individual'])) respond(['error' => 'بيانات غير مكتملة'], 400);
            $stmt = $pdo->prepare("INSERT INTO sections (name, type, has_turns) VALUES (?,?,?)");
            $stmt->execute([$name, $type, $hasTurns]);
            respond(['id' => $pdo->lastInsertId()], 201);
        }
        if ($method === 'PUT') {
            $id   = intval($input['id'] ?? 0);
            $name = trim($input['name'] ?? '');
            if (!$id || !$name) respond(['error' => 'الاسم مطلوب'], 400);
            if (array_key_exists('hasTurns', $input)) {
                $hasTurns = !empty($input['hasTurns']) ? 1 : 0;
                $pdo->prepare("UPDATE sections SET name=?, has_turns=? WHERE id=?")->execute([$name, $hasTurns, $id]);
            } else {
                $pdo->prepare("UPDATE sections SET name=? WHERE id=?")->execute([$name, $id]);
            }
            respond(['ok' => true]);
        }
        if ($method === 'DELETE') {
            $id = intval($_GET['id'] ?? 0);
            $pdo->prepare("DELETE FROM sections WHERE id=?")->execute([$id]);
            respond(['ok' => true]);
        }
        break;

    case 'gam3eyas':
        if ($method === 'GET') {
            $rows = $pdo->query("SELECT * FROM gam3eyas ORDER BY created_at DESC")->fetchAll();
            foreach ($rows as &$g) {
                $s = $pdo->prepare("SELECT * FROM gam3eya_schedule WHERE gam3eya_id=? ORDER BY month_idx ASC");
                $s->execute([$g['id']]);
                $g['schedule'] = $s->fetchAll();

                $t = $pdo->prepare("SELECT month_idx FROM gam3eya_turns WHERE gam3eya_id=? ORDER BY month_idx ASC");
                $t->execute([$g['id']]);
                $g['my_turns'] = array_map('intval', array_column($t->fetchAll(), 'month_idx'));
            }
            respond($rows);
        }
        if ($method === 'POST') {
            $sectionId = intval($input['sectionId'] ?? 0);
            $name     = trim($input['name'] ?? '');
            $start    = $input['startDate'] ?? '';
            $months   = intval($input['months'] ?? 0);
            $amount   = floatval($input['monthlyAmount'] ?? 0);
            $currency = in_array($input['currency'] ?? '', ['EGP','USD']) ? $input['currency'] : 'EGP';
            $myTurns  = is_array($input['myTurnMonths'] ?? null) ? array_values(array_unique(array_map('intval', $input['myTurnMonths']))) : [];
            if (!$sectionId || !$name || !$start || $months < 1 || $amount <= 0) respond(['error' => 'بيانات غير مكتملة'], 400);
            foreach ($myTurns as $mt) {
                if ($mt < 1 || $mt > $months) respond(['error' => 'رقم الشهر غير صحيح'], 400);
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO gam3eyas (section_id, name, start_date, months, monthly_amount, currency) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$sectionId, $name, $start, $months, $amount, $currency]);
            $gid = $pdo->lastInsertId();

            $ins = $pdo->prepare("INSERT INTO gam3eya_schedule (gam3eya_id, month_idx, due_date, amount, paid) VALUES (?,?,?,?,0)");
            $startDT = new DateTime($start);
            $day = (int)$startDT->format('d');
            $baseYear = (int)$startDT->format('Y');
            $baseMonth = (int)$startDT->format('m');
            for ($i = 0; $i < $months; $i++) {
                $targetMonthTotal = $baseMonth + $i;
                $y = $baseYear + intdiv($targetMonthTotal - 1, 12);
                $m = (($targetMonthTotal - 1) % 12) + 1;
                $lastDay = (int)date('t', mktime(0, 0, 0, $m, 1, $y));
                $d = min($day, $lastDay);
                $dateStr = sprintf('%04d-%02d-%02d', $y, $m, $d);
                $ins->execute([$gid, $i + 1, $dateStr, $amount]);
            }
            if ($myTurns) {
                $turnIns = $pdo->prepare("INSERT INTO gam3eya_turns (gam3eya_id, month_idx) VALUES (?,?)");
                foreach ($myTurns as $mt) {
                    $turnIns->execute([$gid, $mt]);
                }
            }
            $pdo->commit();
            respond(['id' => $gid], 201);
        }
        if ($method === 'DELETE') {
            $id = intval($_GET['id'] ?? 0);
            $pdo->prepare("DELETE FROM gam3eyas WHERE id=?")->execute([$id]);
            respond(['ok' => true]);
        }
        if ($method === 'PUT') {
            $id   = intval($input['id'] ?? 0);
            $name = trim($input['name'] ?? '');
            if (!$id || !$name) respond(['error' => 'الاسم مطلوب'], 400);
            $pdo->prepare("UPDATE gam3eyas SET name=? WHERE id=?")->execute([$name, $id]);
            respond(['ok' => true]);
        }
        break;

    case 'toggle_paid':
        if ($method === 'POST') {
            $id = intval($input['id'] ?? 0);
            $pdo->prepare("UPDATE gam3eya_schedule SET paid = NOT paid WHERE id=?")->execute([$id]);
            respond(['ok' => true]);
        }
        break;

    case 'set_my_turns':
        if ($method === 'POST') {
            $gid = intval($input['gam3eyaId'] ?? 0);
            $months = is_array($input['months'] ?? null) ? array_values(array_unique(array_map('intval', $input['months']))) : [];
            if (!$gid) respond(['error' => 'بيانات غير صحيحة'], 400);

            $chk = $pdo->prepare("SELECT months FROM gam3eyas WHERE id=?");
            $chk->execute([$gid]);
            $g = $chk->fetch();
            if (!$g) respond(['error' => 'الجمعية غير موجودة'], 404);
            foreach ($months as $mt) {
                if ($mt < 1 || $mt > (int)$g['months']) respond(['error' => 'رقم الشهر غير صحيح'], 400);
            }

            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM gam3eya_turns WHERE gam3eya_id=?")->execute([$gid]);
            if ($months) {
                $ins = $pdo->prepare("INSERT INTO gam3eya_turns (gam3eya_id, month_idx) VALUES (?,?)");
                foreach ($months as $mt) {
                    $ins->execute([$gid, $mt]);
                }
            }
            $pdo->commit();
            respond(['ok' => true]);
        }
        break;

    case 'individuals':
        if ($method === 'GET') {
            $rows = $pdo->query("SELECT * FROM individuals ORDER BY created_at DESC")->fetchAll();
            foreach ($rows as &$p) {
                $s = $pdo->prepare("SELECT * FROM individual_entries WHERE individual_id=? ORDER BY entry_date ASC, id ASC");
                $s->execute([$p['id']]);
                $p['entries'] = $s->fetchAll();
            }
            respond($rows);
        }
        if ($method === 'POST') {
            $sectionId = intval($input['sectionId'] ?? 0);
            $name     = trim($input['name'] ?? '');
            $phone    = trim($input['phone'] ?? '');
            $currency = in_array($input['currency'] ?? '', ['EGP','USD']) ? $input['currency'] : 'EGP';
            if (!$sectionId || !$name) respond(['error' => 'بيانات غير مكتملة'], 400);
            $stmt = $pdo->prepare("INSERT INTO individuals (section_id, name, phone, currency) VALUES (?,?,?,?)");
            $stmt->execute([$sectionId, $name, $phone, $currency]);
            respond(['id' => $pdo->lastInsertId()], 201);
        }
        if ($method === 'DELETE') {
            $id = intval($_GET['id'] ?? 0);
            $pdo->prepare("DELETE FROM individuals WHERE id=?")->execute([$id]);
            respond(['ok' => true]);
        }
        if ($method === 'PUT') {
            $id       = intval($input['id'] ?? 0);
            $name     = trim($input['name'] ?? '');
            $phone    = trim($input['phone'] ?? '');
            $currency = in_array($input['currency'] ?? '', ['EGP','USD']) ? $input['currency'] : 'EGP';
            if (!$id || !$name) respond(['error' => 'الاسم مطلوب'], 400);
            $pdo->prepare("UPDATE individuals SET name=?, phone=?, currency=? WHERE id=?")->execute([$name, $phone, $currency, $id]);
            respond(['ok' => true]);
        }
        break;

    case 'entries':
        if ($method === 'POST') {
            $individualId = intval($input['individualId'] ?? 0);
            $note   = trim($input['note'] ?? '');
            $amount = floatval($input['amount'] ?? 0);
            $type   = ($input['type'] ?? '') === 'credit' ? 'credit' : 'debit';
            $date   = $input['date'] ?? date('Y-m-d');
            if (!$individualId || $amount <= 0) respond(['error' => 'بيانات غير صحيحة'], 400);
            $stmt = $pdo->prepare("INSERT INTO individual_entries (individual_id, note, amount, type, entry_date) VALUES (?,?,?,?,?)");
            $stmt->execute([$individualId, $note, $amount, $type, $date]);
            respond(['id' => $pdo->lastInsertId()], 201);
        }
        if ($method === 'PUT') {
            $id     = intval($input['id'] ?? 0);
            $note   = trim($input['note'] ?? '');
            $amount = floatval($input['amount'] ?? 0);
            $type   = ($input['type'] ?? '') === 'credit' ? 'credit' : 'debit';
            $date   = $input['date'] ?? date('Y-m-d');
            if (!$id || $amount <= 0) respond(['error' => 'بيانات غير صحيحة'], 400);
            $stmt = $pdo->prepare("UPDATE individual_entries SET note=?, amount=?, type=?, entry_date=? WHERE id=?");
            $stmt->execute([$note, $amount, $type, $date, $id]);
            respond(['ok' => true]);
        }
        if ($method === 'DELETE') {
            $id = intval($_GET['id'] ?? 0);
            $pdo->prepare("DELETE FROM individual_entries WHERE id=?")->execute([$id]);
            respond(['ok' => true]);
        }
        break;

    default:
        respond(['error' => 'مسار غير معروف'], 404);
}
