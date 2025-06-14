// frontend/owner_dashboard.js

document.addEventListener("DOMContentLoaded", () => {
    const list           = document.getElementById("vehicleList");
    const typeFilter     = document.getElementById("typeFilter");
    const locationFilter = document.getElementById("locationFilter");
    const addVehicleForm = document.getElementById("addVehicleForm");
    let vehiclesData     = [];
  
    // 1) Initial fetch of vehicles
    fetchVehicles();
  
    // 2) Handle Add Vehicle form without page reload
    addVehicleForm.addEventListener("submit", e => {
      e.preventDefault();
      const formData = new FormData(addVehicleForm);
  
      fetch("../backend/add_vehicle.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(json => {
        if (json.error) {
          alert(json.error);
        } else {
          alert(json.message);
          addVehicleForm.reset();
          fetchVehicles();  // refresh list including the new vehicle
        }
      })
      .catch(() => alert("❌ Network error"));
    });
  
    // Fetch & render vehicles from backend
    function fetchVehicles() {
      fetch("../backend/get_vehicles.php")
        .then(res => res.json())
        .then(data => {
          vehiclesData = data.vehicles || [];
          populateLocationFilter();
          renderVehicles(vehiclesData);
          typeFilter.onchange     = filterAndRender;
          locationFilter.onchange = filterAndRender;
        })
        .catch(() => {
          list.innerHTML = "<p>❌ Could not load vehicles.</p>";
        });
    }
  
    // Build the location dropdown
    function populateLocationFilter() {
      const locs = [...new Set(vehiclesData.map(v => v.location))];
      locationFilter.innerHTML = `<option value="All">All Locations</option>` +
        locs.map(l => `<option value="${l}">${l}</option>`).join("");
    }
  
    // Apply filters and re-render
    function filterAndRender() {
      const t = typeFilter.value, l = locationFilter.value;
      const filtered = vehiclesData.filter(v =>
        (t === "All" || v.vehicleType === t) &&
        (l === "All" || v.location     === l)
      );
      renderVehicles(filtered);
    }
  
    // Render each vehicle card and start its countdown
    function renderVehicles(arr) {
      list.innerHTML = "";
      if (!arr.length) {
        list.innerHTML = "<p>No vehicles available.</p>";
        return;
      }
      arr.forEach(v => {
        const card = document.createElement("div");
        card.className = "vehicle-card";
        card.innerHTML = `
          <h3>${v.vehicleType} – ${v.model}</h3>
          <p><strong>Price:</strong> ₹${v.price}/hr</p>
          <p><strong>Hours:</strong> ${v.hours}</p>
          <p><strong>Location:</strong> ${v.location}</p>
          <p><strong>Contact:</strong> ${v.contact}</p>
          <p><strong>Time Left:</strong> <span id="timer-${v.id}">Loading...</span></p>
          <button class="book-button" onclick="bookVehicle(${v.id},${v.hours})">
            Book Now
          </button>
        `;
        list.appendChild(card);
  
        // Parse the stored ISO expiry timestamp
        const expiryDate = new Date(v.expiry_iso);
        startCountdown(`timer-${v.id}`, expiryDate, v.id);
      });
    }
  });
  
  // Navigate to the payment page
  function bookVehicle(vehicleId, hours) {
    window.location.href = `/Car-Bike-Sharing-Platform/backend/payment_page.php?vehicleId=${vehicleId}&hours=${hours}`;
  }
  
  // Live countdown until expiry, then remove card
  function startCountdown(elId, expiryDate, vid) {
    const el = document.getElementById(elId);
    const timer = setInterval(() => {
      const diff = expiryDate - new Date();
      if (diff <= 0) {
        clearInterval(timer);
        el.innerText = "Expired";
        const btn = document.querySelector(`button[onclick*="${vid}"]`);
        if (btn) btn.closest(".vehicle-card")?.remove();
        return;
      }
      const h = Math.floor(diff / 3600000),
            m = Math.floor((diff % 3600000) / 60000),
            s = Math.floor((diff % 60000) / 1000);
      el.innerText = `${h}h ${m}m ${s}s`;
    }, 1000);
  }
  