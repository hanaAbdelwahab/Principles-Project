
  // Show modal with car details
  const carModal = document.getElementById('carModal');
  carModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const name = button.getAttribute('data-name');
    const image = button.getAttribute('data-image');
    const desc = button.getAttribute('data-desc');
    const price = button.getAttribute('data-price');

    document.getElementById('modalCarName').textContent = name;
    document.getElementById('modalCarImage').src = image;
    document.getElementById('modalCarDesc').textContent = desc;
    document.getElementById('modalCarPrice').textContent = price;
  });
   // Toggle filter collapsible sections
  document.querySelectorAll('.toggle-header').forEach(header => {
    header.addEventListener('click', () => {
      header.classList.toggle('active');
      const body = header.nextElementSibling;
      const currentDisplay = window.getComputedStyle(body).display;
      body.style.display = currentDisplay === 'block' ? 'none' : 'block';
    });
  });

  // Flip cards (Read More button)
  document.querySelectorAll('.toggle-info').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const card = this.closest('.card');
      card.classList.toggle('show');
    });
  });

  // Date validation logic
  const pickupDateInput = document.getElementById('pickupDate');
  const dropoffDateInput = document.getElementById('dropoffDate');

  pickupDateInput.addEventListener('change', () => {
    dropoffDateInput.min = pickupDateInput.value;
    if (dropoffDateInput.value < pickupDateInput.value) {
      dropoffDateInput.value = pickupDateInput.value;
    }
  });

  window.addEventListener('DOMContentLoaded', () => {
    if (pickupDateInput.value) {
      dropoffDateInput.min = pickupDateInput.value;
    }

    // Max Price slider init and color gradient
    const maxPrice = document.getElementById('maxPrice');
    const maxPriceValue = document.getElementById('maxPriceValue');

   function updateSliderBackground(value) {
  const min = parseInt(slider.min);
  const max = parseInt(slider.max);
  const percent = ((value - min) / (max - min)) * 100;
  slider.style.background = `linear-gradient(to right, #308e3f 0%, #308e3f ${percent}%, #ddd ${percent}%, #ddd 100%)`;
}


    maxPrice.addEventListener('input', function () {
      maxPriceValue.textContent = maxPrice.value;
      updateSliderBackground(maxPrice.value);
    });

    const urlParams = new URLSearchParams(window.location.search);
    const maxPriceParam = urlParams.get('max_price');
    const initialValue = maxPriceParam ?? maxPrice.value;

    maxPrice.value = initialValue;
    maxPriceValue.textContent = initialValue;
    updateSliderBackground(initialValue);
  });
  