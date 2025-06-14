// 🚀 script.js - Handles API requests & UI interactions

document.addEventListener("DOMContentLoaded", function () {
    // ✅ Get Started button - Redirects to Register Page
    document.getElementById("getStartedBtn")?.addEventListener("click", function () {
        window.location.href = "register.html";
    });

    // ✅ Navigation button functionalities
    document.getElementById("homeBtn")?.addEventListener("click", function () {
        window.location.href = "index.html";
    });

    document.getElementById("aboutBtn")?.addEventListener("click", function () {
        window.location.href = "about.html";
    });

    document.getElementById("servicesBtn")?.addEventListener("click", function () {
        window.location.href = "services.html";
    });

    document.getElementById("contactBtn")?.addEventListener("click", function () {
        window.location.href = "contact.html";
    });

    // ✅ Feature buttons functionality
    document.getElementById("carShareBtn")?.addEventListener("click", function () {
        alert("Car Sharing: Rent your car and earn extra income.");
    });

    document.getElementById("bikeShareBtn")?.addEventListener("click", function () {
        alert("Bike Sharing: Share your bike with trusted users.");
    });

    document.getElementById("secureBtn")?.addEventListener("click", function () {
        alert("Secure & Trusted: We ensure safe and verified transactions.");
    });

    // ✅ Load vehicles on pages where applicable
    if (document.getElementById("vehicleList")) {
        loadVehicles();
    }
});

// ✅ User Registration
document.getElementById("registerForm")?.addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("http://localhost/Car-Bike-Sharing-Platform/backend/register.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || data.error);
        if (data.user) {
            localStorage.setItem("user", JSON.stringify(data.user)); // Save session
            window.location.href = "dashboard.html"; // Redirect
        }
    })
    .catch(error => console.error("Error:", error));
});

// ✅ User Login
document.getElementById("loginForm")?.addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("http://localhost/Car-Bike-Sharing-Platform/backend/login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            localStorage.setItem("user", JSON.stringify(data.user)); // Save session
            window.location.href = "dashboard.html"; // Redirect
        } else {
            alert(data.error);
        }
    })
    .catch(error => console.error("Error:", error));
});

// ✅ Load Available Vehicles
function loadVehicles() {
    fetch("http://localhost/Car-Bike-Sharing-Platform/backend/get_vehicles.php")
    .then(response => response.json())
    .then(data => {
        let vehicleList = document.getElementById("vehicleList");
        if (!vehicleList) return;
        vehicleList.innerHTML = "";
        data.forEach(vehicle => {
            vehicleList.innerHTML += `
                <div class="vehicle">
                    <h3>${vehicle.model} (${vehicle.vehicleType})</h3>
                    <p>Price: ${vehicle.price} per hour</p>
                    <label>Hours: 
                        <select id="hours-${vehicle.id}">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                        </select>
                    </label>
                    <button onclick="bookVehicle(${vehicle.id})">Book Now</button>
                </div>
            `;
        });
    })
    .catch(error => console.error("Error:", error));
}

// ✅ Book a Vehicle
function bookVehicle(vehicleId) {
    let user = JSON.parse(localStorage.getItem("user"));
    if (!user) {
        alert("Please log in to book a vehicle!");
        return;
    }

    let hours = document.getElementById(`hours-${vehicleId}`).value;
    let formData = new FormData();
    formData.append("userId", user.id);
    formData.append("vehicleId", vehicleId);
    formData.append("hours", hours);

    fetch("http://localhost/Car-Bike-Sharing-Platform/backend/book_vehicle.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => alert(data.message || data.error))
    .catch(error => console.error("Error:", error));
}
