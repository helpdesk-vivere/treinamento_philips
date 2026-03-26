<?php
include 'config.php';

function pingHost($host) {
    exec("ping -c 1 -W 2 $host", $output, $status);
    return $status === 0;
}

$status_file = "status.json";

// Carrega status anterior
$last_status = file_exists($status_file) 
    ? json_decode(file_get_contents($status_file), true) 
    : [];

$current_status = [];

foreach ($hosts as $name => $ip) {

    $isUp = pingHost($ip);
    $current_status[$name] = $isUp ? "UP" : "DOWN";

    $last = $last_status[$name] ?? "UNKNOWN";

    // Se mudou → envia alerta
    if ($last !== $current_status[$name]) {

        $subject = "$name - " . $current_status[$name];
        $message = "Host: $name ($ip)\nStatus: " . $current_status[$name];

        mail($email_to, $subject, $message);
    }
}

// Salva status atual
file_put_contents($status_file, json_encode($current_status));

echo "Monitoramento executado\n";
?>