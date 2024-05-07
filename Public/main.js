
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

function toggleAddressForm(show) {
    const addressForm = document.getElementById("addressForm");
    addressForm.style.display = show ? "block" : "none";
}

function enableAddressForm(enable) {
    const addressForm = document.getElementById("address_form");
    const addressInput = addressForm.getElementsByTagName("address_input");
    const addressTextarea = addressForm.getElementsByTagName("address_textarea")[0];
    const addressSelect = addressForm.getElementsByTagName("address_select");
    for (let address_input of addressInput) {
        address_input.disabled = !enable;
    }
    addressTextarea.disabled = !enable;
    for (let select of addressSelect) {
        select.disabled = !enable;
    }
}


function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

document.getElementById("emailInput").addEventListener("blur", function() {
    const emailInput = document.getElementById("emailInput");
    const emailIcon = document.getElementById("emailIcon");
    const emailErrorMessage = document.getElementById("emailErrorMessage");
    const emailFormGroup = document.getElementById("emailFormGroup");
    
    if (validateEmail(emailInput.value)) {
        
        emailIcon.classList.remove("text-danger", "fa-times-circle");
        emailIcon.classList.add("text-success", "fa-check-circle");
        emailIcon.style.display = "inline-block";
        emailErrorMessage.textContent = '';
        emailInput.style.borderColor = 'green';
        emailFormGroup.classList.remove("has-error");
        emailFormGroup.classList.add("has-success");
        toggleAddressForm(true); 
        enableAddressForm(true); 
    }  else {
       
        emailIcon.classList.remove("text-success", "fa-check-circle");
        emailIcon.classList.add("text-danger", "fa-times-circle");
        emailIcon.style.display = "inline-block"; 
        emailErrorMessage.textContent = 'Please enter a valid email address.';
        emailInput.style.borderColor = 'red';
        emailFormGroup.classList.remove("has-success");
        emailFormGroup.classList.add("has-error");
        toggleAddressForm(false); 
        enableAddressForm(false); 
    }
});


function validateField(inputId, errorMessageId, feildName) {
    const fieldInput = document.getElementById(inputId);
    const fieldErrorMessage = document.getElementById(errorMessageId);
    
    if (fieldInput.value.trim() === '') {
        
        fieldErrorMessage.textContent = `Please enter your ${feildName}.`;
        fieldInput.style.borderColor = 'red';  
    } else {

        fieldErrorMessage.textContent = '';
        fieldInput.style.borderColor = 'green';

    }
}

document.getElementById("firstName").addEventListener("blur", function() {
    validateField("firstName", "firstNameErrorMessage", "first name");
});

document.getElementById("lastName").addEventListener("blur", function() {
    validateField("lastName", "lastNameErrorMessage", "last name");
});

document.getElementById("streetAddress").addEventListener("blur", function() {
    validateField("streetAddress", "address1ErrorMessage", "delivery address");
});

document.getElementById("city").addEventListener("blur", function() {
    validateField("city", "cityErrorMessage", "city");
});

document.getElementById("state").addEventListener("blur", function() {
    validateField("state", "stateErrorMessage", "state");
});


function validatePCode_PhNum(inputId, errorMessageId, infoId, fieldName1) {
    const fieldInput = document.getElementById(inputId);
    const errorMessage = document.getElementById(errorMessageId);
    const info = document.getElementById(infoId);

 
    if (fieldInput.value.trim() === '' || document.activeElement === fieldInput) {
        errorMessage.textContent = "";
        
        if (inputId === "postcode" && infoId === "PC-Info") {
            info.textContent = "Example: 11900";
        } else if (inputId === "phoneNumber" && infoId === "PhNum-Info") {
            info.textContent = "Notification will be sent to this number.";
        }
    } else if (/^\d+$/.test(fieldInput.value.trim())) {
        
        errorMessage.textContent = "";
        fieldInput.style.borderColor = 'green';
        if (inputId === "postcode" && infoId === "PC-Info") {
            info.textContent = "Example: 11900";
        } else if (inputId === "phoneNumber" && infoId === "PhNum-Info") {
            info.textContent = "Notification will be sent to this number.";
        }
    } 

    fieldInput.addEventListener("blur", function() {
        if (fieldInput.value.trim() === '') {
            errorMessage.textContent = `Please enter your ${fieldName1}.`;
            info.textContent = ""; 
            fieldInput.style.borderColor = 'red';
        } else if (!/^\d+$/.test(fieldInput.value.trim())) {
            errorMessage.textContent = `Please check your ${fieldName1}.`;
            info.textContent = ""; 
            fieldInput.style.borderColor = 'red';
        }
    });
}

document.getElementById("postcode").addEventListener("blur", function() {
    validatePCode_PhNum("postcode", "postcodeErrorMessage", "PC-Info", "postcode");
});

document.getElementById("phoneNumber").addEventListener("blur", function() {
    validatePCode_PhNum("phoneNumber", "phoneNumberErrorMessage", "PhNum-Info", "phone number");
});


validatePCode_PhNum("postcode", "postcodeErrorMessage", "PC-Info", "postcode");
validatePCode_PhNum("phoneNumber", "phoneNumberErrorMessage", "PhNum-Info", "phone number");


const textFields = document.querySelectorAll('input[type="text"]');
textFields.forEach(textField => {
    textField.addEventListener("input", checkAllFieldsFilled);
});

function checkAllFieldsFilled() {
    const allFieldsFilled = Array.from(textFields).every(textField => textField.value.trim() !== '');
    const nextButton = document.getElementById("addressButton");
    const shippingSection = document.querySelector(".shippingSection"); 

    if (allFieldsFilled) {
        nextButton.disabled = false; 
        nextButton.addEventListener("click", function() {
            shippingSection.style.display = "block"; 
            shippingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    } else {
        nextButton.disabled = true; 
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const shippingSection = document.querySelector(".shippingSection");
    shippingSection.style.display = "none";
});


const termsCheckbox = document.getElementById('terms');
const nextButton = document.getElementById('nextButton');

termsCheckbox.addEventListener('change', function() {
  nextButton.disabled = !this.checked;
});

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
