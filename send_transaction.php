<?php
// Dummy example of sending Pepecoin transaction using pepecoin-cli.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $recipient = $_POST['recipient'] ?? '';
    $amount = $_POST['amount'] ?? 0;

    // Validate the inputs
    if (empty($recipient) || !is_numeric($amount) || $amount <= 0) {
        header('Location: index.php?page=send&status=error');
        exit();
    }

    // Actual transaction logic (call pepecoin-cli)
    $transactionResult = sendPepecoinTransaction($recipient, $amount);

    // Redirect to the send page with status based on the result
    if ($transactionResult === true) {
        header('Location: index.php?page=send&status=error');
    } else {
        header('Location: index.php?page=send&status=success');
    }
    exit();
}

function sendPepecoinTransaction($recipient, $amount) {
    // Build the command for pepecoin-cli
    $command = escapeshellcmd("/home/wallet/pepecoin/bin/pepecoin-cli --datadir=/home/wallet/.pepecoin sendtoaddress $recipient $amount");

    // Execute the command and capture the output
    $output = shell_exec($command);

    // Check if the output contains a valid transaction ID (successful transaction)
    if ($output && strlen($output) == 64) { // Assuming a valid transaction ID is 64 characters long
        return true;  // Transaction successful
    }

    // If no valid transaction ID is returned, it means something went wrong
    return false; // Transaction failed
}
?>
