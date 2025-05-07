<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');
    $age   = intval($_POST['age']);

    // تحقق التكرار
    $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE patient_phone = ?');
    $stmtCheck->execute([$phone]);
    $exists = $stmtCheck->fetchColumn() > 0;

    if ($exists) {
        $message = 'عذراً، هذا الرقم مسجل بالفعل.';
        $type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO appointments (patient_name, patient_phone, patient_age) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $phone, $age]);
            $message = 'تم الحجز بنجاح!';
            $type = 'success';
        } catch (Exception $e) {
            $message = 'فشل في الحجز، حاول مرة أخرى.';
            $type = 'error';
        }
    }

    // عرض صفحة النتائج
    echo "<!DOCTYPE html>\n<html lang='ar' dir='rtl'>\n<head>\n";
    echo "<meta charset='UTF-8'>\n<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
    echo "<title>نتيجة الحجز</title>\n";
    echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css'>\n";
    echo "<link rel='stylesheet' href='style.css'>\n</head>\n<body>\n";
    echo "<div class='confirmation'>\n";
    echo $type === 'success'
         ? "<div class='message-success'>{$message}</div>\n"
         : "<div class='message-error'>{$message}</div>\n";
    if ($type === 'success') {
        echo "<ul>\n<li><strong>الاسم:</strong> {$name}</li>\n<li><strong>رقم التليفون:</strong> {$phone}</li>\n<li><strong>السن:</strong> {$age}</li>\n</ul>\n";
    }
    echo "<p style='margin-top:16px;'>سيتواصل معك فريق الدكتور غدية قريباً.</p>\n";
    echo "</div>\n</body>\n</html>";

} else {
    header('Location: index.html'); exit;
}
?>