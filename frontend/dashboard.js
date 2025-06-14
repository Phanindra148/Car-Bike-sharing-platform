// Fetch Available Vehicles
fetch("../backend/get_vehicles.php")
  .then(response => response.json())
  .then(data => {
    const container = document.getElementById("vehicles");
    if (data.length === 0) {
      container.innerHTML = "<p>No vehicles available.</p>";
      return;
    }

    data.forEach(vehicle => {
      const card = document.createElement("div");
      card.className = "vehicle-card";
      card.innerHTML = `
        <h3>${vehicle.model} (${vehicle.vehicleType})</h3>
        <p>Price: ₹${vehicle.price}/hr</p>
        <p>Available: ${vehicle.hours} hrs</p>
        <p>Location: ${vehicle.location}</p>
        <p>Contact: ${vehicle.contact}</p>
        <button onclick="bookVehicle(${vehicle.id})" class="btn">Book Now</button>
      `;
      container.appendChild(card);
    });
  })
  .catch(err => {
    console.error("Error loading vehicles:", err);
    document.getElementById("vehicles").innerHTML = "<p>Unable to load vehicles.</p>";
  });

// Book Vehicle
function bookVehicle(vehicleId) {
  const user = JSON.parse(localStorage.getItem("user"));
  if (!user) {
    alert("Please log in first.");
    window.location.href = "login.html";
    return;
  }

  const formData = new FormData();
  formData.append("userId", user.id);
  formData.append("vehicleId", vehicleId);
  formData.append("hours", 2);

  fetch("../backend/book_vehicle.php", {
    method: "POST",
    body: formData,
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message || data.error);
    location.reload();
  });
}

// Add Vehicle
document.getElementById("addVehicleForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const user = JSON.parse(localStorage.getItem("user"));
  if (!user) {
    alert("Please log in first.");
    return;
  }

  const formData = new FormData();
  formData.append("ownerId", user.id);
  formData.append("vehicleType", document.getElementById("vehicleType").value);
  formData.append("model", document.getElementById("model").value);
  formData.append("price", document.getElementById("price").value);
  formData.append("hours", document.getElementById("hours").value);
  formData.append("location", document.getElementById("location").value);
  formData.append("contact", document.getElementById("contact").value);

  fetch("../backend/add_vehicle.php", {
    method: "POST",
    body: formData,
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message || data.error);
    location.reload();
  });
});

// Logout
document.getElementById("logoutBtn").addEventListener("click", () => {
  localStorage.removeItem("user");
  fetch("../backend/logout.php").then(() => {
    window.location.href = "login.html";
  });
});
