function fetchWalletBalances() {
    $.ajax({
        url: "get_wallet_balances.php", // PHP script to fetch balances
        method: "GET",
        success: function (response) {
            const balances = JSON.parse(response);
            $("#balance-usdc").text(balances.balance_usdc.toFixed(5));
            $("#balance-sol").text(balances.balance_sol.toFixed(5));
            $("#balance-btc").text(balances.balance_btc.toFixed(5));
            $("#balance-eth").text(balances.balance_eth.toFixed(5));
        },
        error: function () {
            console.error("Failed to fetch wallet balances.");
        },
    });
}

// Call fetchWalletBalances on page load
$(document).ready(fetchWalletBalances);
