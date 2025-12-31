<?php

function setThemeFromPost()
{
    if (isset($_POST['theme'])) {
        $_SESSION['theme'] = $_POST['theme'];
    }
}

function getCurrentTheme(): string
{
    return $_SESSION['theme'] ?? 'system';
}