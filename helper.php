<?php

/////////////////////////////////////////
////Helper by Alexander (Syneation)/////
///////use php && pdo//////////////////
///////////////////////////////////////

session_start();

include "/php/db/db.php";

//redirect (use header)
function redirect($path) {
    header("Location: $path");
}

//checking repeated login
function isLoginExists(PDO $pdo, string $login): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = :login");
    $stmt->execute([':login' => $login]);
    $count = $stmt->fetchColumn();

    return ($count > 0);
}

//error
function setMessage(string $key, string $message): void {
    $_SESSION['message'][$key] = $message;
}

function hasMessage(string $key): bool {
    return isset($_SESSION['message'][$key]);
}

function getMessage(string $key) : string {
    $message = $_SESSION['message'][$key] ?? '';
    unset($_SESSION['message'][$key]);
    return $message;
}

//pdo INSERT
function pdoSet($allowed, &$values, $source = array()) {
    $set = '';
    $values = array();

    if (!$source) $source = &$_POST;

    foreach ($allowed as $field) {
        if (isset($source[$field])) {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
            $values[$field] = $source[$field];
        }
    }

    return substr($set, 0, -2);
    
}

//functions for recursive audio file search
function findAudioFiles() {

    global $pdo;

    $musicList = [];

    $sql = "SELECT id, title, file_path, artist FROM songs";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path'])) {
            $musicList[] =  [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'category' => $row['category'],
                'relative_path' => $row['file_path']
            ];
        }
    }

    return $musicList;
}

//find New Songs
function findNewSongs($limit) {

    global $pdo;

    $musicList = [];

    $sql = "SELECT id, title, file_path, artist, category
            FROM songs
            ORDER BY upload_time DESC 
            LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch((PDO::FETCH_ASSOC))) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path'])) {
            $musicList[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'category' => $row['category'],
                'relative_path' => $row['file_path']
            ];
        }
    }

    return $musicList;
    
}

//find audio for category
function findAudioForCategory($category) {

    global $pdo;

    $musicList = [];

    $sql = "SELECT id, title, file_path, artist FROM songs WHERE category = :category";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path'])) {
            $musicList[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'artist' => $row['artist'],
                'category' => $row['category'],
                'relative_path' => $row['file_path']
            ];
        }
    }

    return $musicList;

}

//find category from base date
function findCategory() {
    global $pdo;

    try {
        $categoryList = [];
        $sql = "SELECT DISTINCT category FROM songs ORDER BY category";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $e) {
        error_log("Ошибка при получении категорий!");
        return [];
    }

}

//functions to delete music from base date and from folder
function deleteMusic($id) {
    
    global $pdo;

    //получаем информацию о файле
    $sql = "SELECT file_path FROM songs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    //Record Check
    if (!$file) return ['success' => false, 'message' => 'Запись не найдена'];

    $filePath = $_SERVER['DOCUMENT_ROOT'] . $file['file_path'];

    //удаляем из бд
    $sql = "DELETE FROM songs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    //удаляем файл из сервера
    if (file_exists($filePath))
        if (!unlink($filePath))
        return ['success' => false, 'message' => 'Не удалось удалить файл'];

    return ['success' => true, 'message' => 'Музыка удалена'];

}

// Function to remove an extension from a file name
function removeExtension($fileName) {
    $extension = ['.mp3', '.wav', '.ogg', 'm4a', 'aac'];
    foreach ($extension as $ext) {
        if (substr($fileName, -strlen($ext)) === $ext) {
            return substr($fileName, 0, -strlen($ext));
        }
    }

    return $fileName;
}

//music search function
function searchSongs($searchQuery) {

    global $pdo;

    $searchTerm = "%$searchQuery%";
    $sql = "SELECT id, title, artist, file_path as relative_path
            FROM songs 
            WHERE title LIKE :search_title OR artist LIKE :search_artist";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_title', $searchTerm, PDO::PARAM_STR);
    $stmt->bindParam(':search_artist', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['relative_path']))
            $results[] = $row;
    }

    return $results;

}


//////////////////////////////////////////////
//////////////Video///////////////////////////
/////////////////////////////////////////////
function findVideo() {

    global $pdo;

    $videosList = [];

    $sql = "SELECT * FROM videos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $row['file_path'])) {
            $videosList[] = [
                'id' => $row['id'],
                'user' => $row['user'],
                'title' => $row['title'],
                'author' => $row['author'],
                'photo' => $row['photo'],
                'description' => $row['description'],
                'file_path' => $row['file_path'],
                'category' => $row['category']
            ];
        }
    }

    return $videosList;

}

