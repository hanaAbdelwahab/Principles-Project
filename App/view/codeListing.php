
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Car Listings | Fillo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet" />

  <link href="<?= BASE_URL ?>/Public/css/codeListing.css" rel="stylesheet"/>
<style>
.search-box {
  background: rgba(158, 158, 158, 0.3); /* dark background with opacity */
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white; /* light text for dark background */
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.9);
}

.search-box .form-label,
.search-box .btn {
font-size:1.2rem;
  font-family:"Cambria";
  color:white;
}

.search-box .form-select,
.search-box input {
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid rgb(39, 73, 176);
}

.search-box .form-select:focus,
.search-box input:focus {
  background-color: rgba(255, 255, 255, 0.15);
  border-color: #6c757d;
  color: black;
}

.search-box .btn {
  background-color:rgb(39, 73, 176);
  border: none;
}

.search-box .btn:hover {
  background-color:#3366ff;
}

</style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-5">
  <a class="navbar-brand" href="#">fillo</a>
  <div class="collapse navbar-collapse w-100">
    <ul class="navbar-nav mx-auto justify-content-center">
  <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Cars</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
</ul>

    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fa-solid fa-user fa-lg text-white"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li class="px-3 py-2 border-bottom d-flex align-items-center">
            <div class="avatar-circle">H</div>
            <div>
              <strong>Hana Abdelwahab</strong><br>
              <small>hana.m.abdelwahab3@gmail.com</small>
            </div>
          </li>
          <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> View profile</a></li>
          <li><a class="dropdown-item" href="#"><i class="fa-solid fa-clock-rotate-left me-2"></i> History</a></li>
          <li><a class="dropdown-item" href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>


<div class="container mt-4">
  <div class="row">
    <!-- Sidebar Filter (Left) -->
    <div class="col-md-3">
      <div class="search-box shadow-sm p-3" style="min-height: 100%; border-radius: 10px;">
  <form method="GET" action="">

    <!-- Car Brand -->
    <div class="mb-3">
      <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
        <span>Car Brand</span>
        <i class="fas fa-chevron-down rotate-icon"></i>
      </label>
      <div class="toggle-body">
        <select class="form-select" name="brand">
          <option disabled <?= !isset($_GET['brand']) ? 'selected' : '' ?>>Choose...</option>
          <?php foreach ($brands as $brand): ?>
            <option value="<?= htmlspecialchars($brand) ?>" <?= (isset($_GET['brand']) && $_GET['brand'] === $brand) ? 'selected' : '' ?>>
              <?= htmlspecialchars($brand) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Pickup Location -->
    <div class="mb-3">
      <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
        <span>Pickup Location</span>
        <i class="fas fa-chevron-down rotate-icon"></i>
      </label>
      <div class="toggle-body">
        <select class="form-select" name="location">
          <option disabled <?= !isset($_GET['location']) ? 'selected' : '' ?>>Choose...</option>
          <?php foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>" <?= (isset($_GET['location']) && $_GET['location'] === $loc) ? 'selected' : '' ?>>
              <?= htmlspecialchars($loc) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Pickup Date & Time -->
    <div class="mb-3">
      <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
        <span>Pickup Date & Time</span>
        <i class="fas fa-chevron-down rotate-icon"></i>
      </label>
      <div class="toggle-body">
        <div class="d-flex gap-2">
          <input
  type="date"
  class="form-control"
  name="pickup_date"
  id="pickupDate"
  min="<?= $dateRange['min_start'] ?>"
  max="<?= $dateRange['max_end'] ?>"
  value="<?= htmlspecialchars($_GET['pickup_date'] ?? '') ?>"
/>
          <input type="time" class="form-control" name="pickup_time" />
        </div>
      </div>
    </div>

    <!-- Drop-off Date & Time -->
    <div class="mb-3">
      <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
        <span>Drop-off Date & Time</span>
        <i class="fas fa-chevron-down rotate-icon"></i>
      </label>
      <div class="toggle-body">
        <div class="d-flex gap-2">
         <input
  type="date"
  class="form-control"
  name="dropoff_date"
  id="dropoffDate"
  min="<?= $dateRange['min_start'] ?>"
  max="<?= $dateRange['max_end'] ?>"
  value="<?= htmlspecialchars($_GET['dropoff_date'] ?? '') ?>"
/>
          <input type="time" class="form-control" name="dropoff_time" />
        </div>
      </div>
    </div>

    <!-- Car Color -->
