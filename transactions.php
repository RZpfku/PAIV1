<?php
// Set the default limit for transactions
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // Default to 10 if not provided
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0; // Pagination offset

// Fetch transactions
$cmd = '/home/wallet/pepecoin/bin/pepecoin-cli --datadir=/home/wallet/.pepecoin listtransactions "*" ' . $limit . ' ' . $offset;
$output = shell_exec($cmd);

if ($output === null) {
    echo "<p class='error'>Error: No response from Pepecoin daemon.</p>";
    return;
}

$transactions = json_decode($output, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<p class='error'>Error decoding JSON: " . json_last_error_msg() . "</p>";
    return;
}

// Reverse the transactions to display the newest first
$transactions = array_reverse($transactions);

// Function to format amounts correctly
function formatAmount($amount, $category)
{
    return number_format($amount, 2) . ' Ᵽ';
}

// Function to format timestamps
function formatDateTime($timestamp)
{
    return date("H:i d.m.Y", $timestamp);
}

// Prepare transactions for display
$formattedTransactions = [];
foreach ($transactions as $transaction) {
    $formattedAmount = formatAmount($transaction['amount'], $transaction['category']);
    $confirmations = $transaction['confirmations'] >= 6 ? "Potvrdené" : $transaction['confirmations'] . "/6";
    $dateTime = isset($transaction['time']) ? formatDateTime($transaction['time']) : "N/A";
    
    // Determine address (Sender or Recipient)
    $address = isset($transaction['address']) ? $transaction['address'] : "Unknown";
    $direction = ($transaction['category'] === 'receive') ? "Od" : "Poslané";

    $formattedTransactions[] = "
        <div class='transaction-list'>
            <strong>{$formattedAmount}</strong><br>
            {$direction}: <span class='address'>{$address}</span><br>
            <span class='confirmations'>{$confirmations}</span><br>
            <span class='datetime'>{$dateTime}</span>
        </div>
    ";
}

// Set previous and next offset values
$prevOffset = max(0, $offset - $limit);
$nextOffset = $offset + $limit;

// Check if there are more transactions for the "Next" button
$hasMore = count($transactions) === $limit;
?>

<div class="transactions transactions-page">
    <!-- Display transactions -->
    <?php if (!empty($formattedTransactions)): ?>
        <?= implode("\n", $formattedTransactions) ?>
    <?php else: ?>
        <p>No transactions found.</p>
    <?php endif; ?>
</div>

<!-- Pagination Controls -->
<div class="pagination">
    <?php if ($offset > 0): ?>
        <form method="GET" action="/index.php" style="display: inline;">
            <input type="hidden" name="page" value="transactions">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <input type="hidden" name="offset" value="<?= $prevOffset ?>">
            <button type="submit" class="btn btn-inline"><img class="pagination-img" src="/assets/previous.png" alt="Ďalšia"></button>
        </form>
    <?php endif; ?>

    <?php if ($hasMore): ?>
        <form method="GET" action="/index.php" style="display: inline;">
            <input type="hidden" name="page" value="transactions">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <input type="hidden" name="offset" value="<?= $nextOffset ?>">
            <button type="submit" class="btn btn-inline"><img class="pagination-img" src="/assets/next.png" alt="Predošlá"></button>
        </form>
    <?php endif; ?>
</div>

<div class="logo">
    <img src="/assets/logo.png" alt="Logo">
</div>