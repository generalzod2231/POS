<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Brew & Bean POS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ADDING THIS STYLE BLOCK TO CENTER THE HEADER */
        header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 20px 0;
            background-color: #4A3022;
            color: white;
            width: 100%;
            margin: 0;
        }
        
        .disabled-addins {
            opacity: 0.4;
            pointer-events: none; 
            filter: grayscale(100%); 
        }
    </style>
</head>
<body>

<header>☕ Brew & Bean Kiosk</header>

<div class="container">

    <div class="sidebar" style="display: flex; flex-direction: column; gap: 15px; padding: 15px 10px; align-items: center; background-color: #F9F9F9; min-width: 180px;">
        
        <button class="filter-btn active" onclick="filterCategory('All', this)" style="background-color: #D6C4B3; border: 2px solid #8C6A51; border-radius: 12px; padding: 20px 10px; cursor: pointer; width: 150px; transition: 0.2s; opacity: 1;">
            <span style="font-weight: bold; font-size: 18px; color: #4A3022;">All Menu</span>
        </button>

        <button class="filter-btn" onclick="filterCategory('Hot', this)" style="background-color: transparent; border: none; border-radius: 12px; padding: 20px 10px; cursor: pointer; width: 150px; transition: 0.2s; opacity: 0.6;">
            <span style="font-weight: bold; font-size: 18px; color: #4A3022;">Hot</span>
        </button>

        <button class="filter-btn" onclick="filterCategory('Iced', this)" style="background-color: transparent; border: none; border-radius: 12px; padding: 20px 10px; cursor: pointer; width: 150px; transition: 0.2s; opacity: 0.6;">
            <span style="font-weight: bold; font-size: 18px; color: #4A3022;">Iced</span>
        </button>
    </div>

    <div class="products" id="product-container">
        <p>Loading menu...</p>
    </div>

    <div class="modifiers-panel" id="modifiers-panel" style="width: 360px; background: #F4F1EA; border-left: 1px solid #E0D5C1; padding: 15px; display: flex; flex-direction: column;">
        
        <div id="mod-placeholder" style="text-align: center; color: #bcaaa4; margin-top: 150px;">
            <span style="font-size: 50px;">👈</span>
            <p style="margin-top: 10px; font-weight: 500;">Select a drink from the menu.</p>
        </div>

        <div id="mod-content" style="display: none; flex-direction: column; height: 100%;">
            
            <h3 style="text-align: center; font-size: 14px; margin-bottom: 5px; color: #4A3022;">SIZE</h3>
            
            <div class="size-selector-container">
                <button class="mod-size-btn" id="btn-size-S" onclick="setModifierSize('Small', 0)">
                    <div class="cup-container">
                        <img id="cup-img-S" src="small.png" alt="Small" style="height: 35px;">
                    </div>
                    <span>Small</span>
                </button>
                
                <button class="mod-size-btn active" id="btn-size-M" onclick="setModifierSize('Medium', 20)">
                    <div class="cup-container">
                        <img id="cup-img-M" src="medium.png" alt="Medium" style="height: 48px;">
                    </div>
                    <span>Medium</span>
                </button>

                <button class="mod-size-btn" id="btn-size-L" onclick="setModifierSize('Large', 40)">
                    <div class="cup-container">
                        <img id="cup-img-L" src="large.png" alt="Large" style="height: 60px;">
                    </div>
                    <span>Large</span>
                </button>
            </div>

            <div class="item-info-box">
                <div class="item-info-row">
                    <span><strong>Item:</strong> <span id="mod-name">Iced Latte</span></span>
                </div>
                <div class="item-info-row">
                    <span><strong>Size:</strong> <span id="mod-size-text">Medium</span></span>
                    <span><strong>Price:</strong> <span id="mod-price">₱0.00</span></span>
                </div>
            </div>

            <div class="add-ins-container" id="add-ins-wrapper">
                <div class="add-in-title">ADD-INS</div>
                
                <div class="modifier-toggles">
                    <button class="mod-toggle-btn" onclick="setToggle(this, 'No')">No</button>
                    <button class="mod-toggle-btn active" onclick="setToggle(this, 'Add')">Add</button>
                    <button class="mod-toggle-btn" onclick="setToggle(this, 'Lite')">Lite</button>
                    <button class="mod-toggle-btn" onclick="setToggle(this, 'Extra')">Extra</button>
                </div>

                <div class="add-ins-grid" id="add-ins-grid">
                    <p style="text-align:center; font-size:12px;">Loading add-ins...</p>
                </div>
            </div>

            <button onclick="confirmCurrentItem()" style="margin-top: auto; width: 100%; padding: 18px; background: #8C6A51; color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.2s;">
                Confirm Item
            </button>
        </div>
    </div>

    <div class="cart">
        <div class="receipt-header">
            <h2>☕ Brew & Bean</h2>
            <p>Main Branch POS</p>
            <p id="receipt-date">Date: Loading...</p>
        </div>
        
        <div class="dashed-line"></div>
        
        <ul id="cart-list"></ul>
        
        <div class="dashed-line"></div>
        
        <div class="total-box">
            <span>Total</span>
            <span>₱<span id="total">0.00</span></span>
        </div>
        
        <button class="checkout-btn" onclick="openOrderTypeModal()">Checkout</button>
    </div>

