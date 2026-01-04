<?php

// require_once __DIR__ . '/../Models/forumModel.php'; // À créer

/**
 * Affiche l'index du forum (liste des topics)
 */
function showForumIndex($pdo, $userId) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access the forum.";
        header("Location: /");
        exit;
    }
    
    $title = "Forum";
    // $topics = getAllForumTopics($pdo);
    $template = __DIR__ . "/../Views/Pages/forumIndex.php";
    require_once(__DIR__ . "/../Views/base.php");
}

/**
 * Affiche un topic spécifique avec ses messages
 */
function showForumTopic($pdo, $userId) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access the forum.";
        header("Location: /");
        exit;
    }
    
    $topicId = (int) $_GET['topicId'];
    
    if ($topicId <= 0) {
        $_SESSION['error'] = "Invalid topic ID.";
        header("Location: /forum");
        exit;
    }
    
    // $topic = getForumTopicById($pdo, $topicId);
    // $messages = getMessagesByTopicId($pdo, $topicId);
    
    $title = "Forum Topic";
    $template = __DIR__ . "/../Views/Pages/forumTopic.php";
    require_once(__DIR__ . "/../Views/base.php");
}

/**
 * Création d'un nouveau topic
 */
function createForumTopic($pdo, $userId, $method) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to create a topic.";
        header("Location: /");
        exit;
    }
    
    if ($method === 'POST' && isset($_POST['create_topic'])) {
        $title = trim($_POST['topic_title'] ?? '');
        $content = trim($_POST['topic_content'] ?? '');
        
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = "Title and content are required.";
        } else {
            // $topicId = createTopic($pdo, $userId, $title, $content);
            
            // if ($topicId) {
            //     $_SESSION['success'] = "Topic created successfully!";
            //     header("Location: /forum/topic?topicId=" . $topicId);
            //     exit;
            // } else {
            //     $_SESSION['error'] = "Error creating topic.";
            // }
        }
    }
    
    $title = "Create Topic";
    $template = __DIR__ . "/../Views/Pages/forumCreateTopic.php";
    require_once(__DIR__ . "/../Views/base.php");
}