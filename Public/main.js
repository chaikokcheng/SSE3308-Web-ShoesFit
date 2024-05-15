
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
                <a href="${product.link}" class="card mb-4 shadow-sm text-decoration-none">
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


// payment

function validateEmailFormat(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

document.getElementById("emailInput").addEventListener("blur", function() {
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
    }  
    else {
        emailCheckIcon.classList.remove("text-success", "fa-check-circle");
        emailCheckIcon.classList.add("text-danger", "fa-times-circle");
        emailCheckIcon.style.display = "inline-block"; 
        emailErrorMessage.textContent = 'Please enter a valid email address.';
        emailInput.style.borderColor = 'red';
        emailFormGroup.classList.remove("has-success");
        emailFormGroup.classList.add("has-error"); 
    }
});



function validateAddressField(input, errorMessage, fieldName) {
    const fieldInput = document.getElementById(input);
    const fieldErrorMessage = document.getElementById(errorMessage);
    
    if (fieldInput.value.trim() === '') {
        fieldErrorMessage.textContent = `Please enter your ${fieldName}.`;
        fieldInput.style.borderColor = 'red';  
    } 
    else {
        fieldErrorMessage.textContent = '';
        fieldInput.style.borderColor = 'green';
    }
}

document.getElementById("firstName").addEventListener("blur", function() {
    validateAddressField("firstName", "firstNameErrorMessage", "first name");
});

document.getElementById("lastName").addEventListener("blur", function() {
    validateAddressField("lastName", "lastNameErrorMessage", "last name");
});

document.getElementById("streetAddress").addEventListener("blur", function() {
    validateAddressField("streetAddress", "addressErrorMessage", "delivery address");
});

document.getElementById("city").addEventListener("blur", function() {
    validateAddressField("city", "cityErrorMessage", "city");
});

document.getElementById("state").addEventListener("blur", function() {
    validateAddressField("state", "stateErrorMessage", "state");
});



function validatePCode_PhNum(input, errorMessage, footer, fieldName) {
    const fieldInput = document.getElementById(input);
    const fieldErrorMessage = document.getElementById(errorMessage);
    const fieldFooter = document.getElementById(footer);

    if (fieldInput.value.trim() === '') {   
        if (input === "postcode" && footer === "PC-Footer") {
            fieldFooter.textContent = "Example: 11900";
        } 
        else if (input === "phoneNumber" && footer === "PhNum-Footer") {
            fieldFooter.textContent = "Notification will be sent to this number.";
        }
    } 
    
    else if (/^\d+$/.test(fieldInput.value.trim())) {
        fieldErrorMessage.textContent = "";
        fieldInput.style.borderColor = 'green';
        if (input === "postcode" && footer === "PC-Footer") {
            fieldFooter.textContent = "Example: 11900";
        } 
        else if (input === "phoneNumber" && footer === "PhNum-Footer") {
            fieldFooter.textContent = "Notification will be sent to this number.";
        }
    } 

    fieldInput.addEventListener("blur", function() {
        if (fieldInput.value.trim() === '') {
            fieldErrorMessage.textContent = `Please enter your ${fieldName}.`;
            fieldFooter.textContent = ""; 
            fieldInput.style.borderColor = 'red';
        } 
        else if (!/^\d+$/.test(fieldInput.value.trim())) {
            fieldErrorMessage.textContent = `Please check your ${fieldName}.`;
            fieldFooter.textContent = ""; 
            fieldInput.style.borderColor = 'red';
        }
    });
}

document.getElementById("postcode").addEventListener("blur", function() {
    validatePCode_PhNum("postcode", "postcodeErrorMessage", "PC-Footer", "postcode");
});

document.getElementById("phoneNumber").addEventListener("blur", function() {
    validatePCode_PhNum("phoneNumber", "phoneNumberErrorMessage", "PhNum-Footer", "phone number");
});

validatePCode_PhNum("postcode", "postcodeErrorMessage", "PC-Footer", "postcode");
validatePCode_PhNum("phoneNumber", "phoneNumberErrorMessage", "PhNum-Footer", "phone number");



