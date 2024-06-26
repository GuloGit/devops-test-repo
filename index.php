<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Поиск записей по комментарию</title>
        <!-- Подключение Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1 class="mt-5">Поиск записей по комментарию</h1>
            <form action="search.php" method="GET" class="mt-3">
                <div class="form-group">
                    <input type="text" id="search" name="search" class="form-control mb-3 w-30" minlength="3" placeholder="Введите текст комментария (минимум 3 символа):" required>
                </div>
                <button type="submit" class="btn btn-primary mb-3">Найти</button>
            </form>
        </div>

        <?php
            try {
                $dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            // Проверка количества записей в таблице posts
            $stmt = $dbh->query("SELECT COUNT(*) FROM posts");
            $post_count = $stmt->fetchColumn();

            // Проверка количества записей в таблице comments
            $stmt = $dbh->query("SELECT COUNT(*) FROM comments");
            $comment_count = $stmt->fetchColumn();

            if ($post_count > 10 || $comment_count > 10) {
                echo "<div class='container'>" .
                    "Данные уже загруженны" .
                    "</div>";
            } else {
                // Загрузка списка записей блога
                $posts_json = file_get_contents('https://jsonplaceholder.typicode.com/posts');
                $posts = json_decode($posts_json, true);

                // Загрузка комментариев
                $comments_json = file_get_contents('https://jsonplaceholder.typicode.com/comments');
                $comments = json_decode($comments_json, true);

                // Загрузка записей в базу данных
                foreach ($posts as $post) {
                    $stmt = $dbh->prepare("INSERT IGNORE INTO posts (id, userId, title, body) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$post['id'], $post['userId'], $post['title'], $post['body']]);
                }

                // Загрузка комментариев в базу данных
                foreach ($comments as $comment) {
                    $stmt = $dbh->prepare("INSERT IGNORE INTO comments (id, postId, name, email, body) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$comment['id'], $comment['postId'], $comment['name'], $comment['email'], $comment['body']]);
                }

                // Вывод сообщения о завершении загрузки
                echo "<div class='container'>" .
                    "Загружено " . count($posts) . " записей и " . count($comments) . " комментариев" .
                    "</div>";
            }
        ?>
    </body>
</html>
