<div class="balance"><span id="amount"><?php echo $data['amount'] ?? 0; ?></span> Ᵽ</div>
<div class="value"><span id="value"><?php echo $data['value'] ?? '0.00'; ?></span>€</div>
<button class="btn btn-inline" onclick="location.href='index.php?page=send'">
    <img src="/assets/send-dark.png" alt="Send">
</button>
<button class="btn btn-inline" onclick="location.href='index.php?page=receive'">
    <img src="/assets/receive-dark.png" alt="Receive">
</button>

<div class="transactions">
    <h3>Nedávne Transakcie</h3>
    <div></div>
    <button class="btn" onclick="location.href='index.php?page=transactions'">Všetky Transakcie</button>
</div>

<div class="progress">
    <div class="progress-bar" style="width: 0%;"></div>
    <span id="syncStatus">Synchronizácia: 0%</span> <!-- Display sync status -->
</div>

<div class="logo">
    <img src="/assets/logo.png" alt="Logo">
</div>

<script>
    fetch('wallet_data.php')
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check the response data in the console

            // Handle the transactions
            if (data.transactions && data.transactions.length > 0) {
                const transactionList = document.querySelector('.transactions div');

                // Reverse the transaction order to show newest first
                const reversedTransactions = data.transactions.reverse();

                // Assuming 'reversedTransactions' is an array of transactions
                reversedTransactions.forEach(tx => {
                    // Create a div with the class 'transaction' for each transaction
                    const transactionDiv = document.createElement('div');
                    transactionDiv.classList.add('transaction');

                    // Create a <p> element for the transaction content
                    const p = document.createElement('p');
                    p.textContent = tx;  // Set the transaction content

                    // Append the <p> element to the transaction div
                    transactionDiv.appendChild(p);

                    // Finally, append the transaction div to the transaction list container
                    transactionList.appendChild(transactionDiv);
                });


            } else {
                const transactionList = document.querySelector('.transactions ul');
                transactionList.innerHTML = '<li>Nenašli sa žiadne transakcie.</li>';
            }

            // Handle sync progress (show progress percentage)
            if (data.syncProgress !== undefined) {
                const progressBar = document.querySelector('.progress-bar');
                const syncStatus = document.getElementById('syncStatus');
                console.log(data.syncProgress); // Check the sync progress in the console

                // Update the progress bar width and status text
                progressBar.style.width = `${data.syncProgress}%`;

                // Change text to "Synced" if 100%, otherwise show the syncing percentage
                if (data.syncProgress === 100) {
                    syncStatus.textContent = "Synchronizované";
                } else {
                    syncStatus.textContent = `Synchronizuje sa: ${Math.round(data.syncProgress)}%`;
                }
            }
        })
        .catch(error => console.error('Nastala chyba:', error));
</script>