</div>

<div id="orderTypeModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeOrderTypeModal()">&times;</span>
        <h2 style="color: #3e2723; margin-bottom: 5px;">Checkout Order</h2>
        
        <div style="margin: 20px 0; text-align: left;">
            <label for="customerName" style="display: block; font-weight: bold; color: #5a3d2b; margin-bottom: 8px;">Customer Name:</label>
            <input type="text" id="customerName" placeholder="e.g. Sarah, John..." style="width: 100%; padding: 12px; border: 2px solid #ccc; border-radius: 6px; font-size: 16px; box-sizing: border-box; font-family: 'Poppins', sans-serif;">
        </div>

        <p style="color: #757575; font-size: 14px; margin-bottom: 15px;">How would you like your order?</p>
        
        <div class="size-options" style="display: flex; flex-direction: row; gap: 15px;">
            <button class="size-btn" style="flex: 1; justify-content: center; font-size: 18px; padding: 20px;" onclick="submitOrder('Dine In')">🍽️ Dine In</button>
            <button class="size-btn" style="flex: 1; justify-content: center; font-size: 18px; padding: 20px;" onclick="submitOrder('Takeout')">🥡 Takeout</button>
        </div>
    </div>
</div>

<script>
document.getElementById('receipt-date').innerText = "Date: " + new Date().toLocaleDateString();

let currentCategory = 'All'; 
let cart = [];

let activeCartIndex = -1; 
let activeToggle = 'Add'; 

function handleProductClick(id, name, basePrice, hasSizes) {
    document.getElementById('mod-placeholder').style.display = 'none';
    document.getElementById('mod-content').style.display = 'flex';
    
    let newItem = {
        id: id,
        baseName: name,
        hasSizes: hasSizes,
        size: hasSizes == 1 ? 'Medium' : '',
        basePrice: parseFloat(basePrice),
        extraPrice: hasSizes == 1 ? 20 : 0, 
        addIns: [],
        name: name + (hasSizes == 1 ? ' - Medium' : ''),
        totalPrice: parseFloat(basePrice) + (hasSizes == 1 ? 20 : 0)
    };

    cart.push(newItem);
    activeCartIndex = cart.length - 1; 

    document.querySelectorAll('.add-in-item').forEach(btn => {
        btn.classList.remove('active');
        let base = btn.getAttribute('data-base');
        if(base) btn.querySelector('.add-in-name').innerText = base;
    });
    setToggle(document.querySelectorAll('.mod-toggle-btn')[1], 'Add');

    if (hasSizes == 1) {
        document.querySelector('.size-selector-container').style.display = 'flex';
        updateSizeButtons('Medium');
    } else {
        document.querySelector('.size-selector-container').style.display = 'none';
    }

    let lowerName = name.toLowerCase();

    // --- DYNAMIC CUP IMAGES ---
    // Check if it's an Iced drink
    let isColdDrink = (currentCategory === 'Iced') || lowerName.includes('iced') || lowerName.includes('frappe') || lowerName.includes('frappuccino') || lowerName.includes('nitro');
    
    // Grab the image elements
    let cupImgS = document.getElementById('cup-img-S');
    let cupImgM = document.getElementById('cup-img-M');
    let cupImgL = document.getElementById('cup-img-L');

    // Swap the images!
    if (isColdDrink) {
        cupImgS.src = 'icedsmall.png';
        cupImgM.src = 'icedmedium.png';
        cupImgL.src = 'icedlarge.png';
    } else {
        cupImgS.src = 'small.png';
        cupImgM.src = 'medium.png';
        cupImgL.src = 'large.png';
    }
    // -------------------------------

    let addInsWrapper = document.getElementById('add-ins-wrapper');
    let isBlendedCoffeeBag = lowerName.includes('blend coffee');
    
    if (isBlendedCoffeeBag) {
        addInsWrapper.classList.add('disabled-addins'); 
    } else {
        addInsWrapper.classList.remove('disabled-addins'); 
    }

    updateLiveUI();
    renderCart(); 
}

