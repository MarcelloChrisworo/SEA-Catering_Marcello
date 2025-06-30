// Navigation Functions
function HOME() {
    window.location.href = "SeaCateringMain.html";
}

function MENU() {
    window.location.href = "SeaCateringMenu.html";
}

function SUBSCRIPTION() {
    window.location.href = "SeaCateringSubscription.html";
}

function CONTACTUS() {
    alert("BELOM U ISI WOE JAN LUPS");
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

// ======================= SUBSCRIPTION PAGE =======================//

// Plan prices
const planPrices = {
    diet: 30000,
    protein: 40000,
    royal: 60000
};

// Additional validation functions for subscription form
function validateMealTypes() {
    const selectedMealTypes = document.querySelectorAll('input[name="mealTypes"]:checked');
    const errorElement = document.getElementById('mealTypesError');
    
    if (selectedMealTypes.length === 0) {
        if (errorElement) errorElement.style.display = 'block';
        return false;
    } else {
        if (errorElement) errorElement.style.display = 'none';
        return true;
    }
}

function validateDeliveryDays() {
    const selectedDeliveryDays = document.querySelectorAll('input[name="deliveryDays"]:checked');
    const errorElement = document.getElementById('deliveryDaysError');
    
    if (selectedDeliveryDays.length === 0) {
        if (errorElement) errorElement.style.display = 'block';
        return false;
    } else {
        if (errorElement) errorElement.style.display = 'none';
        return true;
    }
}

// Calculate subscription price
function calculatePrice() {
    const selectedPlan = document.querySelector('input[name="plan"]:checked');
    const selectedMealTypes = document.querySelectorAll('input[name="mealTypes"]:checked');
    const selectedDeliveryDays = document.querySelectorAll('input[name="deliveryDays"]:checked');
    
    const priceSummary = document.getElementById('priceSummary');
    const subscribeBtn = document.getElementById('subscribeBtn');
    
    const mealTypeError = document.getElementById('mealTypesError');
    const deliveryDaysError = document.getElementById('deliveryDaysError');
    if (mealTypeError) mealTypeError.style.display = 'none';
    if (deliveryDaysError) deliveryDaysError.style.display = 'none';
    
    if (selectedPlan && selectedMealTypes.length > 0 && selectedDeliveryDays.length > 0) {
        const planPrice = parseInt(selectedPlan.dataset.price);
        const mealTypeCount = selectedMealTypes.length;
        const deliveryDayCount = selectedDeliveryDays.length;
        
        //Formula TotalPrice
        const totalPrice = planPrice * mealTypeCount * deliveryDayCount * 4;
        
        // Update summary display
        document.getElementById('selectedPlan').textContent = 
            selectedPlan.value.charAt(0).toUpperCase() + selectedPlan.value.slice(1) + ' Plan (Rp ' + planPrice.toLocaleString() + '/meal)';
        
        document.getElementById('selectedMeals').textContent = 
            Array.from(selectedMealTypes).map(input => input.value.charAt(0).toUpperCase() + input.value.slice(1)).join(', ') + ' (' + mealTypeCount + ' types)';
        
        document.getElementById('selectedDays').textContent = 
            Array.from(selectedDeliveryDays).map(input => input.value.charAt(0).toUpperCase() + input.value.slice(1)).join(', ') + ' (' + deliveryDayCount + ' days)';
        
        document.getElementById('calculationBreakdown').textContent = 
            `Rp ${planPrice.toLocaleString()} × ${mealTypeCount} × ${deliveryDayCount} × 4`;
        
        document.getElementById('totalPrice').textContent = 'Rp ' + Math.round(totalPrice).toLocaleString();
        
        priceSummary.style.display = 'block';
        subscribeBtn.disabled = false;
    } else {
        priceSummary.style.display = 'none';
        subscribeBtn.disabled = true;
    }
}

function validateSubscriptionForm() {
    const fullName = document.getElementById('fullName').value.trim();
    const phoneNumber = document.getElementById('phoneNumber').value.trim();
    const selectedPlan = document.querySelector('input[name="plan"]:checked');
    const selectedMealTypes = document.querySelectorAll('input[name="mealTypes"]:checked');
    const selectedDeliveryDays = document.querySelectorAll('input[name="deliveryDays"]:checked');
    
    let isValid = true;
    
    if (!fullName || !phoneNumber || !selectedPlan) {
        isValid = false;
    }
    
    if (selectedMealTypes.length === 0) {
        document.getElementById('mealTypesError').style.display = 'block';
        isValid = false;
    }
    
    if (selectedDeliveryDays.length === 0) {
        document.getElementById('deliveryDaysError').style.display = 'block';
        isValid = false;
    }
    
    // Validate phone number
    const phonePattern = /^[0-9]{10,13}$/;
    if (phoneNumber && !phonePattern.test(phoneNumber)) {
        alert('Please enter a valid phone number (10-13 digits)');
        isValid = false;
    }
    
    return isValid;
}

// Submit subscription form
async function submitSubscription(event) {
    event.preventDefault();
    
    if (!validateSubscriptionForm()) {
        return;
    }
    
    const submitBtn = document.getElementById('subscribeBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    try {
        const formData = new FormData(event.target);
        const subscriptionData = {
            fullName: formData.get('fullName'),
            phoneNumber: formData.get('phoneNumber'),
            plan: formData.get('plan'),
            mealTypes: formData.getAll('mealTypes'),
            deliveryDays: formData.getAll('deliveryDays'),
            allergies: formData.get('allergies') || 'None specified'
        };
        
        const selectedPlan = document.querySelector('input[name="plan"]:checked');
        const planPrice = parseInt(selectedPlan.dataset.price);
        const finalPrice = planPrice * subscriptionData.mealTypes.length * subscriptionData.deliveryDays.length * 4;
        subscriptionData.totalPrice = Math.round(finalPrice);
        
        // Send ke backend databse
        const response = await fetch('backend/SeaCatering_SubmitSubscription.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(subscriptionData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(`🎉 Subscription submitted successfully!\n\nName: ${subscriptionData.fullName}\nPhone: ${subscriptionData.phoneNumber}\nPlan: ${subscriptionData.plan.charAt(0).toUpperCase() + subscriptionData.plan.slice(1)} Plan\nMeal Types: ${subscriptionData.mealTypes.join(', ')}\nDelivery Days: ${subscriptionData.deliveryDays.join(', ')}\nTotal Price: Rp ${subscriptionData.totalPrice.toLocaleString()}\n\nThank you for choosing SEA Catering!`);
            
            event.target.reset();
            document.getElementById('priceSummary').style.display = 'none';
            document.getElementById('mealTypesError').style.display = 'none';
            document.getElementById('deliveryDaysError').style.display = 'none';
            
        } else {
            throw new Error(result.error || 'Subscription failed');
        }
        
    } catch (error) {
        console.error('Subscription Error:', error);
        alert('❌ Error submitting subscription: ' + error.message + '\n\nPlease try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Subscribe Now';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    displayMeals();
    updateCartDisplay();
    displayTestimonials();
    
    if (testimonials.length > 1) {
        setInterval(nextTestimonial, 5000);
    }
    
    if (document.getElementById('subscriptionForm')) {
        // Add event listeners
        const planInputs = document.querySelectorAll('input[name="plan"]');
        const mealTypeInputs = document.querySelectorAll('input[name="mealTypes"]');
        const deliveryDayInputs = document.querySelectorAll('input[name="deliveryDays"]');
        
        planInputs.forEach(input => input.addEventListener('change', calculatePrice));
        mealTypeInputs.forEach(input => input.addEventListener('change', calculatePrice));
        deliveryDayInputs.forEach(input => input.addEventListener('change', calculatePrice));
        
        calculatePrice();
    }
});

