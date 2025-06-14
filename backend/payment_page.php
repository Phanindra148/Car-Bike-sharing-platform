<?php
session_start();
include 'db.php';

if (!isset($_GET['vehicleId'])) {
    echo "Invalid request.";
    exit();
}

$vehicleId = intval($_GET['vehicleId']);

// Fetch vehicle details and expiry
$stmt = $conn->prepare("
    SELECT vehicleType, model, location, contact, price,
           DATE_FORMAT(expiry, '%Y-%m-%dT%H:%i:%s') AS expiry_iso
    FROM vehicles
    WHERE id = ? AND available = 1
");
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "Vehicle not found or unavailable.";
    exit();
}

$vehicle    = $result->fetch_assoc();
$expiry_iso = $vehicle['expiry_iso'];

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment | Car & Bike Sharing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../frontend/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
    }

    nav {
      display: flex;
      justify-content: center;
      padding: 15px 0;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      position: relative;
    }

    nav ul li a::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -5px;
      width: 0%;
      height: 2px;
      background-color: #D4AF37;
      transition: width 0.3s ease;
    }

    nav ul li a:hover::after {
      width: 100%;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      padding: 40px 20px;
    }

    .card {
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 30px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      animation: fadeIn 0.6s ease-out both;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .card h2 {
      text-align: center;
      color: #D4AF37;
      margin-bottom: 20px;
    }

    .card p, .card h3 {
      margin: 10px 0;
    }

    .card form button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background-color: #D4AF37;
      border: none;
      border-radius: 8px;
      color: #000;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s;
    }

    .card form button:hover {
      background-color: #c8a437;
      transform: scale(1.03);
    }

    #timer.pulse {
      animation: pulse 1s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50%      { transform: scale(1.1); }
    }

    footer {
      text-align: center;
      padding: 15px;
      background-color: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      color: #aaa;
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav>
    <ul>
      <li><a href="../frontend/index.html">🏠 Home</a></li>
      <li><a href="../frontend/add_vehicle.html">➕ Add Vehicle</a></li>
      <li><a href="../frontend/user_dashboard.html">🚘 Book Vehicle</a></li>
      <li><a href="../frontend/login.html">🔐 Login</a></li>
      <li><a href="../frontend/register.html">📝 Register</a></li>
    </ul>
  </nav>

  <!-- Main Content -->
  <div class="container">
    <div class="card">
      <h2>Payment Confirmation</h2>
      <p><strong>Type:</strong> <?= htmlspecialchars($vehicle['vehicleType']) ?></p>
      <p><strong>Model:</strong> <?= htmlspecialchars($vehicle['model']) ?></p>
      <p><strong>Location:</strong> <?= htmlspecialchars($vehicle['location']) ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($vehicle['contact']) ?></p>
      <p><strong>Rate:</strong> ₹<?= number_format($vehicle['price'], 2) ?>/hr</p>
      <p><strong>Time Left:</strong> <span id="timer">Loading...</span></p>
      <h3>Total Amount: ₹<span id="total">0.00</span></h3>

      <form method="POST" action="confirm_booking.php" id="paymentForm">
        <input type="hidden" name="vehicleId" value="<?= $vehicleId ?>">
        <input type="hidden" name="amount" id="amountInput" value="">
        <button type="submit">Pay & Confirm</button>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    &copy; 2025 Car & Bike Sharing Platform. All rights reserved.
  </footer>

  <script>
    const expiry = new Date("<?= $expiry_iso ?>");
    const rate = <?= json_encode((float)$vehicle['price']) ?>;
    const timerEl = document.getElementById("timer");
    const totalEl = document.getElementById("total");
    const amountInput = document.getElementById("amountInput");

    function updatePaymentInfo() {
      const now = new Date();
      let diff = expiry - now;
      if (diff <= 0) {
        timerEl.innerText = "Expired";
        totalEl.innerText = "0.00";
        amountInput.value = "0.00";
        timerEl.classList.remove("pulse");
        return;
      }

      const hoursFrac = diff / 3600000;
      const h = Math.floor(hoursFrac);
      const m = Math.floor((hoursFrac - h) * 60);
      const s = Math.floor((hoursFrac * 3600 % 60));

      timerEl.innerText = `${h}h ${m}m ${s}s`;

      if (diff < 10 * 60 * 1000) {
        timerEl.classList.add("pulse");
      }

      const costFixed = (rate * hoursFrac).toFixed(2);
      totalEl.innerText = costFixed;
      amountInput.value = costFixed;
    }

    updatePaymentInfo();
    setInterval(updatePaymentInfo, 1000);
  </script>

</body>
</html>
