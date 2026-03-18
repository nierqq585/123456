<?php
session_start();

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
    try {
        $stmt = $pdo->query("SELECT * FROM settings");
        $settings = [];
        while($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch(PDOException $e) {
        return [];
    }
}

function getPrograms($pdo, $active_only = true) {
    try {
        $sql = "SELECT * FROM programs";
        if($active_only) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function getTeam($pdo, $active_only = true) {
    try {
        $sql = "SELECT * FROM team";
        if($active_only) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY order_num ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function addAppointment($pdo, $name, $phone, $email = null, $program = null, $message = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO appointments (name, phone, email, program, message) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $phone, $email, $program, $message]);
    } catch(PDOException $e) {
        return false;
    }
}

if(isset($_POST['appointment_submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'] ?? null;
    $program = $_POST['program'] ?? null;
    $message = $_POST['message'] ?? null;
    
    if(addAppointment($pdo, $name, $phone, $email, $program, $message)) {
        $success = "Спасибо! Мы свяжемся с вами в ближайшее время.";
    } else {
        $error = "Произошла ошибка. Пожалуйста, попробуйте позже.";
    }
}

$programs = getPrograms($pdo);
$team = getTeam($pdo);
$settings = getSettings($pdo);

$page = isset($_GET['page']) ? $_GET['page'] : 'main';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_title'] ?? 'SpaGold Life'; ?> - Центр превентивного оздоровления</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo a {
            font-size: 24px;
            font-weight: bold;
            color: #1a2a3a;
            text-decoration: none;
        }
        
        .logo span {
            color: #c5a572;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: #c5a572;
        }
        
        .nav-phone {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 18px;
        }
        
        .page-hero {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .page-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .page-hero p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .content-section {
            padding: 60px 0;
        }
        
        .section-title {
            font-size: 36px;
            margin-bottom: 40px;
            color: #1a2a3a;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #c5a572;
        }
        
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .program-card {
            background: white;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .program-card:hover {
            transform: translateY(-10px);
        }
        
        .program-header {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            color: white;
            padding: 25px;
        }
        
        .program-header h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .program-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .program-content {
            padding: 25px;
        }
        
        .program-content p {
            color: #666;
            margin-bottom: 25px;
            font-size: 15px;
        }
        
        .program-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .price {
            font-size: 22px;
            font-weight: bold;
            color: #1a2a3a;
        }
        
        .btn-outline {
            display: inline-block;
            padding: 8px 20px;
            border: 2px solid #c5a572;
            color: #c5a572;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .btn-outline:hover {
            background: #c5a572;
            color: white;
        }
        
        .appointment-form {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 50px;
            background: #c5a572;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(197, 165, 114, 0.4);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
        }
        
        .admin-panel-link {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: #1a2a3a;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 14px;
            z-index: 999;
        }
        
        .admin-panel-link:hover {
            background: #c5a572;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .team-card {
            text-align: center;
        }
        
        .team-photo {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #c5a572;
        }
        
        .team-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .about-image {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .features-list {
            list-style: none;
            margin-top: 20px;
        }
        
        .features-list li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .features-list li::before {
            content: '✓';
            color: #c5a572;
            font-weight: bold;
            font-size: 20px;
        }
        
        .cookie-banner {
            background: #1a2a3a;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 14px;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .cookie-banner a {
            color: #c5a572;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            .page-hero h1 {
                font-size: 32px;
            }
            
            .about-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <a href="?page=main">SpaGold <span>Life</span></a>
                </div>
                <ul class="nav-links">
                    <li><a href="?page=main" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'main') ? 'active' : ''; ?>">Главная</a></li>
                    <li><a href="?page=programs" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'programs') ? 'active' : ''; ?>">Программы</a></li>
                    <li><a href="?page=about" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'about') ? 'active' : ''; ?>">О центре</a></li>
                    <li><a href="?page=contacts" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'contacts') ? 'active' : ''; ?>">Контакты</a></li>
                    <li><a href="?page=appointment" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'appointment') ? 'active' : ''; ?>">Записаться</a></li>
                </ul>
                <a href="tel:<?php echo $settings['site_phone'] ?? '+79017188899'; ?>" class="nav-phone"><?php echo $settings['site_phone'] ?? '+7 (901) 718 88 99'; ?></a>
            </nav>
        </div>
    </header>

    <a href="#" onclick="openLoginModal()" class="admin-panel-link">🔑 Вход для администратора</a>

    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <h2 style="margin-bottom: 20px; color: #1a2a3a;">Вход в админ-панель</h2>
            
            <?php if(isset($_SESSION['login_error'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="admin_login.php">
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="username" required value="admin">
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required value="admin123">
                </div>
                
                <button type="submit" name="login" class="btn" style="width: 100%;">Войти</button>
            </form>
        </div>
    </div>

    <?php
    switch($page) {
        case 'programs':
            ?>
            <section class="page-hero">
                <div class="container">
                    <h1>Наши программы</h1>
                    <p>Комплексные программы оздоровления от 1 до 21 дня</p>
                </div>
            </section>

            <section class="content-section">
                <div class="container">
                    <h2 class="section-title">Все программы центра</h2>
                    <?php if(empty($programs)): ?>
                        <p style="text-align: center;">Программы временно недоступны</p>
                    <?php else: ?>
                    <div class="programs-grid">
                        <?php foreach($programs as $program): ?>
                        <div class="program-card">
                            <div class="program-header">
                                <h3><?php echo htmlspecialchars($program['title']); ?></h3>
                                <p><?php echo htmlspecialchars($program['duration'] ?? ''); ?></p>
                            </div>
                            <div class="program-content">
                                <p><?php echo htmlspecialchars($program['description'] ?? ''); ?></p>
                                <div class="program-footer">
                                    <span class="price">от <?php echo number_format($program['price'] ?? 0, 0, '', ' '); ?> ₽</span>
                                    <a href="?page=appointment&program=<?php echo urlencode($program['title']); ?>" class="btn-outline">Записаться</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            break;
            
        case 'about':
            ?>
            <section class="page-hero">
                <div class="container">
                    <h1>О центре SpaGold Life</h1>
                    <p>Принципиально новый формат оздоровления в Москве</p>
                </div>
            </section>

            <section class="content-section">
                <div class="container">
                    <div class="about-grid">
                        <div>
                            <h2 class="section-title">Наша философия</h2>
                            <p style="margin-bottom: 20px;">SpaGold Life — это принципиально новый формат оздоровления, реализуемый под эгидой санатория лечебного голодания и детокса SpaGold. Он сочетает в себе научный подход в области СПА-терапии, здорового питания и лечебного голодания.</p>
                            
                            <ul class="features-list">
                                <li>Индивидуальный подход к каждому гостю</li>
                                <li>Медицинское сопровождение программ</li>
                                <li>Авторские методики оздоровления</li>
                                <li>Современное SPA-оборудование</li>
                                <li>Команда профессиональных специалистов</li>
                            </ul>
                        </div>
                        <div class="about-image">
                            <img src="images/spa-center.jpg" alt="Наш центр" onerror="this.style.display='none'; this.parentNode.innerHTML='[Фото центра]';">
                        </div>
                    </div>
                </div>
            </section>

<section class="content-section" style="background: #f5f5f5;">
    <div class="container">
        <h2 class="section-title">Наша команда</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-photo">
                    <img src="images/team1.jpg" alt="Загитов Даниял" onerror="this.src='https://via.placeholder.com/200x200?text=Фото'">
                </div>
                <h3>Загитов Даниял</h3>
                <p>Главный врач, терапевт</p>
            </div>
            
            <div class="team-card">
                <div class="team-photo">
                    <img src="images/team2.jpg" alt="Мосин Оскар" onerror="this.src='https://via.placeholder.com/200x200?text=Фото'">
                </div>
                <h3>Мосин Оскар</h3>
                <p>Врач-диетолог</p>
            </div>
            
            <div class="team-card">
                <div class="team-photo">
                    <img src="images/team3.jpg" alt="Кузнецов Кирилл" onerror="this.src='https://via.placeholder.com/200x200?text=Фото'">
                </div>
                <h3>Кузнецов Кирилл</h3>
                <p>СПА-терапевт</p>
            </div>
            
            <div class="team-card">
                <div class="team-photo">
                    <img src="images/team4.jpg" alt="Ахмадаллин Влад" onerror="this.src='https://via.placeholder.com/200x200?text=Фото'">
                </div>
                <h3>Ахмадаллин Влад</h3>
                <p>Психолог</p>
            </div>
        </div>
    </div>
</section>
            <?php
            break;
            
        case 'contacts':
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>Контакты</h1>
            <p>Свяжитесь с нами любым удобным способом</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="about-grid">
                <div class="contact-info">
                    <h2 class="section-title">Как нас найти</h2>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #1a2a3a; margin-bottom: 10px;">📞 Телефон</h3>
                        <a href="tel:<?php echo $settings['site_phone'] ?? '+79017188899'; ?>" style="font-size: 24px; color: #c5a572; text-decoration: none;"><?php echo $settings['site_phone'] ?? '+7 (901) 718 88 99'; ?></a>
                        <p><?php echo $settings['working_hours'] ?? 'Ежедневно с 9:00 до 21:00'; ?></p>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #1a2a3a; margin-bottom: 10px;">📍 Адрес</h3>
                        <p><?php echo $settings['site_address'] ?? 'г. Уфа, ул. Комсомольская, д. 2'; ?></p>
                        <p><small>Зеленая роща, у дома ВДНХА</small></p>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #1a2a3a; margin-bottom: 10px;">✉️ Email</h3>
                        <p><?php echo $settings['site_email'] ?? 'info@spagold.ru'; ?></p>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #1a2a3a; margin-bottom: 10px;">🕒 Режим работы</h3>
                        <p>Ежедневно: 09:00 - 21:00</p>
                        <p>Без выходных</p>
                    </div>
                </div>
                
                <div class="about-image" style="height: 450px;">
                    <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Aabc123def456xyz&width=100%&height=450&lang=ru_RU&scroll=true"></script>
                    
                    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ae6b6b3f1b5a9b7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c&amp;source=constructor" width="100%" height="450" frameborder="0" style="border-radius: 15px;"></iframe>
                </div>
            </div>
        </div>
    </section>
    <?php
    break;
            
        case 'appointment':
            $selected_program = isset($_GET['program']) ? $_GET['program'] : '';
            ?>
            <section class="page-hero">
                <div class="container">
                    <h1>Запись на программы</h1>
                    <p>Оставьте свои контакты, и мы свяжемся с вами</p>
                </div>
            </section>

            <section class="content-section">
                <div class="container">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="appointment-form">
                        <form method="POST">
                            <div class="form-group">
                                <label>Ваше имя *</label>
                                <input type="text" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Телефон *</label>
                                <input type="tel" name="phone" required placeholder="+7 (___) ___-__-__">
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email">
                            </div>
                            
                            <div class="form-group">
                                <label>Выберите программу</label>
                                <select name="program">
                                    <option value="">-- Выберите программу --</option>
                                    <?php foreach($programs as $program): ?>
                                    <option value="<?php echo htmlspecialchars($program['title']); ?>" <?php echo $selected_program == $program['title'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($program['title']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Сообщение</label>
                                <textarea name="message" placeholder="Ваши пожелания или вопросы"></textarea>
                            </div>
                            
                            <button type="submit" name="appointment_submit" class="btn" style="width: 100%;">Отправить заявку</button>
                        </form>
                    </div>
                </div>
            </section>
            <?php
            break;
            
        default:
            ?>
            <section class="page-hero">
                <div class="container">
                    <h1><?php echo $settings['site_title'] ?? 'SpaGold Life'; ?></h1>
                    <p>Центр превентивного оздоровления в Москве</p>
                    <p style="font-size: 18px; margin-top: 20px;">Комплексные программы со СПА-процедурами сроком от 1 до 21 дня</p>
                    
                    <div style="margin-top: 40px;">
                        <a href="?page=appointment" class="btn">Записаться на программу</a>
                    </div>
                </div>
            </section>

            <section class="content-section">
                <div class="container">
                    <h2 class="section-title">Наши программы</h2>
                    <?php if(empty($programs)): ?>
                        <p style="text-align: center;">Программы временно недоступны</p>
                    <?php else: ?>
                    <div class="programs-grid">
                        <?php 
                        $main_programs = array_slice($programs, 0, 3);
                        foreach($main_programs as $program): 
                        ?>
                        <div class="program-card">
                            <div class="program-header">
                                <h3><?php echo htmlspecialchars($program['title']); ?></h3>
                                <p><?php echo htmlspecialchars($program['duration'] ?? ''); ?></p>
                            </div>
                            <div class="program-content">
                                <p><?php echo htmlspecialchars($program['description'] ?? ''); ?></p>
                                <div class="program-footer">
                                    <span class="price">от <?php echo number_format($program['price'] ?? 0, 0, '', ' '); ?> ₽</span>
                                    <a href="?page=appointment&program=<?php echo urlencode($program['title']); ?>" class="btn-outline">Подробнее</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            break;
    }
    ?>

    <div class="cookie-banner">
        <div class="container">
            Мы используем файлы cookie, чтобы сделать сайт удобнее. Продолжая использовать сайт, вы соглашаетесь. <a href="#">Подробнее</a>
        </div>
    </div>

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }
        
        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            var modal = document.getElementById('loginModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>