function setModifierSize(size, extraPrice) {
    if(activeCartIndex === -1) return; 
    let item = cart[activeCartIndex];

    item.size = size;
    item.extraPrice = extraPrice;
    
    updateSizeButtons(size);
    recalculateActiveItem();
    updateLiveUI();
    renderCart(); 
}

function updateSizeButtons(size) {
    document.querySelectorAll('.mod-size-btn').forEach(btn => btn.classList.remove('active'));
    let sizeChar = size.charAt(0); 
    let activeBtn = document.getElementById('btn-size-' + sizeChar);
    if(activeBtn) activeBtn.classList.add('active');
}

function setToggle(clickedBtn, modifierType) {
    document.querySelectorAll('.mod-toggle-btn').forEach(btn => btn.classList.remove('active'));
    clickedBtn.classList.add('active');
    activeToggle = modifierType;
}

function toggleAddIn(clickedBtn, baseName, standardPrice) {
    if(activeCartIndex === -1) return;
    let item = cart[activeCartIndex];

    let existingIndex = item.addIns.findIndex(a => a.baseName === baseName);

    if (existingIndex > -1) {
        item.addIns.splice(existingIndex, 1);
        clickedBtn.classList.remove('active');
        clickedBtn.querySelector('.add-in-name').innerText = baseName; 
    } else {
        let addInPrice = 0;
        standardPrice = parseFloat(standardPrice);

        if (activeToggle === 'No' || activeToggle === 'Lite') {
            addInPrice = 0;
        } else if (activeToggle === 'Add') {
            addInPrice = standardPrice;
        } else if (activeToggle === 'Extra') {
            addInPrice = standardPrice === 0 ? 10 : standardPrice * 2; 
        }

        item.addIns.push({
            baseName: baseName,
            displayName: activeToggle + ' ' + baseName,
            price: addInPrice
        });

        clickedBtn.classList.add('active');
        clickedBtn.querySelector('.add-in-name').innerText = activeToggle + ' ' + baseName;
    }
    
    recalculateActiveItem();
    updateLiveUI();
    renderCart(); 
}

function recalculateActiveItem() {
    if(activeCartIndex === -1) return;
    let item = cart[activeCartIndex];
    
    item.name = item.baseName + (item.size ? ' - ' + item.size : '');
    item.totalPrice = item.basePrice + item.extraPrice;
    item.addIns.forEach(addon => item.totalPrice += addon.price);
}

function updateLiveUI() {
    if(activeCartIndex === -1) return;
    let item = cart[activeCartIndex];
    document.getElementById('mod-name').innerText = item.baseName;
    document.getElementById('mod-size-text').innerText = item.size || 'Regular';
    document.getElementById('mod-price').innerText = '₱' + item.totalPrice.toFixed(2);
}

function confirmCurrentItem() {
    activeCartIndex = -1;
    document.getElementById('mod-placeholder').style.display = 'block';
    document.getElementById('mod-content').style.display = 'none';
    renderCart(); 
}

