//CodeListing.js
document.addEventListener('DOMContentLoaded', () => {
  // -------------------- THEME TOGGLE --------------------

  // -------------------- MODAL SETUP --------------------
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

  // -------------------- FILTER SECTION TOGGLE --------------------
  document.querySelectorAll('.toggle-header').forEach(header => {
    header.addEventListener('click', () => {
      header.classList.toggle('active');
      const body = header.nextElementSibling;
      const isVisible = window.getComputedStyle(body).display === 'block';
      body.style.display = isVisible ? 'none' : 'block';
    });
  });

  // -------------------- CARD FLIP --------------------
  document.querySelectorAll('.toggle-info').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const card = this.closest('.card');
      card.classList.toggle('show');
    });
  });

  // -------------------- DATE LOGIC --------------------
document.addEventListener('DOMContentLoaded', function () {
  const pickup = document.getElementById('pickupDate');
  const dropoff = document.getElementById('dropoffDate');

  // Convert PHP-passed arrays
  const unavailableSet = new Set(unavailableDates);
  const minDate = minAvailableDate;
  const maxDate = maxAvailableDate;

  function isUnavailable(dateStr) {
    return unavailableSet.has(dateStr);
  }

  function enforceValidDate(input) {
    input.addEventListener('input', () => {
      const val = input.value;
      if (isUnavailable(val) || val < minDate || val > maxDate) {
        input.classList.add('is-invalid');
        input.value = '';
      } else {
        input.classList.remove('is-invalid');
      }
    });

    input.setAttribute('min', minDate);
    input.setAttribute('max', maxDate);
  }

  if (pickup && dropoff) {
    enforceValidDate(pickup);
    enforceValidDate(dropoff);

    pickup.addEventListener('change', () => {
      dropoff.min = pickup.value;
      if (dropoff.value < pickup.value) {
        dropoff.value = pickup.value;
      }
    });

    // Pre-validate on page load
    if (pickup.value && isUnavailable(pickup.value)) pickup.value = '';
    if (dropoff.value && isUnavailable(dropoff.value)) dropoff.value = '';
  }
});
  // -------------------- PRICE SLIDER --------------------
  const slider = document.getElementById('maxPrice');
  const sliderDisplay = document.getElementById('maxPriceValue');

  function updateSliderBackground(value) {
    const min = parseInt(slider.min);
    const max = parseInt(slider.max);
    const percent = ((value - min) / (max - min)) * 100;
    slider.style.background = `linear-gradient(to right, #308e3f 0%, #308e3f ${percent}%, #ddd ${percent}%, #ddd 100%)`;
  }

  if (slider && sliderDisplay) {
    const urlParams = new URLSearchParams(window.location.search);
    const paramValue = urlParams.get('max_price') ?? slider.value;

    slider.value = paramValue;
    sliderDisplay.textContent = paramValue;
    updateSliderBackground(paramValue);

    slider.addEventListener('input', () => {
      sliderDisplay.textContent = slider.value;
      updateSliderBackground(slider.value);
    });
  }
});