const textFields = document.querySelectorAll('input[type="text"]');
textFields.forEach(textField => {
    textField.addEventListener("input", checkAllFieldsFilled);
});

function checkAllFieldsFilled() {
    const allFieldsFilled = Array.from(textFields).every(textField => textField.value.trim() !== '');
    const addressButton = document.getElementById("addressButton");
    const shippingSection = document.querySelector(".shippingSection"); 

    if (allFieldsFilled) {
        addressButton.disabled = false; 
        addressButton.addEventListener("click", function() {
            shippingSection.style.display = "block"; 
            shippingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    } 
    else {
        addressButton.disabled = true; 
    }
}


document.addEventListener("DOMContentLoaded", function() {
    const shippingSection = document.querySelector(".shippingSection");
    const paymentSection = document.querySelector(".paymentSection");
    shippingSection.style.display = "none";
    paymentSection.style.display = "none";

    const checkbox = document.getElementById('t&c');
    const shippingButton = document.getElementById('shippingButton');

    checkbox.addEventListener('change', function() {
        if (!this.checked) {
            shippingButton.disabled = true;
            paymentSection.style.display = "none";
        }
        else {
            shippingButton.disabled = false;
        }
    });
    
    shippingButton.addEventListener('click', function() {
        paymentSection.style.display = "block";
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});


document.addEventListener("DOMContentLoaded", function() {
    const creditCardRadio = document.getElementById('creditCardRadio');
    const creditCardDetails = document.getElementById('creditCardDetails');
    const onlineBankingRadio = document.getElementById('onlineBankingRadio');
    const onlineBankingDetails = document.getElementById('onlineBankingDetails');
    const paymentButton = document.getElementById('paymentButton');

    onlineBankingDetails.style.display = 'none';
    creditCardDetails.style.display = 'none';
   
    creditCardRadio.addEventListener('change', function() {
        if (this.checked) {
            creditCardDetails.style.display = 'block';
            onlineBankingDetails.style.display = 'none';
            paymentButton.disabled = false;
        }
    });

    onlineBankingRadio.addEventListener('change', function() {
        if (this.checked) {
            creditCardDetails.style.display = 'none';
            onlineBankingDetails.style.display = 'block';
            paymentButton.disabled = false;
        }
    });
});


const nameOnCardRegex = /^[a-zA-Z ]+$/; 
const cardNumberRegex = /^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/; 
const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/; 
const cvvRegex = /^[0-9]{3}$/; 

function formatCardNumber(input) {
    const inputValue = input.value.replace(/\D/g, '');

    const formattedValue = inputValue.match(/.{1,4}/g).join(' ');

    input.value = formattedValue;
}

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

document.getElementById('nameOnCard').addEventListener('input', function() {
    validateCard('nameOnCard', 'nameOnCardValidity', 'name on card', nameOnCardRegex);
});

document.getElementById('cardNumber').addEventListener('input', function() {
    validateCard('cardNumber', 'cardNumberValidity', 'card number', cardNumberRegex);
});

document.getElementById('expiryDate').addEventListener('input', function() {
    validateCard('expiryDate', 'expiryDateValidity', 'expiry date (MM/YY)', expiryDateRegex);
});

document.getElementById('cvc').addEventListener('input', function() {
    validateCard('cvc', 'cvcValidity', 'CVV (3 digits)', cvvRegex);
});

const inputs = document.querySelectorAll('.form-control');

    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.removeAttribute('placeholder');
        });
    });

$(document).ready(function() {
    $('#paymentButton').click(function() {
        $('#myModal').modal('show');
    });
});

$(document).ready(function() {
    $('.cart-btn').click(function() {
        $('#codeModal').modal('show');
    });
});

  
var countDownTime = new Date().getTime() + 1 * 60 * 1000;

var x = setInterval(function() {
    var now = new Date().getTime();
    var distance = countDownTime - now;
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
    document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s ";
        
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";

        setTimeout(function() {
            location.reload();
        }, 1000);
    }
}, 1000);

document.getElementById("email").addEventListener("input", updateEmailFeedback);
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
        filterCategory('Formal');
