<?php

function getMaterialIconSVG($iconName)
{
    switch ($iconName) {
        case "home":
            return "https://img.icons8.com/material/48/000000/home--v1.png";
            break;
        case "playlist":
            return "img.icons8.com/material-outlined/48/000000/video-playlist.png";
            break;
        case "profile":
            return "img.icons8.com/material-outlined/48/000000/user.png";
            break;
        case "forum":
            return;
            break;
        case "error":
            return "img.icons8.com/material-outlined/48/ff0000/error.png";
            break;
        default:
            return;
            break;
    }
}
