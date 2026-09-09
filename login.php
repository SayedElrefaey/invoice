<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول - دفتر الجمعيات والحسابات</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  :root{
    --cover:#12332A; --cover-2:#0C241D; --gold:#C9962C; --gold-light:#E6B95C;
    --paper:#FBF7EC; --line:#D8CFB0; --danger:#A3402F;
  }
  *{box-sizing:border-box;}
  body{
    margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
    background: linear-gradient(180deg, var(--cover) 0%, var(--cover-2) 100%);
    font-family:'Tajawal', sans-serif; padding:20px;
  }
  .box{
    background:var(--paper); width:100%; max-width:380px; border-radius:16px;
    padding:28px 24px; box-shadow:0 10px 30px rgba(0,0,0,0.25);
  }
  .seal{
    width:52px;height:52px;border-radius:50%; margin:0 auto 14px;
    background: radial-gradient(circle at 35% 30%, var(--gold-light), var(--gold) 60%, #8a6a1c 100%);
    display:flex;align-items:center;justify-content:center;
    font-family:'Amiri', serif; font-weight:700; font-size:22px; color:var(--cover-2);
  }
  h1{ font-family:'Amiri', serif; color:var(--cover); text-align:center; font-size:21px; margin:0 0 22px; }
  .field{ margin-bottom:14px; }
  .field label{ display:block; font-size:13px; font-weight:700; color:var(--cover); margin-bottom:5px; }
  .field input{
    width:100%; padding:11px; border-radius:8px; border:1px solid var(--line);
    font-family:'Tajawal',sans-serif; font-size:14px; background:#fff; color:#000;
  }
  button{
    width:100%; background:var(--cover); color:var(--gold-light); border:none;
    padding:12px; border-radius:8px; font-family:'Tajawal',sans-serif; font-weight:700;
    font-size:15px; cursor:pointer; margin-top:6px;
  }
  button:hover{ background:var(--cover-2); }
  .error{ color:var(--danger); font-size:13px; text-align:center; margin-bottom:12px; }
</style>
</head>
<body>
  <div class="box">
    <div class="seal">ج</div>
    <h1>تسجيل الدخول</h1>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
    <form method="POST">
      <div class="field"><label>اسم المستخدم</label><input type="text" name="username" required autofocus></div>
      <div class="field"><label>كلمة المرور</label><input type="password" name="password" required></div>
      <button type="submit">دخول</button>
    </form>
  </div>
</body>
</html>
