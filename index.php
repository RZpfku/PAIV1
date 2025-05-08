<?php
// Fetch wallet data from backend
$data = json_decode(file_get_contents('/wallet_data.php'), true);

// Determine the page to include based on the URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'wallet'; // Default to 'wallet' if no page is specified
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRP Digitálny Trezor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background: #292929;
            color: white;
        }

        .container {
            padding: 10px;
            max-width: 400px;
            margin: auto;
        }

        .balance {
            font-size: 24px;
            margin-top: 10px;
        }

        .value {
            font-size: 16px;
            color: #bbb;
        }

        .btn {
            display: block;
            margin: 10px auto;
            padding: 15px;
            width: fit-content;
            background: #269B4D;
            color: white;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s !important;
        }

        .btn:hover,
        .btn:active {
            background: #208241;
            color: white;
        }

        .transaction {
            text-align: center;
            padding: 1px;
            background: #393939;
            border-radius: 28px;
            margin-bottom: 5px;
        }

        .transaction-list {
            text-align: center;
            padding: 5px;
            background: #393939;
            border-radius: 28px;
            margin-bottom: 5px;
        }

        .transactions-page {
            margin-top: 20px;
        }

        .progress {
            width: 100%;
            background: #444;
            height: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .progress-bar {
            height: 100%;
            background: #28a745;
            width: <?= isset($data['sync']) ? $data['sync'] : 0; ?>%;
            border-radius: 100px;
        }

        #syncStatus {
            margin-top: 5px!important;
        }

        .menu {
            display: flex;
            justify-content: space-around;
            padding: 10px;
            background: #393939;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .menu button {
            background: none;
            border: none;
            color: white;
            font-size: 40px;
            cursor: pointer;
        }

        .qr-code img {
            border-radius: 28px;
        }

        .logo img {
            max-width: 260px !important;
            margin-top: -40px;
        }

        /* Make the buttons inline */
        .btn-inline {
            display: inline-block;
            border: none;
            background: transparent;
            padding: 0;
            margin: 0 10px;
            transition: all 0.8s ease;
            /* Space between buttons */
        }

        /* Style the images inside the buttons */
        .btn-inline img {
            width: 80px;
            /* Adjust size of the icons */
            height: 80px;
            display: block;
        }

        .btn-inline:hover,
        .btn-inline:active {
            transform: scale(0.95);
            background: none!important;
        }

        .pagination-img {
            width: 70px!important;
            height: 70px!important;
        }

        .send {
            margin-top: 10px;
            padding: 10px;
            border-radius: 28px;
            font-size: 18px;
        }

        .send-success {
            background: #28a745;
            color: white;
        }

        .send-fail {
            background: #dc3545;
            color: white;
        }

        input {
            padding: 10px;
            border-radius: 28px;
            border: none;
            margin-top: 10px;
            width: 100%;
            background: #393939;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        // Include the appropriate page based on the 'page' query parameter
        switch ($page) {
            case 'wallet':
                include('wallet.php');
                break;
            case 'send':
                include('send.php');
                break;
            case 'receive':
                include('receive.php');
                break;
            case 'transactions':
                include('transactions.php');
                break;
            case 'backup':
                include('backup.php');
                break;
            default:
                include('wallet.php');
                break;
        }
        ?>
    </div>
    <div class="menu">
        <button onclick="location.href='index.php?page=wallet'">
            <img src="/assets/wallet-dark.png" alt="Wallet" style="width: 40px; height: 40px; margin-right: 8px;">
        </button>
        <button onclick="location.href='index.php?page=backup'">
            <img src="/assets/backup-dark.png" alt="Backup" style="width: 30px; height: 30px; margin-right: 8px;">
        </button>
    </div>

    <script>
        // Check if the price is already stored in localStorage and if it's not expired
        let cachedPrice = localStorage.getItem('pepecoinPrice');
        let timestamp = localStorage.getItem('pepecoinTimestamp');

        updateData()

        const cacheTimeout = 300000; // 5 minutes in milliseconds

        if (cachedPrice && timestamp && (Date.now() - timestamp < cacheTimeout)) {
            // Use cached price
            let value = JSON.parse(cachedPrice);
            console.log('Using cached value:', value);
        } else {
            // Fetch Pepecoin price from CoinGecko
            fetch('https://api.coingecko.com/api/v3/simple/price?ids=pepecoin-network&vs_currencies=eur')
                .then(response => response.json())
                .then(data => {
                    const price = data['pepecoin-network']['eur'] || 0;

                    // Cache the price and the timestamp in localStorage
                    localStorage.setItem('pepecoinPrice', JSON.stringify(price));
                    localStorage.setItem('pepecoinTimestamp', Date.now());

                    // Use the fetched price
                    console.log('Fetched new price:', price);
                })
                .catch(error => console.error('Error fetching Pepecoin price:', error));
        }

        function updateData() {
            fetch('/wallet_data.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('amount').innerText = data.amount;
                    document.getElementById('value').innerText = (localStorage.getItem('pepecoinPrice') * data.amount).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.querySelector('.progress-bar').style.width = data.syncProgress + '%';
                });
        }
        setInterval(updateData, 15000);
    </script>
</body>

</html>