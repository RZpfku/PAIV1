<?php
header('Content-Type: application/json');

// Function to execute Pepecoin CLI command
function getPepecoinData($command) {
    $output = shell_exec("/home/wallet/pepecoin/bin/pepecoin-cli --datadir=/home/wallet/.pepecoin $command");
    return json_decode($output, true);
}

// Fetch wallet balance
$balance = getPepecoinData('getbalance');

// Fetch recent transactions
$transactions = getPepecoinData('listtransactions "*" 5');

// Check if transactions exist and are an array
if (!is_array($transactions) || empty($transactions)) {
    $transactions = []; // Set empty array if there are no transactions
}

// Fetch blockchain sync status
$blockCount = getPepecoinData('getblockcount');
$blockCount = (int) $blockCount;  // Ensure it's treated as an integer

// Fetch block count from external API
$externalBlockCount = file_get_contents('https://pepeblocks.com/api/getblockcount');
$externalBlockCount = (int) $externalBlockCount;  // Ensure it's treated as an integer

// Debugging outputs to ensure block counts are correct
if ($blockCount === null) {
    echo json_encode(["error" => "Failed to fetch local block count."]);
    exit();
}

if (!is_numeric($blockCount) || $blockCount <= 0) {
    echo json_encode(["error" => "Invalid block count received from Pepecoin node."]);
    exit();
}

if ($externalBlockCount <= 0) {
    echo json_encode(["error" => "Invalid external block count or failed to fetch from external API."]);
    exit();
}

// Calculate sync percentage
$syncPercentage = 0;
if ($externalBlockCount > 0 && $blockCount > 0) {
    // Calculate sync percentage only if both values are greater than 0
    $syncPercentage = min(100, ($blockCount / $externalBlockCount) * 100);
} else {
    $syncPercentage = 100;  // If no external blocks or local blocks, assume 100% synced
}

// Process transactions
$transactionList = [];
foreach ($transactions as $tx) {
    if (isset($tx['amount'])) {
        $type = ($tx['category'] == 'receive') ? 'Prijaté' : 'Odoslané';
        $formattedAmount = number_format($tx['amount'], 2, '.', ','); // Format amount
        $transactionList[] = "$formattedAmount Ᵽ";
    }
}

// Prepare JSON response
$walletData = [
    'amount' => $balance ?? 0,
    'syncProgress' => $syncPercentage,
    'transactions' => $transactionList
];

echo json_encode($walletData);

?>
