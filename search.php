<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты поиска</title>
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Результаты поиска</h1>
        <a href="index.php" class="btn btn-primary mb-3">Назад к поиску</a>
        <?php
            // Получение параметра поиска
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // Подготовка запроса к базе данных
            $stmt = $dbh->prepare("SELECT * FROM posts WHERE body LIKE :search");
            $stmt->execute(['search' => "%$search%"]);
            $results = $stmt->fetchAll();

            if (count($results) > 0) {
                echo "<div class='mt-3'>";
                echo "<h2>Найденные записи:</h2>";
                echo "<ul>";
                foreach ($results as $result) {
                    echo "<li><b>{$result['title']}:</b><br>{$result['body']}</li>";
                }
                echo "</ul>";
                echo "</div>";
            } else {
                echo "<p class='mt-3'>Записи по указанному комментарию не найдены.</p>";
            }
        ?>
    </div>

</body>
</html>