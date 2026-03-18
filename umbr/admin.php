<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if($action == 'delete_program') {
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Программа удалена';
    } elseif($action == 'delete_team') {
        $stmt = $pdo->prepare("DELETE FROM team WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Сотрудник удален';
    }
    
    header('Location: admin.php');
    exit;
}

$programs = getPrograms($pdo, false);
$team = getTeam($pdo, false);
$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель SpaGold</title>
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
        
        .admin-header {
            background: #1a2a3a;
            color: white;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            font-size: 24px;
        }
        
        .admin-header h1 span {
            color: #c5a572;
        }
        
        .logout-btn {
            background: #c5a572;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .admin-content {
            padding: 40px 0;
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-radius: 5px 5px 0 0;
        }
        
        .tab-btn.active {
            background: #1a2a3a;
            color: white;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .add-btn {
            background: #c5a572;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        th {
            background: #1a2a3a;
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .edit-btn {
            background: #1a2a3a;
            color: white;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .settings-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .settings-card h3 {
            color: #1a2a3a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c5a572;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .save-btn {
            background: #c5a572;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-container">
            <div class="admin-header-content">
                <h1>SpaGold <span>Life</span> Админ-панель</h1>
                <a href="admin_logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
    </header>
    
    <div class="admin-content">
        <div class="admin-container">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('programs')">Программы</button>
                <button class="tab-btn" onclick="showTab('team')">Команда</button>
                <button class="tab-btn" onclick="showTab('settings')">Настройки</button>
            </div>
            
            <div id="programs" class="tab-content active">
                <a href="admin_edit_program.php" class="add-btn">+ Добавить программу</a>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Длительность</th>
                            <th>Цена</th>
                            <th>Старая цена</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($programs as $program): ?>
                        <tr>
                            <td><?php echo $program['id']; ?></td>
                            <td><?php echo htmlspecialchars($program['title']); ?></td>
                            <td><?php echo htmlspecialchars($program['duration']); ?></td>
                            <td><?php echo number_format($program['price'], 0, '', ' '); ?> ₽</td>
                            <td><?php echo $program['old_price'] ? number_format($program['old_price'], 0, '', ' ') . ' ₽' : '-'; ?></td>
                            <td><?php echo $program['is_active'] ? 'Активна' : 'Не активна'; ?></td>
                            <td class="actions">
                                <a href="admin_edit_program.php?id=<?php echo $program['id']; ?>" class="edit-btn">✏️</a>
                                <a href="admin.php?action=delete_program&id=<?php echo $program['id']; ?>" class="delete-btn" onclick="return confirm('Удалить программу?')">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="team" class="tab-content">
                <a href="admin_edit_team.php" class="add-btn">+ Добавить сотрудника</a>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Имя</th>
                            <th>Должность</th>
                            <th>Порядок</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($team as $member): ?>
                        <tr>
                            <td><?php echo $member['id']; ?></td>
                            <td>
                                <?php if($member['photo']): ?>
                                    <img src="uploads/<?php echo $member['photo']; ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    Нет фото
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo htmlspecialchars($member['position']); ?></td>
                            <td><?php echo $member['order_num']; ?></td>
                            <td><?php echo $member['is_active'] ? 'Активен' : 'Не активен'; ?></td>
                            <td class="actions">
                                <a href="admin_edit_team.php?id=<?php echo $member['id']; ?>" class="edit-btn">✏️</a>
                                <a href="admin.php?action=delete_team&id=<?php echo $member['id']; ?>" class="delete-btn" onclick="return confirm('Удалить сотрудника?')">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="settings" class="tab-content">
                <form method="POST" action="admin_save_settings.php">
                    <div class="settings-grid">
                        <div class="settings-card">
                            <h3>Основные настройки</h3>
                            <div class="form-group">
                                <label>Название сайта</label>
                                <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Телефон</label>
                                <input type="text" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h3>Контакты</h3>
                            <div class="form-group">
                                <label>Адрес</label>
                                <input type="text" name="site_address" value="<?php echo htmlspecialchars($settings['site_address'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Часы работы</label>
                                <input type="text" name="working_hours" value="<?php echo htmlspecialchars($settings['working_hours'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="save-btn">Сохранить настройки</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            
            event.target.classList.add('active');
        }
    </script>
</body>
</html>