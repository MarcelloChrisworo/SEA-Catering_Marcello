// Navigation Functions
function HOME() {
    window.location.href = "SeaCateringMain.html";
}

function MENU() {
    window.location.href = "SeaCateringMenu.html";
}

function SUBSCRIPTION() {
    alert("Subscription page coming soon!");
}

function CONTACTUS() {
    alert("Contact page coming soon!");
}


// ----------------------- MENU PAGE ----------------------------//

// Meals data
const meals = [
    {
        name: "Grilled Salmon Bowl",
        description: "Fresh grilled salmon with quinoa, avocado, and seasonal vegetables",
        image: "Assets/Images/salmon-bowl.jpg",
        price: 85000,
        calories: 520,
        protein: "35g",
        category: ["lunch", "dinner", "high-protein"]
    },
    {
        name: "Chicken Teriyaki Bowl",
        description: "Tender chicken teriyaki with brown rice and steamed broccoli",
        image: "Assets/Images/chicken-teriyaki.jpg",
        price: 65000,
        calories: 480,
        protein: "30g",
        category: ["lunch", "dinner", "high-protein"]
    },
    {
        name: "Vegetarian Buddha Bowl",
        description: "Colorful mix of roasted vegetables, chickpeas, and tahini dressing",
        image: "Assets/Images/buddha-bowl.jpg",
        price: 55000,
        calories: 420,
        protein: "18g",
        category: ["lunch", "dinner", "vegetarian"]
    },
    {
        name: "Protein Pancakes",
        description: "High-protein pancakes with fresh berries and Greek yogurt",
        image: "Assets/Images/protein-pancakes.jpg",
        price: 45000,
        calories: 380,
        protein: "25g",
        category: ["breakfast", "high-protein"]
    },
    {
        name: "Overnight Oats Bowl",
        description: "Nutritious overnight oats with chia seeds, fruits, and nuts",
        image: "Assets/Images/overnight-oats.jpg",
        price: 35000,
        calories: 320,
        protein: "12g",
        category: ["breakfast", "vegetarian"]
    },
    {
        name: "Mediterranean Salad",
        description: "Fresh mixed greens with feta cheese, olives, and olive oil dressing",
        image: "Assets/Images/mediterranean-salad.jpg",
        price: 50000,
        calories: 290,
        protein: "15g",
        category: ["lunch", "vegetarian"]
    },
    {
        name: "Beef Rendang Bowl",
        description: "Traditional Indonesian beef rendang with coconut rice",
        image: "Assets/Images/beef-rendang.jpg",
        price: 75000,
        calories: 580,
        protein: "32g",
        category: ["lunch", "dinner"]
    },
    {
        name: "Tofu Stir Fry",
        description: "Crispy tofu with mixed vegetables in a savory sauce",
        image: "Assets/Images/tofu-stirfry.jpg",
        price: 45000,
        calories: 380,
        protein: "20g",
        category: ["lunch", "dinner", "vegetarian"]
    }
];

// Display Meals
function displayMeals(mealsToShow = meals) {
    const mealsGrid = document.getElementById('meals-grid');
    if (!mealsGrid) return;
    
    mealsGrid.innerHTML = mealsToShow.map(meal => `
        <div class="meal-card">
            <img src="${meal.image}" alt="${meal.name}" class="meal-image">
            <div class="meal-details">
                <h3 class="meal-name">${meal.name}</h3>
                <p class="meal-description">${meal.description}</p>
                <div class="meal-nutrition">
                    <span>${meal.calories} cal</span>
                    <span>${meal.protein}</span>
                </div>
                <div class="meal-price">Rp ${meal.price.toLocaleString()}</div>
                <button class="add-to-cart-btn" onclick="addToCart('${meal.name}', ${meal.price})">Add to Cart</button>
            </div>
        </div>
    `).join('');
}

// Search
function filterMeals() {
    const searchText = document.getElementById('search-bar').value.toLowerCase();
    const filtered = meals.filter(meal => 
        meal.name.toLowerCase().includes(searchText) || 
        meal.description.toLowerCase().includes(searchText)
    );
    displayMeals(filtered);
}

// Cart function
let cart = JSON.parse(localStorage.getItem('seaCateringCart')) || [];

