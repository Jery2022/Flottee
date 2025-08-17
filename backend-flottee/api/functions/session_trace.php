<?php 
function traceSessionGlobale() 
{ if (session_status() !== PHP_SESSION_ACTIVE) 
    { 
        session_start(); 
    }

    $logPath = __DIR__ . '/../../logs/session_trace.log'; 
    $sessionData = [ 'timestamp' => date('Y-m-d H:i:s'), 'user_id' => $_SESSION['user_id'] ?? null, 'role' => $_SESSION['role'] ?? null, 'status' => $_SESSION['status'] ?? null, 'first_name' => $_SESSION['first_name'] ?? null, 'session_id' => session_id(), 'page' => basename($_SERVER['SCRIPT_FILENAME']) ];

    error_log(json_encode($sessionData) . " ", 3, $logPath); 
}

traceSessionGlobale(); 
?>