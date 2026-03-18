<?php
$host = 'MySQL-8.2';
$dbname = 'spa_salon';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

function getSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getPrograms($pdo, $active_only = true) {
    $sql = "SELECT * FROM programs";
    if($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY created_at DESC";
    return $pdo->query($sql)->fetchAll();
}

function getTeam($pdo, $active_only = true) {
    $sql = "SELECT * FROM team";
    if($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY order_num ASC";
    return $pdo->query($sql)->fetchAll();
}
?>