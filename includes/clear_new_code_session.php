<?php
session_start();

unset($_SESSION['show_new_code_modal']);
unset($_SESSION['new_code_value']);
unset($_SESSION['new_code_email']);
unset($_SESSION['new_code_expires']);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Session cleared']);
?>