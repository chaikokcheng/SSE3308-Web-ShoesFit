


const products = [
    { 
        id: 1, 
        name: "Nike Dunk Low Retro", 
        category: "Sport", 
        description: "Optimized for performance and agility.", 
        price: 110, 
        img: "images/sportNIkeDunkLow.png",
        link: "productpage.html"
    },
    { 
        id: 2, 
        name: "New Balance 530", 
        category: "Sport", 
        description: "Classic design meets modern comfort.", 
        price: 100, 
        img: "images/sportNB530.png", 
        link: "productpage.html"
    },
    { 
        id: 3, 
        name: "Adidas Originals Handball Spezial", 
        category: "Sport", 
        description: "Retro style, superior court performance.", 
        price: 85, 
        img: "images/sportAdidasOriginalsHB.png", 
        link: "productpage.html"
    },
    { 
        id: 4, 
        name: "KNIGHT - Business Slip Ons", 
        category: "Formal", 
        description: "Sleek slip-ons for professional elegance.", 
        price: 25, 
        img: "images/formalKnight.png", 
        link: "productpage.html"
    },
    { 
        id: 5, 
        name: "Cole Haan Men's Classics", 
        category: "Formal", 
        description: "Tradition and comfort in classic footwear.", 
        price: 300, 
        img: "images/formalCHmen.png", 
        link: "productpage.html"
    },
    { 
        id: 6, 
        name: "C&K Heeled Sandals", 
        category: "Heels", 
        description: "Chic heeled sandals for every occasion.", 
        price: 65, 
        img: "images/heelCKTexturedTie.png", 
        link: "productpage.html"
    },
    { 
        id: 7, 
        name: "Jimmy Choo Azia Pump 75", 
        category: "Heels", 
        description: "Elegant pumps with signature style.", 
        price: 1080, 
        img: "images/heelJCAziaPump75.png", 
        link: "productpage.html"
    }
];





        const newArrivals = products.slice();
        const trending = products.slice();

        function displayProducts(products, containerId, category = '') {
    const productList = document.getElementById(containerId);
    productList.innerHTML = '';
    products.filter(product => !category || product.category === category).forEach(product => {
        productList.innerHTML += `
            <div class="col-md-4">
                <a href="${product.link}" class="card mb-4 shadow-sm text-decoration-none">
                    <img class="card-img-top" alt="${product.name}" src="${product.img}" style="height: 225px; width: 100%; display: block;">
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

displayProducts(products, 'trending', 'Sport'); 
displayProducts(products, 'newArrivals', 'Heels');

function filterCategory(category) {

    displayProducts(products, 'productList', category);

    document.querySelectorAll('.btn-outline-secondary').forEach(button => {
        button.classList.remove('active');
    });

    document.querySelector(`button[data-category="${category}"]`).classList.add('active');
}

        filterCategory('Formal');


