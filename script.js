// Hamburger menu functionality
document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.getElementById("hamburger");
    const mobileNav = document.getElementById("mobileNav");
    const menuLinks = mobileNav.querySelectorAll("a");

    // Toggle mobile navigation menu
    hamburger.addEventListener("click", () => {
        console.log("Hamburger clicked");
        mobileNav.classList.toggle("active");
    });

    // Close the mobile menu when a link is clicked
    menuLinks.forEach((link) => {
        link.addEventListener("click", () => {
            mobileNav.classList.remove("active");
        });
    });

    // Close the mobile menu when clicking outside the menu
    document.addEventListener("click", (event) => {
        if (!mobileNav.contains(event.target) && !hamburger.contains(event.target)) {
            mobileNav.classList.remove("active");
        }
    });
});

// Login form handling
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form behavior

            const formData = new FormData(this);

            // Send login data to the backend
            fetch("login.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Redirect to wallet page on success
                        window.location.href = "wallet.html";
                    } else {
                        // Display error message on failure
                        alert(data.message || "Login failed. Please try again.");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred. Please try again.");
                });
        });
    }
});

// Logout button functionality
document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logoutButton");
    if (logoutButton) {
        logoutButton.addEventListener("click", function () {
            // Redirect to logout PHP script
            window.location.href = "logout.php";
        });
    }
});

// Check login status and manage the "Connect Wallet" button
document.addEventListener("DOMContentLoaded", function () {
    const connectWalletBtn = document.getElementById("connectWalletBtn");

    if (connectWalletBtn) {
        fetch("check_login_status.php")
            .then((response) => response.json())
            .then((data) => {
                if (data.loggedIn) {
                    // Update button to "Swap" and attach performSwap
                    connectWalletBtn.textContent = "Swap";
                    connectWalletBtn.removeEventListener("click", redirectToLogin);
                    connectWalletBtn.addEventListener("click", performSwap);
                } else {
                    // Update button to "Connect Wallet" and redirect to login
                    connectWalletBtn.textContent = "Connect Wallet";
                    connectWalletBtn.addEventListener("click", redirectToLogin);
                }
            })
            .catch((error) => {
                console.error("Error checking login status:", error);
            });
    }
});

function redirectToLogin() {
    window.location.href = "login.html";
}


function performSwap() {
    const sellingCrypto = document.getElementById("selling-crypto").value;
    const buyingCrypto = document.getElementById("buying-crypto").value;
    const sellingAmount = parseFloat(document.querySelector(".token-input").value);

    if (!sellingAmount || sellingAmount <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    fetch("perform_swap.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            selling_crypto: sellingCrypto,
            buying_crypto: buyingCrypto,
            selling_amount: sellingAmount,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                alert(data.message);
                // Update balances dynamically
                if (data.balances) {
                    document.getElementById("balance-usdc").textContent = data.balances.USDC.toFixed(5);
                    document.getElementById("balance-sol").textContent = data.balances.SOL.toFixed(5);
                    document.getElementById("balance-btc").textContent = data.balances.BTC.toFixed(5);
                    document.getElementById("balance-eth").textContent = data.balances.ETH.toFixed(5);
                }
            } else {
                alert(data.message || "Swap failed.");
            }
        })
        .catch((error) => {
            console.error("Error performing swap:", error);
            alert("An error occurred. Please check the console for more details.");
        });
    
}

