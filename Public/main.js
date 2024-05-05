

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


