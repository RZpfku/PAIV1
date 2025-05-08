<?php
$walletPath = '/home/wallet/.pepecoin/wallet.dat';  // Path to the wallet.dat file
$usbMountDir = '/media/wallet/USB/';  // Base path where USB devices are mounted

// Get list of USB devices
$usbDevices = getUsbMounts('/media/wallet/');

// Check USB devices and print all detected USBs for debugging
function getUsbMounts($usbMountDir)
{
    $usbDevices = array_filter(scandir($usbMountDir), function ($dir) use ($usbMountDir) {
        return is_dir($usbMountDir . '/' . $dir) && $dir !== '.' && $dir !== '..';
    });

    return $usbDevices;
}

// Export Wallet function
function exportWallet($usbMountDir)
{
    $usbPath = $usbMountDir;

    // Check if the wallet.dat file exists
    global $walletPath;
    if (file_exists($walletPath)) {
        $backupPath = $usbPath . '/wallet.dat';
        echo "Zálohujem do: " . $backupPath . "<br>";  // Debugging

        // Use sudo to copy the wallet file to the USB
        $command = "sudo cp " . escapeshellarg($walletPath) . " " . escapeshellarg($backupPath);
        $output = shell_exec($command);

        // Check if the backup was successful
        if ($output === null) {
            return "Peňaženka bola úspešne zálohovaná na USB: " . $backupPath;
        } else {
            return "Nebolo možné zálohovať peňaženku: " . $output;
        }
    } else {
        return "Súbor wallet.dat sa nenašiel.";
    }
}

// Import Wallet function
function importWallet($usbMountDir)
{
    // Choose the first detected USB device
    $importFile = $usbMountDir . 'wallet.dat';

    // Check if the wallet.dat exists on the USB
    if (file_exists($importFile)) {
        global $walletPath;

        $commandChmodUSB = "chmod 777 " . escapeshellarg($importFile);
        shell_exec($commandChmodUSB);

        $commandDelOld = "rm " . escapeshellarg($walletPath);
        shell_exec($commandDelOld);

        // Replace the current wallet.dat with the one from the USB
        $commandCopy = "cp " . escapeshellarg($importFile) . " " . escapeshellarg($walletPath);
        shell_exec($commandCopy);

        // Ensure wallet.dat has the correct permissions (777) to avoid permission issues
        $commandChmod = "chmod 777 " . escapeshellarg($walletPath);
        shell_exec($commandChmod);

        // Restart Pepecoin daemon
        $stopCommand = "/home/wallet/pepecoin/bin/pepecoin-cli --datadir=/home/wallet/.pepecoin stop";
        shell_exec($stopCommand);

        sleep(5);  // Wait for the daemon to stop

        // Start Pepecoin daemon
        $startCommand = "/home/wallet/pepecoin/bin/pepecoind --datadir=/home/wallet/.pepecoin";
        shell_exec($startCommand);

        $restartCommand = "sudo reboot";
        shell_exec($restartCommand);
    } else {
        return "Súbor wallet.dat sa nenašiel na USB.";
    }
}

// Initialize the message variable
$message = 'Zariadenie sa reštartuje...';

// Handle POST request to either export or import
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['export'])) {
        $message = exportWallet($usbMountDir);
        // Redirect back to the backup page with the success or error message
        header('Location: index.php?page=backup&message=' . urlencode($message));
        exit;
    } elseif (isset($_POST['import'])) {
        $message = importWallet($usbMountDir);
        // Redirect back to the backup page with the success or error message
        header('Location: index.php?page=backup&message=' . urlencode($message));
        exit;
    }
}
?>

<style>
    body {
        margin-top: 20px;
    }

    .button {
        padding: 10px 20px;
        margin: 10px 0;
        cursor: pointer;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
    }

    .button:hover {
        background-color: #45a049;
    }

    .container {
        max-width: 600px;
        margin: 20px;
        padding: 20px;
        border-radius: 28px;
        background-color: #393939;
        color: white;
    }

    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }

    .success {
        background-color: #4CAF50;
        color: white;
    }

    .error {
        background-color: #f44336;
        color: white;
    }
</style>

<div class="container">
    <h2>Záloha / Obnovenie Peňaženky</h2>
    <p>USB disk musí byť naformátovaný vo FAT32 a pomenovaný "usb"!</p>

    <!-- Display the message if there is one -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message <?php echo (strpos($_GET['message'], 'success') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=backup" method="post">
        <!-- Export Wallet Section -->
        <button class="btn" type="submit" name="export" class="button">Zálohovať Peňaženku</button>
    </form>
    <?php
    if (!file_exists($usbMountDir . 'wallet.dat')) {
        echo "Overte si, či existuje súbor s názvom wallet.dat na USB.";
    } else {
        echo "Záloha peňaženky wallet.dat sa našla.";
    }
    ?>

    <form action="index.php?page=backup" method="post">
        <!-- Import Wallet Section -->
         <p>Po obnovení sa peňaženka reštartuje.</p>
        <button class="btn" type="submit" name="import" class="button">Obnoviť Peňaženku</button>
    </form>
</div>

<div class="logo">
    <img src="/assets/logo.png" alt="Logo">
</div>