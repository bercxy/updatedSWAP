<?php
session_start();

if (
    !isset($_SESSION['user_role']) ||
    strtolower($_SESSION['user_role']) !== 'admin'
) {
    echo "Access denied";
    exit;
}
