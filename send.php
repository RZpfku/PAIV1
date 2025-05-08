<h3>Poslať Pepecoin</h3>
<form action="send_transaction.php" method="post">
    <label for="recipient">Adresa príjemcu:</label>
    <input type="text" id="recipient" name="recipient" required><br><br>

    <!-- Virtual Keyboard for Address -->
    <div id="address-keyboard" class="keyboard">
        <div class="row">
            <button type="button" class="key" data-key="1">1</button>
            <button type="button" class="key" data-key="2">2</button>
            <button type="button" class="key" data-key="3">3</button>
            <button type="button" class="key" data-key="4">4</button>
            <button type="button" class="key" data-key="5">5</button>
            <button type="button" class="key" data-key="6">6</button>
            <button type="button" class="key" data-key="7">7</button>
            <button type="button" class="key" data-key="8">8</button>
            <button type="button" class="key" data-key="9">9</button>
            <button type="button" class="key" data-key="0">0</button>
        </div>
        <div class="row">
            <button type="button" class="key" data-key="Q">q</button>
            <button type="button" class="key" data-key="W">w</button>
            <button type="button" class="key" data-key="E">e</button>
            <button type="button" class="key" data-key="R">r</button>
            <button type="button" class="key" data-key="T">t</button>
            <button type="button" class="key" data-key="Y">y</button>
            <button type="button" class="key" data-key="U">u</button>
            <button type="button" class="key" data-key="I">i</button>
            <button type="button" class="key" data-key="O">o</button>
            <button type="button" class="key" data-key="P">p</button>
        </div>
        <div class="row">
            <button type="button" class="key" data-key="A">a</button>
            <button type="button" class="key" data-key="S">s</button>
            <button type="button" class="key" data-key="D">d</button>
            <button type="button" class="key" data-key="F">f</button>
            <button type="button" class="key" data-key="G">g</button>
            <button type="button" class="key" data-key="H">h</button>
            <button type="button" class="key" data-key="J">j</button>
            <button type="button" class="key" data-key="K">k</button>
            <button type="button" class="key" data-key="L">l</button>
        </div>
        <div class="row">
            <button type="button" class="key" data-key="Z">z</button>
            <button type="button" class="key" data-key="X">x</button>
            <button type="button" class="key" data-key="C">c</button>
            <button type="button" class="key" data-key="V">v</button>
            <button type="button" class="key" data-key="B">b</button>
            <button type="button" class="key" data-key="N">n</button>
            <button type="button" class="key" data-key="M">m</button>
        </div>
        <div class="row">
            <button type="button" class="key" id="caps-lock" data-key="capslock">Caps Lock</button>
            <button type="button" class="key" data-key="backspace">Zmazať</button>
            <button type="button" class="key" data-key="clear">Vyčistiť</button>
        </div>
    </div><br><br>

    <label for="amount">Množstvo (Ᵽ):</label>
    <input type="text" id="amount" name="amount" required><br><br>

    <!-- Virtual Keyboard for Amount -->
    <div id="amount-keyboard" class="keyboard">
        <div class="row">
            <button type="button" class="key" data-key="1">1</button>
            <button type="button" class="key" data-key="2">2</button>
            <button type="button" class="key" data-key="3">3</button>
            <button type="button" class="key" data-key="4">4</button>
            <button type="button" class="key" data-key="5">5</button>
            <button type="button" class="key" data-key="6">6</button>
            <button type="button" class="key" data-key="7">7</button>
            <button type="button" class="key" data-key="8">8</button>
            <button type="button" class="key" data-key="9">9</button>
            <button type="button" class="key" data-key="0">0</button>
        </div>
        <div class="row">
            <button type="button" class="key" data-key=".">.</button>
            <button type="button" class="key" id="backspace" data-key="backspace">Zmazať</button>
            <button type="button" class="key" data-key="clear">Vyčistiť</button>
        </div>
    </div><br><br>

    <input type="submit" class="btn" value="Poslať">
</form>

<!-- Display status or errors if any -->
<?php if (isset($_GET['status'])): ?>
    <div class="status">
        <?php
        if ($_GET['status'] == 'success') {
            echo "<p class='send send-success'>Transakcia bola úspešne dokončená!</p>";
        } else if ($_GET['status'] == 'error') {
            echo "<p class='send send-fail'>Nastala chyba.</p>";
        }
        ?>
    </div>
<?php endif; ?>

<!-- Virtual Keyboard Styles and Scripts -->
<style>
    .keyboard {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
    }
    .row {
        display: flex;
        justify-content: center;
    }
    .key {
        margin: 5px;
        padding: 7px 12px;
        font-size: 18px;
        cursor: pointer;
        background-color: #269B4D;
        color: white;
        border: none;
        border-radius: 100px;
        transition: all 0.8s ease;
    }
    .key:hover, .key:active {
        background-color: #208241;
        transform: scale(0.95);
    }
</style>

<script>
    // Toggle Caps Lock
    let capsLock = false;

    document.getElementById('caps-lock').addEventListener('click', function () {
        capsLock = !capsLock;
        const keys = document.querySelectorAll('#address-keyboard .key');

        keys.forEach(key => {
            const keyText = key.textContent.toUpperCase();
            if (capsLock) {
                key.textContent = keyText;
            } else {
                key.textContent = keyText.toLowerCase();
            }
        });
    });

    // Handle virtual keyboard input for the recipient address
    document.querySelectorAll('#address-keyboard .key').forEach(button => {
        button.addEventListener('click', function () {
            const key = this.getAttribute('data-key');
            const inputField = document.getElementById('recipient');

            if (key === 'clear') {
                inputField.value = ''; // Clear the input
            } else if (key === 'capslock') {
                return; // Caps Lock toggle, handled above
            } else if (key === 'backspace') {
                inputField.value = inputField.value.slice(0, -1); // Remove the last character
            } else {
                const char = capsLock ? key.toUpperCase() : key.toLowerCase();
                inputField.value += char; // Append the key to the input field
            }
        });
    });

    // Handle virtual keyboard input for the amount
    document.querySelectorAll('#amount-keyboard .key').forEach(button => {
        button.addEventListener('click', function () {
            const key = this.getAttribute('data-key');
            const inputField = document.getElementById('amount');

            if (key === 'clear') {
                inputField.value = ''; // Clear the input
            } else if (key === 'backspace') {
                inputField.value = inputField.value.slice(0, -1); // Remove the last character
            } else {
                inputField.value += key; // Append the key to the input field
            }
        });
    });
</script>

<?php
function sendPepecoinTransaction($recipient, $amount)
{
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