function addToCart(mealName, price) {
    const existingItem = cart.find(item => item.name === mealName);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ name: mealName, price: price, quantity: 1 });
    }
    
    localStorage.setItem('seaCateringCart', JSON.stringify(cart));
    updateCartDisplay();
    alert(mealName + ' added to cart!');
}

function updateCartDisplay() {
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
    
    if (cartItems) {
        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p>Rp ${item.price.toLocaleString()}</p>
                </div>
                <div class="cart-item-controls">
                    <button onclick="changeQuantity('${item.name}', ${item.quantity - 1})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="changeQuantity('${item.name}', ${item.quantity + 1})">+</button>
                    <button onclick="removeFromCart('${item.name}')" class="remove-btn">🗑️</button>
                </div>
            </div>
        `).join('');
    }
    
    if (cartTotal) {
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotal.textContent = total.toLocaleString();
    }
}

function changeQuantity(mealName, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(mealName);
        return;
    }
    
    const item = cart.find(item => item.name === mealName);
    if (item) {
        item.quantity = newQuantity;
        localStorage.setItem('seaCateringCart', JSON.stringify(cart));
        updateCartDisplay();
    }
}

function removeFromCart(mealName) {
    cart = cart.filter(item => item.name !== mealName);
    localStorage.setItem('seaCateringCart', JSON.stringify(cart));
    updateCartDisplay();
}

function toggleCart() {
    const cartSummary = document.getElementById('cartSummary');
    if (cartSummary) {
        cartSummary.style.display = cartSummary.style.display === 'block' ? 'none' : 'block';
    }
}

function clearCart() {
    cart = [];
    localStorage.removeItem('seaCateringCart');
    updateCartDisplay();
    alert('Cart cleared!');
}

function goToCheckout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    alert('Checkout total: Rp ' + total.toLocaleString());
}

document.addEventListener('DOMContentLoaded', function() {
    displayMeals(); // Show all meals first
    updateCartDisplay(); // Update cart counter
});

// TESTIMONIALS FUNCTION
//I ALSO NEEED AI ON THIS PART ESPECIALLY 4 THOSE STARS EMOJI
let testimonials = [
];

let currentTestimonial = 0;
let currentRating = 0;

function displayTestimonials() {
    const display = document.getElementById('testimonialDisplay');
    if (!display || testimonials.length === 0) return;
    
    const testimonial = testimonials[currentTestimonial];
    const stars = '★'.repeat(testimonial.rating) + '☆'.repeat(5 - testimonial.rating);
    
    display.innerHTML = `
        <div class="testimonial-item active">
            <blockquote>"${testimonial.message}"</blockquote>
            <div class="testimonial-author">${testimonial.name}</div>
            <div class="testimonial-stars">${stars}</div>
        </div>
    `;
}

function nextTestimonial() {
    currentTestimonial = (currentTestimonial + 1) % testimonials.length;
    displayTestimonials();
}

function previousTestimonial() {
    currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
    displayTestimonials();
}

function setRating(rating) {
    currentRating = rating;
    const stars = document.querySelectorAll('#starRating .star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.style.color = '#ffd700';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.style.color = '#ccc';
            star.classList.remove('active');
        }
    });
}

function submitTestimonial(event) {
    event.preventDefault();
    
    const name = document.getElementById('customerName').value;
    const message = document.getElementById('reviewMessage').value;
    
    if (name && message && currentRating > 0) {
        // Add new testimonial to the array
        testimonials.push({
            name: name,
            message: message,
            rating: currentRating
        });
        
        // Reset form
        document.getElementById('testimonialForm').reset();
        setRating(0);
        currentRating = 0;
        
        // Show the new testimonial
        currentTestimonial = testimonials.length - 1;
        displayTestimonials();
        
        alert('Thank you for your testimonial! Your review has been added.');
    } else {
        alert('Please fill in all fields and select a rating.');
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    displayMeals(); // Show all meals first
    updateCartDisplay(); // Update cart counter
    displayTestimonials(); // Show testimonials
    
    // Auto-rotate testimonials every 5 seconds
    if (testimonials.length > 1) {
        setInterval(nextTestimonial, 5000);
    }
});

