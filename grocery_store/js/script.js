// ================= ADD TO CART MESSAGE =================

document.addEventListener("DOMContentLoaded", function () {

    const cartButton = document.getElementById("addCartBtn");

    if (cartButton) {

        cartButton.addEventListener("click", function () {

            cartButton.innerHTML = "🛒 Adding...";

            setTimeout(function () {
                cartButton.innerHTML = "🛒 Add to Cart";
            }, 1000);

        });

    }

});

// ================= QUANTITY VALIDATION =================

const quantityInput = document.getElementById("quantity");

if (quantityInput) {

    quantityInput.addEventListener("input", function () {

        const maxStock = parseInt(quantityInput.max);
        const quantity = parseInt(quantityInput.value);

        if (quantity > maxStock) {

            quantityInput.value = maxStock;

            alert(
                "Only " + maxStock + " items are available in stock."
            );

        }

        if (quantity < 1 || isNaN(quantity)) {

            quantityInput.value = 1;

        }

    });

}

// ================= SUCCESS NOTIFICATION =================

function showSuccessMessage(message) {

    const notification = document.createElement("div");

    notification.className = "success-notification";

    notification.innerHTML = "✅ " + message;

    document.body.appendChild(notification);

    setTimeout(function () {
        notification.remove();
    }, 2500);

}

// ================= CART SUCCESS MESSAGE =================

document.addEventListener("DOMContentLoaded", function () {

    const messageElement =
        document.getElementById("cartSuccessMessage");

    if (messageElement) {

        const message = messageElement.dataset.message;

        showSuccessMessage(message);

    }

});

// ================= CAROUSEL =================

let slideIndex = 1;
let slideTimer;

function showSlide(n) {

    const slides = document.querySelectorAll(".carousel-slide");
    const dots = document.querySelectorAll(".dot");

    if (slides.length === 0) {
        return;
    }

    if (n > slides.length) {
        slideIndex = 1;
    }

    if (n < 1) {
        slideIndex = slides.length;
    }

    slides.forEach(function (slide) {
        slide.classList.remove("active");
    });

    dots.forEach(function (dot) {
        dot.classList.remove("active");
    });

    slides[slideIndex - 1].classList.add("active");

    if (dots[slideIndex - 1]) {
        dots[slideIndex - 1].classList.add("active");
    }
}


function changeSlide(n) {

    slideIndex += n;

    showSlide(slideIndex);

    restartCarousel();

}


function currentSlide(n) {

    slideIndex = n;

    showSlide(slideIndex);

    restartCarousel();

}


function autoSlide() {

    slideIndex++;

    showSlide(slideIndex);

}


function restartCarousel() {

    clearInterval(slideTimer);

    slideTimer = setInterval(autoSlide, 4000);

}


// Start Carousel

document.addEventListener("DOMContentLoaded", function () {

    showSlide(slideIndex);

    slideTimer = setInterval(autoSlide, 4000);

});

// ================= CART QUANTITY =================

function changeQuantity(cartId, change, maxStock) {

    const quantityInput = document.getElementById(
        "quantity-" + cartId
    );

    if (!quantityInput) {
        console.error("Quantity input not found:", cartId);
        return;
    }

    let quantity = parseInt(quantityInput.value) || 1;

    quantity += change;

    // Minimum quantity
    if (quantity < 1) {
        quantity = 1;
    }

    // Maximum stock
    if (maxStock > 0 && quantity > maxStock) {
        quantity = maxStock;
    }

    quantityInput.value = quantity;

    console.log(
        "Cart ID:", cartId,
        "Quantity:", quantity
    );

    updateCart(cartId, quantity);
}


// ================= UPDATE CART =================

function updateCart(cartId, quantity) {

    fetch("update_cart.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body:
            "cart_id=" + encodeURIComponent(cartId) +
            "&quantity=" + encodeURIComponent(quantity)

    })

    .then(response => response.json())

    .then(data => {

        if (!data.success) {

            alert(data.message || "Unable to update cart.");

            return;
        }

        // Get product price
        const priceElement =
            document.getElementById("price-" + cartId);

        // Get subtotal element
        const subtotalElement =
            document.getElementById("subtotal-" + cartId);

        // Get grand total
        const grandTotalElement =
            document.getElementById("grand-total");


        if (priceElement && subtotalElement) {

            const price =
                parseFloat(
                    priceElement.textContent.replace("₹", "").trim()
                );

            const subtotal = price * quantity;

            subtotalElement.innerHTML =
                "Subtotal: ₹" + subtotal.toFixed(2);
        }


        // Calculate all subtotals again
        let grandTotal = 0;

        document.querySelectorAll(".cart-subtotal")
            .forEach(function (element) {

                const subtotal =
                    parseFloat(
                        element.textContent
                        .replace("Subtotal:", "")
                        .replace("₹", "")
                        .trim()
                    );

                if (!isNaN(subtotal)) {
                    grandTotal += subtotal;
                }

            });


        if (grandTotalElement) {

            grandTotalElement.textContent =
                grandTotal.toFixed(2);

        }

    })

    .catch(error => {

        console.error("Cart update error:", error);

        alert("Cart update failed.");

    });

}