function findCategoryVideo() {

    global $pdo;

    try {
        $categoryListVideo = [];
        $sql = "SELECT DISTINCT category FROM videos ORDER BY category";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } catch (PDOException $PDOe) {
        error_log("Ошибка получение категории!");
        return [];
    }

}

function findVideoForCategory($category) {

    global $pdo;

    $videoList = [];

    $sql = "SELECT id, title, author, photo, description, file_path FROM videos WHERE category = :category";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path'])) {
            $videoList[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'photo' => $row['photo'],
                'description' => $row['description'],
                'file_path' => $row['file_path']
            ];
        }
    }

    return $videoList;

}

//delete video
function deleteVideo($id) {

    global $pdo;

    $sql = "SELECT file_path FROM videos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    //Record check
    if (!$file) return ['success' => false, 'message' => 'Видео не найдено!'];

    $filePath = $_SERVER['DOCUMENT_ROOT'] . $file['file_path'];

    //delete from date base
    $sql = "DELETE FROM videos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    //delete file from server
    if (file_exists($filePath))
        if (!unlink($filePath))
            return ['success' => false, 'message' => 'Не удалось удалить файл!'];
        
    return ['success' => true, 'message' => 'Музыка удалена!'];

}

function findNewVideos($limit) {
    global $pdo;

    $videoList = [];

    $sql = "SELECT *
            FROM videos
            ORDER BY upload_time DESC 
            LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch((PDO::FETCH_ASSOC))) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path'])) {
            $videoList[] = [
                'id' => $row['id'],
                'user' => $row['user'],
                'title' => $row['title'],
                'author' => $row['author'],
                'photo' => $row['photo'],
                'description' => $row['description'],
                'file_path' => $row['file_path'],
                'category' => $row['category']
            ];
        }
    }

    return $videoList;
}

// get video ID
function getVideoById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//search video
function searchVideos($searchQuery) {
    global $pdo;

    // Cleaning and checking the search query
    $searchQuery = trim($searchQuery);
    if (empty($searchQuery)) {
        error_log("Пустой поисковый запрос");
        return [];
    }

    // Preparing a search term
    $searchTerm = "%$searchQuery%";
    
    try {
        // Multi-field search sorted by upload date (new at first)
        $sql = "SELECT 
                    id, 
                    user, 
                    title, 
                    author, 
                    photo, 
                    description, 
                    file_path, 
                    category,
                    upload_time
                FROM videos
                WHERE 
                    title LIKE :search_title 
                    OR author LIKE :search_author
                    OR category LIKE :search_category
                ORDER BY upload_time DESC
                LIMIT 50";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':search_title', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search_author', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':search_category', $searchTerm, PDO::PARAM_STR);
        
        error_log("Executing the request: " . $sql);
        error_log("Parameters: " . print_r(['search_title' => $searchTerm, 'search_author' => $searchTerm, 'search_category' => $searchTerm], true));
        
        $stmt->execute();

        $results = [];
        $foundCount = 0;
        $filteredCount = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $foundCount++;
            
            $fileExists = file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['file_path']);
            $photoExists = file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $row['photo']);
            
            if ($fileExists && $photoExists) {
                $results[] = $row;
                $filteredCount++;
            } else {
                error_log("Файл не существует: " . ($fileExists ? "" : $row['file_path']) . 
                          ($photoExists ? "" : ", " . $row['photo']));
            }
        }
        
        error_log("Records found: $fundcount, after filtering $filteredCount");
        
        return $results;
    } catch (PDOException $e) {
        error_log("error searching: " . $e->getMessage());
        return [];
    }
}


///////////////////////////////////////////////
//////////////Comments////////////////////////
/////////////////////////////////////////////
function getComments() {

    global $pdo;

    $commentsList = [];

    $stmt = $pdo->prepare("SELECT * FROM comments ORDER BY upload_time DESC");
    $stmt->execute();

    while ($row = $stmt->fetch((PDO::FETCH_ASSOC))) {
        $commentsList[] = [
            "name" => $row['name'],
            "comment" => $row['comment'],
            "date" => $row['upload_time'] 
        ];
    }

    return $commentsList;

}

?>