function renderCart() {
    let cartList = document.getElementById("cart-list");
    let totalSpan = document.getElementById("total");
    
    cartList.innerHTML = "";
    let grandTotal = 0;

    cart.forEach((item, index) => {
        grandTotal += item.totalPrice;
        let li = document.createElement("li");
        
        let isActive = (index === activeCartIndex);
        let rowStyle = isActive ? "background: #f0fdf4; border: 1.5px solid #2ecc71; border-radius: 6px; padding: 5px;" : "padding: 5px;";
        
        let itemHTML = `
            <div style="${rowStyle}">
                <div class="receipt-item">
                    <span>${item.name}</span>
                    <span>₱${(item.basePrice + item.extraPrice).toFixed(2)}</span>
                </div>
        `;
        
        item.addIns.forEach(addon => {
            let displayPrice = addon.price > 0 ? '+₱' + addon.price.toFixed(2) : '₱0.00';
            itemHTML += `
                <div class="receipt-addin">
                    <span>${addon.displayName}</span>
                    <span>${displayPrice}</span>
                </div>
            `;
        });
        
        itemHTML += `
                <div style="text-align: right;">
                    <span class="remove-link" onclick="removeFromCart(${index})">Remove</span>
                </div>
            </div>
        `;
        
        li.innerHTML = itemHTML;
        cartList.appendChild(li);
    });

    totalSpan.innerText = grandTotal.toFixed(2);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    if (activeCartIndex === index) {
        confirmCurrentItem(); 
    } else if (activeCartIndex > index) {
        activeCartIndex--;
    }
    renderCart();
}

function openOrderTypeModal() {
    if(cart.length === 0) { alert("Cart is empty!"); return; }
    document.getElementById('orderTypeModal').style.display = 'flex';
    setTimeout(() => document.getElementById('customerName').focus(), 100); 
}

function closeOrderTypeModal() {
    document.getElementById('orderTypeModal').style.display = 'none';
    document.getElementById('customerName').value = ""; 
}

window.onclick = function(event) {
    if (event.target == document.getElementById('orderTypeModal')) closeOrderTypeModal();
}

function filterCategory(category, buttonElement) {
    currentCategory = category;
    
    // Dim all buttons and remove styling
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.backgroundColor = 'transparent';
        btn.style.border = 'none';
        btn.style.opacity = '0.6'; 
    });
    
    // Highlight the clicked button
    buttonElement.classList.add('active');
    buttonElement.style.backgroundColor = '#D6C4B3';
    buttonElement.style.border = '2px solid #8C6A51';
    buttonElement.style.opacity = '1';
    
    loadProducts();
}

function loadProducts() {
    fetch('load_products.php?category=' + encodeURIComponent(currentCategory) + '&t=' + new Date().getTime())
        .then(response => response.text())
        .then(html => document.getElementById('product-container').innerHTML = html);
}

function loadAddIns() {
    fetch('load_addins.php?t=' + new Date().getTime())
        .then(response => response.text())
        .then(html => {
            document.getElementById('add-ins-grid').innerHTML = html;
            if (activeCartIndex !== -1) {
                let item = cart[activeCartIndex];
                document.querySelectorAll('.add-in-item').forEach(btn => {
                    let base = btn.getAttribute('data-base');
                    let activeAddon = item.addIns.find(a => a.baseName === base);
                    if (activeAddon) {
                        btn.classList.add('active');
                        btn.querySelector('.add-in-name').innerText = activeAddon.displayName;
                    }
                });
            }
        });
}

loadProducts(); 
loadAddIns();
setInterval(() => {
    loadProducts();
    loadAddIns();
}, 3000);

function submitOrder(orderType) {
    let custName = document.getElementById('customerName').value.trim();
    
    if (custName === "") { 
        alert("Please enter your name before selecting Dine In or Takeout.");
        document.getElementById('customerName').focus(); 
        return; 
    }

    let payload = {
        type: orderType,   
        customer: custName, 
        items: cart        
    };

    fetch("checkout.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        location.reload();
    });
}
</script>

</body>
</html>