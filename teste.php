<?php
include 'config.php';

function pingHost($host) {
    exec("ping -c 1 -W 2 $host", $output, $status);
    return $status === 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitor de Rede</title>
</head>
<body>

<h1>Status dos Hosts</h1>

<table border="1" cellpadding="10">
<tr>
    <th>Nome</th>
    <th>IP</th>
    <th>Status</th>
</tr>

<?php foreach ($hosts as $name => $ip): ?>
<tr>
    <td><?php echo $name; ?></td>
    <td><?php echo $ip; ?></td>
    <td style="color: <?php echo pingHost($ip) ? 'green' : 'red'; ?>">
        <?php echo pingHost($ip) ? 'ONLINE' : 'OFFLINE'; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>