
$(document).ready(function() {
    $('#contactForm').submit(function(e) {
        e.preventDefault(); 
        var formData = $(this).serialize(); 

        $.ajax({
            type: 'POST',
            url: 'contactform.php',
            data: formData,
            success: function(response) {
                alert('Thank you for your message!');
                $('#contactForm')[0].reset();
            },
            error: function() {
                alert('Error sending your message.');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var trailer = document.createElement('div');
    trailer.id = 'trailer';
    document.body.appendChild(trailer);

    function moveTrailer(x, y) {
        trailer.style.left = x + 'px';
        trailer.style.top = y + 'px';
        trailer.style.display = 'block'; 
    }

    document.addEventListener('mousemove', function(e) {
        moveTrailer(e.clientX, e.clientY);
    });

    document.addEventListener('mouseleave', function() {
        trailer.style.display = 'none';
    });
});



function fetchProducts(callback, category = '') {
    fetch('products.json')
        .then(response => response.json())
        .then(data => callback(data.products, category))
        .catch(error => console.error('Error loading the products:', error));
}


function displayProducts(products, containerId, category = '') {
    const productList = document.getElementById(containerId);
    productList.innerHTML = '';
    products.filter(product => !category || product.category === category).forEach(product => {
        productList.innerHTML += `
            <div class="col-md-4">
                <a href="productpage.html?id=${product.id}" class="card mb-4 shadow-sm text-decoration-none">
                    <img class="card-img-top" alt="${product.name}" src="${product.img}">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">$${product.price}</small>
                        </div>
                    </div>
                </a>
            </div>
        `;
    });
}

fetchProducts((products) => {
    displayProducts(products, 'trending', 'Sport');
    displayProducts(products, 'newArrivals', 'Heels');
});


//-----------------------------------------------------------------------------------------------------------------

// Product Details Page + Cart Page
function addToCart(productId, size, color, quantity) {
    let cart = localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [];
    const itemId = `${productId}_${size}_${color}`;

    const cartItem = {
        id: itemId,
        productId: productId,
        size: size,
        color: color,
        quantity: quantity
    };

    const itemIndex = cart.findIndex(item => item.id === itemId);

    if (itemIndex === -1) {
        cart.push(cartItem);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Product added to cart!');
    } else {
        cart[itemIndex].quantity += quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Product quantity updated in cart!');
    }
}

//-----------------------------------------------------------------------------------------------------------------

//shopping cart
const calculateTotal = () => {
    let productTotalAmt = 0;

    $('.card.p-4').each(function () {
        const itemPrice = parseFloat($(this).find('.price_money span').text());
        const itemQuantity = parseInt($(this).find('.set_quantity input').val());
        productTotalAmt += itemPrice * itemQuantity;
    });

    const shippingCharge = parseFloat(document.getElementById('shipping_charge').innerText);

    document.getElementById('product_total_amt').innerText = productTotalAmt.toFixed(2);
    document.getElementById('total_cart_amt').innerText = (productTotalAmt + shippingCharge).toFixed(2);
}

$(document).ready(function () {
    calculateTotal();
});

$(document).ready(function () {

    function calculateDeliveryDate() {
        let today = new Date();
        let starterDay = new Date(today);
        starterDay.setDate(today.getDate() + 4);

        let afterDay = new Date(starterDay);
        afterDay.setDate(starterDay.getDate() + 2);

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        let starterDayStr = starterDay.toLocaleDateString('en-US', options);
        let afterDayStr = afterDay.toLocaleDateString('en-US', options);

        $('#expected-delivery-date').text(`${starterDayStr} - ${afterDayStr}`);
    }

    calculateDeliveryDate();
});
// cart

// payment
// Countdown Timer
var countDownTime = new Date().getTime() + 20 * 60 * 1000;

var x = setInterval(function () {
    var now = new Date().getTime();
    var distance = countDownTime - now;
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s ";

    if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";

        setTimeout(function () {
            location.reload();
        }, 1000);
    }
}, 1000);

// Validate Email Format
function validateEmailFormat(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

document.getElementById("emailInput").addEventListener("blur", function () {
    const emailInput = document.getElementById("emailInput");
    const emailCheckIcon = document.getElementById("email-checkIcon");
    const emailErrorMessage = document.getElementById("emailErrorMessage");
    const emailFormGroup = document.getElementById("emailFormGroup");
    const addressSection = document.querySelector(".addressSection");

    if (validateEmailFormat(emailInput.value)) {
        emailCheckIcon.classList.remove("text-danger", "fa-times-circle");
        emailCheckIcon.classList.add("text-success", "fa-check-circle");
        emailCheckIcon.style.display = "inline-block";
        emailErrorMessage.textContent = '';
        emailInput.style.borderColor = 'green';
        emailFormGroup.classList.remove("has-error");
        emailFormGroup.classList.add("has-success");
        addressSection.style.display = "block";
    } else {
        emailCheckIcon.classList.remove("text-success", "fa-check-circle");
        emailCheckIcon.classList.add("text-danger", "fa-times-circle");
        emailCheckIcon.style.display = "inline-block";
        emailErrorMessage.textContent = 'Please enter a valid email address.';
        emailInput.style.borderColor = 'red';
        emailFormGroup.classList.remove("has-success");
        emailFormGroup.classList.add("has-error");
    }
});

// Validate Address Fields
function validateAddressField(input, errorMessage, fieldName) {
    const fieldInput = document.getElementById(input);
    const fieldErrorMessage = document.getElementById(errorMessage);

    if (fieldInput.value.trim() === '') {
        fieldErrorMessage.textContent = `Please enter your ${fieldName}.`;
        fieldInput.style.borderColor = 'red';
    } else {
        fieldErrorMessage.textContent = '';
        fieldInput.style.borderColor = 'green';
    }
}

document.getElementById("firstName").addEventListener("blur", function () {
    validateAddressField("firstName", "firstNameErrorMessage", "first name");
});

document.getElementById("lastName").addEventListener("blur", function () {
    validateAddressField("lastName", "lastNameErrorMessage", "last name");
});

document.getElementById("streetAddress").addEventListener("blur", function () {
    validateAddressField("streetAddress", "addressErrorMessage", "delivery address");
});

document.getElementById("city").addEventListener("blur", function () {
    validateAddressField("city", "cityErrorMessage", "city");
});

document.getElementById("state").addEventListener("blur", function () {
    validateAddressField("state", "stateErrorMessage", "state");
});


function validatePCode_PhNum(input, errorMessage, footer, fieldName) {
    const fieldInput = document.getElementById(input);
    const fieldErrorMessage = document.getElementById(errorMessage);
    const fieldFooter = document.getElementById(footer);

    fieldInput.addEventListener("blur", function () {
        if (fieldInput.value.trim() === '') {
            fieldErrorMessage.textContent = `Please enter your ${fieldName}.`;
            fieldFooter.textContent = '';
            fieldInput.style.borderColor = 'red';
        } else if (!/^\d+$/.test(fieldInput.value.trim())) {
            fieldErrorMessage.textContent = `Please check your ${fieldName}.`;
            fieldFooter.textContent = '';
            fieldInput.style.borderColor = 'red';
        } else {
            fieldErrorMessage.textContent = '';
            fieldInput.style.borderColor = 'green';
        }
    });
}

document.getElementById("postcode").addEventListener("blur", function () {
    validatePCode_PhNum("postcode", "postcodeErrorMessage", "PC-Footer", "postcode");
});

document.getElementById("phoneNumber").addEventListener("blur", function () {
    validatePCode_PhNum("phoneNumber", "phoneNumberErrorMessage", "PhNum-Footer", "phone number");
});


const textFields = document.querySelectorAll('#firstName, #lastName, #streetAddress, #city, #state, #postcode, #phoneNumber');

textFields.forEach(textField => {
    textField.addEventListener("input", checkAllFieldsFilled);
});

function checkAllFieldsFilled() {
    const allFieldsFilled = Array.from(textFields).every(textField => textField.value.trim() !== '');
    const addressButton = document.getElementById("addressButton");
    const shippingSection = document.querySelector(".shippingSection");

    if (allFieldsFilled) {
        addressButton.disabled = false;
        addressButton.addEventListener("click", function () {
            shippingSection.style.display = "block";
            shippingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    } else {
        addressButton.disabled = true;
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const shippingSection = document.querySelector(".shippingSection");
    const paymentSection = document.querySelector(".paymentSection");
    shippingSection.style.display = "none";
    paymentSection.style.display = "none";

    const checkbox = document.getElementById('t&c');
    const shippingButton = document.getElementById('shippingButton');

    checkbox.addEventListener('change', function () {
        if (!this.checked) {
            shippingButton.disabled = true;
            paymentSection.style.display = "none";
        } else {
            shippingButton.disabled = false;
        }
    });

    shippingButton.addEventListener('click', function () {
        paymentSection.style.display = "block";
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const creditCardRadio = document.getElementById('creditCardRadio');
    const creditCardDetails = document.getElementById('creditCardDetails');
    const onlineBankingRadio = document.getElementById('onlineBankingRadio');
    const onlineBankingDetails = document.getElementById('onlineBankingDetails');
    const paymentButton = document.getElementById('paymentButton');

    onlineBankingDetails.style.display = 'none';
    creditCardDetails.style.display = 'none';

    creditCardRadio.addEventListener('change', function () {
        if (this.checked) {
            creditCardDetails.style.display = 'block';
            onlineBankingDetails.style.display = 'none';
            paymentButton.disabled = false;
            // document.getElementById('paymentMethodInfo').textContent = 'Via: Credit/Debit Card';
        }
    });

    onlineBankingRadio.addEventListener('change', function () {
        if (this.checked) {
            creditCardDetails.style.display = 'none';
            onlineBankingDetails.style.display = 'block';
            paymentButton.disabled = false;
            // document.getElementById('paymentMethodInfo').textContent = 'Via: Credit/Debit Card';
        }
    });
});


const nameOnCardRegex = /^[a-zA-Z ]+$/;
const cardNumberRegex = /^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/;
const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
const cvvRegex = /^[0-9]{3}$/;

function validateCard(input, errorMessage, fieldName, regex) {
    const cardInput = document.getElementById(input);
    const cardErrorMessage = document.getElementById(errorMessage);

    if (cardInput.value.trim() === '') {
        cardErrorMessage.textContent = `Please enter your ${fieldName}.`;
        cardInput.style.borderColor = 'red';
    } else {
        const isValid = regex.test(cardInput.value.trim());
        if (isValid) {
            cardErrorMessage.textContent = '';
            cardInput.style.borderColor = 'green';
        } else {
            cardErrorMessage.textContent = `Please enter a valid ${fieldName}.`;
            cardInput.style.borderColor = 'red';
            cardErrorMessage.style.color = 'red';
        }
    }
}

document.getElementById('nameOnCard').addEventListener('input', function () {
    validateCard('nameOnCard', 'nameOnCardValidity', 'name on card', nameOnCardRegex);
});

document.getElementById('cardNumber').addEventListener('input', function () {
    validateCard('cardNumber', 'cardNumberValidity', 'card number', cardNumberRegex);
});

document.getElementById('expiryDate').addEventListener('input', function () {
    validateCard('expiryDate', 'expiryDateValidity', 'expiry date (MM/YY)', expiryDateRegex);
});

document.getElementById('cvc').addEventListener('input', function () {
    validateCard('cvc', 'cvcValidity', 'CVV (3 digits)', cvvRegex);
});


const inputs = document.querySelectorAll('.form-control');

inputs.forEach(input => {
    input.addEventListener('focus', function () {
        this.removeAttribute('placeholder');
    });
});


$(document).ready(function () {
    $('#paymentButton').click(function () {
        const streetAddress = $('#streetAddress').val();
        const city = $('#city').val();
        const state = $('#state').val();
        const postcode = $('#postcode').val();

        localStorage.setItem("streetAddress", streetAddress);
        localStorage.setItem("city", city);
        localStorage.setItem("state", state);
        localStorage.setItem("postcode", postcode);

        $('#myModal').modal('show');
    });
});

$(document).ready(function () {
    $('.cart-btn').click(function () {
        $('#codeModal').modal('show');
    });
});

// payment


function filterCategory(category) {
    fetchProducts((products) => {
        displayProducts(products, 'productList', category);
    });

    document.querySelectorAll('.btn-outline-secondary').forEach(button => {
        button.classList.remove('active');
    });

    document.querySelector(`button[data-category="${category}"]`).classList.add('active');
}
// filterCategory('Formal');

//Order Summary
// document.addEventListener("DOMContentLoaded", function () {
//     const streetAddress = localStorage.getItem("streetAddress");
//     const city = localStorage.getItem("city");
//     const state = localStorage.getItem("state");
//     const postcode = localStorage.getItem("postcode");

//     let fullAdress = '';
//     if (streetAddress) fullAddress += streetAddress;
//     if (city) fullAddress += ', ${city}';
//     if (state) fullAddress += ', ${state}';
//     if (postcode) fullAddress += ', ${postcode}';

//     if (fullAdress) document.getElementById("fullAddress").textContent = fullAddress;
//     document.getElementById("billingAddress").textContent = fullAddress;
// });
//
// const printBtn = document.getElementById("printBtn");
// if (printBtn) {
//     printBtn.addEventListener("click", function () {
//         window.print();
//     });
// }

// printBtn.addEventListener("click", function () {
//     console.log("Print button clicked");
//     window.print();
// });

//Order Summary