<div class="mb-3">
  <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
    <span>Car Color</span>
    <i class="fas fa-chevron-down rotate-icon"></i>
  </label>
  <div class="toggle-body">
    <select class="form-select" name="color">
  <option disabled <?= !isset($_GET['color']) ? 'selected' : '' ?>>Choose color...</option>
  <?php foreach ($colors as $color): ?>
    <option value="<?= htmlspecialchars($color) ?>" <?= (isset($_GET['color']) && $_GET['color'] === $color) ? 'selected' : '' ?>>
      <?= htmlspecialchars(ucfirst($color)) ?>
    </option>
  <?php endforeach; ?>
</select>

  </div>
</div>


<!-- Price Range -->
<div class="mb-3">
  <label class="form-label d-flex justify-content-between align-items-center toggle-header" style="cursor: pointer;">
    <span>Max Price ($)</span>
    <i class="fas fa-chevron-down rotate-icon"></i>
  </label>
  <div class="toggle-body">
    <div class="dual-range-wrapper position-relative" style="background-color:transparent;">
      <div class="d-flex justify-content-between mb-1" style="background-color:transparent;">
       <div class="d-flex align-items-center gap-2 mb-2" style="background-color:transparent;">
  <label for="maxPriceInput" class="form-label mb-0" style="background-color:transparent;">Up to ($):</label>
  <input type="number"
         id="maxPriceInput"
         name="max_price"
         min="0"
         max="100000"
         step="10"
         value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : 0 ?>"
         class="form-control form-control-sm"
         style="width: 100px;" />
</div>
      <input type="range" id="maxPrice" min="0" max="100000" step="10"  value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : 0 ?>" class="dual-range" />
    </div>
  </div>
      </div>
      </div>

    <!-- Filter Buttons -->
    <button class="btn btn-primary w-100 mt-3">Apply Filters</button>
    <?php if (!empty($_GET)): ?>
      <a href="index.php" class="btn btn-danger w-100 mt-2" style="background-color:rgba(207, 1, 1, 0.675);">Reset Filters</a>
    <?php endif; ?>
  </form>
</div>
    </div>
<!-- Listings (Right) -->
<div class="col-md-9">
  <div class="row g-4">
    <?php foreach ($cars as $car): ?>
      <div class="col-md-4" >
        <div class="card-wrapper" >
          <div class="card glass" >
            <div class="card__image-holder" >
              <img class="card__image" src="<?= BASE_URL ?>/Public/images/<?= htmlspecialchars($car->image_filename) ?>" alt="<?= htmlspecialchars($car->name) ?>" />
            </div>
            <div class="card-title" style="color:white;">
              <a href="#" class="toggle-info btn">
                <span class="left"></span>
                <span class="right"></span>
              </a>
              <h2><?= htmlspecialchars($car->name) . ' ' . htmlspecialchars($car->model) ?>
                <small><?= htmlspecialchars($car->year) ?></small>
              </h2>
            </div>
             <div class="threeinfo d-flex justify-content-start align-items-center gap-3 text-secondary small mt-1 ps-1" style="color:white;">
             <div><i class="fas fa-star me-1"></i> <?= htmlspecialchars($car->rating ?? '4.5') ?></div>
             <div><i class="fas fa-user-group me-1"></i> <?= htmlspecialchars($car->renter_count ?? '123') ?></div>
             <div><i class="fas fa-dollar-sign me-1"></i> <?= htmlspecialchars($car->price_per_day) ?>/day</div>
            </div>
           <div class="card-flap flap1">
  <div class="card-description text-secondary small px-2 py-1" style="color:white;">
    <div><i class="fas fa-bolt me-1"></i> <strong>Power: </strong><?= htmlspecialchars($car->power_type ?? 'Electric') ?></div>
    <div><i class="fas fa-palette me-1"></i> <strong>Color:</strong> <?= htmlspecialchars($car->color ?? 'Unknown') ?></div>
    <div><i class="fas fa-map-marker-alt me-1"></i><strong> Location: </strong><?= htmlspecialchars($car->location ?? 'N/A') ?></div>
  </div>
  <div class="card-flap flap2">
    <div class="card-actions text-center">
     <a href="#" class="btn btn-outline-primary read-more-btn"
   data-bs-toggle="modal"
   data-bs-target="#carModal"
   data-name="<?= htmlspecialchars($car->name . ' ' . $car->model) ?>"
   data-image="<?= BASE_URL ?>/Public/images/<?= htmlspecialchars($car->image_filename) ?>"
   data-desc="<?= htmlspecialchars($car->description) ?>"
   data-price="<?= htmlspecialchars($car->price_per_day) ?>"
   data-color="<?= htmlspecialchars($car->color) ?>"
   data-transmission="<?= htmlspecialchars($car->transmission_type) ?>"
   data-power="<?= htmlspecialchars($car->power_type) ?>"
   data-location="<?= htmlspecialchars($car->location) ?>"
   data-wheels="<?= htmlspecialchars($car->wheels) ?>"
