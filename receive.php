<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to execute Pepecoin CLI command
function getPepecoinData($command)
{
    $output = shell_exec("/home/wallet/pepecoin/bin/pepecoin-cli --datadir=/home/wallet/.pepecoin $command 2>&1");

    // Log the output for debugging
    error_log("Pepecoin CLI Output: " . $output);

    $output = trim($output); // Trim to remove any extra spaces

    if (empty($output)) {
        return 'Error: No address available'; // Return an error message if no output
    }

    return $output; // Return the output (should be the address)
}

// Fetch wallet address from Pepecoin Core
$address = getPepecoinData('getnewaddress');
if (is_array($address)) {
    echo "Address retrieved: " . implode(", ", $address);
} else {
}

// If the address is valid, proceed with QR code generation
$address = $address ?? 'No address available';

// Include the PHP QR Code library
include('/home/wallet/wallet/phpqrcode/qrlib.php');

// Generate QR code for the wallet address
$qrCodeFile = 'qrcode.png'; // Path to save the QR code image
QRcode::png($address, $qrCodeFile, QR_ECLEVEL_L, 10); // Generates QR code image

?>
<h3>Prijať Pepecoin</h3>
<p>Pre prijatie Pepecoinu zazdieľajte vašu adresu:</p>

<!-- Display Wallet Address -->
<div class="address">
    <p><strong><?php echo $address; ?></strong></p>
</div>

<!-- Display QR Code -->
<div class="qr-code">
    <img src="<?php echo $qrCodeFile; ?>" alt="QR Code">
</div>

<!-- Display status or errors if any -->
<?php if (isset($_GET['status'])): ?>
    <div class="status">
        <?php
        if ($_GET['status'] == 'success') {
            echo "<p style='color: green;'>Adresa bola úspešne skopírovaná!</p>";
        } else if ($_GET['status'] == 'error') {
            echo "<p style='color: red;'>Nastala chyba. Nebolo možné načítať adresu.</p>";
        }
        ?>
    </div>
<?php endif; ?>
<div class="logo">
    <img src="/assets/logo.png" alt="Logo">
</div>