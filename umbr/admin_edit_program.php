<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$program = null;

if($id) {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
    $stmt->execute([$id]);
    $program = $stmt->fetch();
}

if(isset($_POST['save'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $old_price = $_POST['old_price'] ?: null;
    $category = $_POST['category'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if($id) {
        $sql = "UPDATE programs SET title=?, description=?, duration=?, price=?, old_price=?, category=?, is_active=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $duration, $price, $old_price, $category, $is_active, $id]);
    } else {
        $sql = "INSERT INTO programs (title, description, duration, price, old_price, category, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $duration, $price, $old_price, $category, $is_active]);
        $id = $pdo->lastInsertId();
    }
    
    $_SESSION['message'] = 'Программа сохранена';
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Редактирование' : 'Добавление'; ?> программы</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .header {
            background: #1a2a3a;
            color: white;
            padding: 20px 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-btn {
            color: white;
            text-decoration: none;
            padding: 5px 15px;
            border: 1px solid white;
            border-radius: 5px;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            margin-top: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input {
            width: auto;
        }
        
        .btn {
            background: #c5a572;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #b3945a;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1><?php echo $id ? 'Редактирование' : 'Добавление'; ?> программы</h1>
                <a href="admin.php" class="back-btn">Назад</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label>Название программы *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($program['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description"><?php echo htmlspecialchars($program['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Длительность (например: "1-14 дней")</label>
                    <input type="text" name="duration" value="<?php echo htmlspecialchars($program['duration'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Цена (руб)</label>
                    <input type="number" name="price" value="<?php echo $program['price'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Старая цена (если есть скидка)</label>
                    <input type="number" name="old_price" value="<?php echo $program['old_price'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Категория</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($program['category'] ?? ''); ?>">
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_active" <?php echo !isset($program) || $program['is_active'] ? 'checked' : ''; ?>>
                    <label>Активна (показывать на сайте)</label>
                </div>
                
                <button type="submit" name="save" class="btn">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>