data-brakes="<?= htmlspecialchars($car->brakes) ?>">

  Read More
</a>

    </div>
  </div>
</div>
    </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


  </div>
</div>
<!-- Car Display Modal -->
<div class="modal fade" id="carModal" tabindex="-1" aria-labelledby="carModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass p-4 shadow-lg border-0 rounded-4">
  <!-- Close Button -->
  <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

      <div class="modal-body">
        <div class="row align-items-start">
          
          <!-- Left Section -->
          <div class="col-md-6 text-white">
            <h2 id="modalCarName" class="fw-bold mb-3" style="color:black; font-size:2rem; fint-weight:700;">Car Name</h2>
            <p id="modalCarDesc" class="text-light-emphasis">Description here</p>

            <div class="modal-details-grid mt-4">
  <div class="modal-glass-tag"><i class="fas fa-palette"></i> <span id="modalColor">Red</span></div>
  <div class="modal-glass-tag"><i class="fas fa-cogs"></i> <span id="modalTransmission">Auto</span></div>
  <div class="modal-glass-tag"><i class="fas fa-bolt"></i> <span id="modalPower">Electric</span></div>
  <div class="modal-glass-tag"><i class="fas fa-map-marker-alt"></i> <span id="modalLocation">Cairo</span></div>
</div>

<!-- Configuration Section -->
<div class="mt-4">
  <h5 class="fw-bold" style="color:#464646">Configuration</h5>
  <div class=" confDivv d-flex justify-content-between border-bottom py-2">
    <span class="fw-semibold">Wheels</span>
    <span id="modalWheels" class="text-muted">Not specified</span>
  </div>
  <div class=" confDivv d-flex justify-content-between border-bottom py-2">
    <span class="fw-semibold">Brakes</span>
    <span id="modalBrakes" class="text-muted">Not specified</span>
  </div>
</div>
            <h4 class="mt-4 d-flex align-items-center justify-content-center" style="color:#303030; font-size:2rem; fint-weight:700;">Price: $<span id="modalCarPrice">0</span></h4>
            
          </div>

          <!-- Right Section -->
          <!-- Right Section (Car Image) -->
<div class="col-md-6 carModalImg d-flex align-items-center justify-content-center">
  <img id="modalCarImage" class="img-fluid rounded" style="max-height: 400px;" src="" alt="Car Image" />
  <button class="btn btn-light text-dark mt-3" >Rent Now</button>
</div>

        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/Public/js/codeListing.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const slider = document.getElementById('maxPrice');
  const input = document.getElementById('maxPriceInput');

  function updateSliderBackground(value) {
    const min = parseInt(slider.min);
    const max = parseInt(slider.max);
    const percent = ((value - min) / (max - min)) * 100;
    slider.style.background = `linear-gradient(to right, rgb(39, 73, 176) 0%, rgb(39, 73, 176) ${percent}%, #ddd ${percent}%, #ddd 100%)`;
  }

  function syncSliderAndInput() {
    let val = parseInt(input.value);
    if (isNaN(val)) val = 0;
    slider.value = val;
    updateSliderBackground(val);
  }

  input.addEventListener('input', syncSliderAndInput);
  slider.addEventListener('input', () => {
    input.value = slider.value;
    updateSliderBackground(slider.value);
  });

  // Initialize on load
  syncSliderAndInput();

  // Modal logic
  const carModal = document.getElementById('carModal');
  if (carModal) {
    carModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('modalCarName').textContent = button.getAttribute('data-name');
      document.getElementById('modalCarImage').src = button.getAttribute('data-image');
      document.getElementById('modalCarDesc').textContent = button.getAttribute('data-desc');
      document.getElementById('modalCarPrice').textContent = button.getAttribute('data-price');
      document.getElementById('modalColor').textContent = button.getAttribute('data-color') ?? 'Unknown';
      document.getElementById('modalTransmission').textContent = button.getAttribute('data-transmission') ?? 'Manual';
      document.getElementById('modalPower').textContent = button.getAttribute('data-power') ?? 'Gasoline';
      document.getElementById('modalLocation').textContent = button.getAttribute('data-location') ?? 'N/A';
      document.getElementById('modalWheels').textContent = button.getAttribute('data-wheels') ?? 'Not specified';
      document.getElementById('modalBrakes').textContent = button.getAttribute('data-brakes') ?? 'Not specified';
    });
  }
});
</script>


</body>
</html>