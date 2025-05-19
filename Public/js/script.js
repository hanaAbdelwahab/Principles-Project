// Design Patterns and SOLID Principles Implementation
// This script follows Factory, Strategy, Facade, and MVC patterns
// along with SOLID principles for clean, maintainable code

// =============================================
// Models (Data Layer)
// =============================================

// Car Factory Pattern - Creates different types of cars
class CarFactory {
  static createCar(type, data) {
    switch (type.toLowerCase()) {
      case "suv":
        return new SUV(data)
      case "sedan":
        return new Sedan(data)
      case "electric":
        return new ElectricCar(data)
      default:
        throw new Error(`Car type ${type} not supported`)
    }
  }
}

// Base Car class
class Car {
  constructor(data) {
    this.id = data.id || this._generateId()
    this.name = data.name
    this.price = data.price
    this.seats = data.seats
    this.transmission = data.transmission
    this.fuel = data.fuel
    this.status = data.status || "Available"
    this.image = data.image || this._getDefaultImage()
  }

  _generateId() {
    return Math.random().toString(36).substring(2, 9)
  }

  _getDefaultImage() {
    // Return static images based on car name
    const carNameLower = this.name.toLowerCase()

    if (carNameLower.includes("rav4")) {
      return "https://images.unsplash.com/photo-1581540222194-0def2dda95b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (carNameLower.includes("civic")) {
      return "https://images.unsplash.com/photo-1590362891991-f776e747a588?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (carNameLower.includes("tesla") || carNameLower.includes("model 3")) {
      return "https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (carNameLower.includes("explorer")) {
      return "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (carNameLower.includes("bmw") || carNameLower.includes("3 series")) {
      return "https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (carNameLower.includes("leaf")) {
      return "https://images.unsplash.com/photo-1593055357429-62eaf3b259cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (this.constructor.name === "SUV") {
      return "https://images.unsplash.com/photo-1568844293986-8d0400bd4745?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (this.constructor.name === "Sedan") {
      return "https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    } else if (this.constructor.name === "ElectricCar") {
      return "https://images.unsplash.com/photo-1619767886558-efdc7e9e5fa2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
    }

    // Default fallback image
    return "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
  }

  toJSON() {
    return {
      id: this.id,
      name: this.name,
      type: this.constructor.name,
      price: this.price,
      seats: this.seats,
      transmission: this.transmission,
      fuel: this.fuel,
      status: this.status,
      image: this.image,
    }
  }
}

// Specific Car Types
class SUV extends Car {
  constructor(data) {
    super(data)
    this.groundClearance = data.groundClearance || "High"
    this.offRoadCapability = data.offRoadCapability || true
  }

  toJSON() {
    return {
      ...super.toJSON(),
      groundClearance: this.groundClearance,
      offRoadCapability: this.offRoadCapability,
    }
  }
}

class Sedan extends Car {
  constructor(data) {
    super(data)
    this.trunkSpace = data.trunkSpace || "Large"
    this.fuelEfficiency = data.fuelEfficiency || "Good"
  }

  toJSON() {
    return {
      ...super.toJSON(),
      trunkSpace: this.trunkSpace,
      fuelEfficiency: this.fuelEfficiency,
    }
  }
}

class ElectricCar extends Car {
  constructor(data) {
    super(data)
    this.range = data.range || "300 miles"
    this.chargingTime = data.chargingTime || "30 minutes"
  }

  toJSON() {
    return {
      ...super.toJSON(),
      range: this.range,
      chargingTime: this.chargingTime,
    }
  }
}

// Booking Model
class Booking {
  constructor(data) {
    this.id = data.id || `BK-${Math.floor(Math.random() * 10000)}`
    this.customerId = data.customerId
    this.customerName = data.customerName
    this.carId = data.carId
    this.carName = data.carName
    this.pickupDate = data.pickupDate
    this.returnDate = data.returnDate
    this.status = data.status || "Active"
    this.totalPrice = data.totalPrice
  }
}

// User Model
class User {
  constructor(data) {
    this.id = data.id || `USR-${Math.floor(Math.random() * 10000)}`
    this.name = data.name
    this.email = data.email
    this.role = data.role
    this.status = data.status || "Active"
    this.joinedDate = data.joinedDate || new Date().toISOString().split("T")[0]
    this.avatar = data.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random`
  }
}

// Support Ticket Model
class SupportTicket {
  constructor(data) {
    this.id = data.id || `TKT-${Math.floor(Math.random() * 10000)}`
    this.title = data.title
    this.description = data.description
    this.userId = data.userId
    this.userName = data.userName
    this.userAvatar = data.userAvatar
    this.priority = data.priority || "Medium"
    this.status = data.status || "Open"
    this.createdAt = data.createdAt || new Date().toISOString()
  }
}

// =============================================
// Strategy Pattern Implementation
// =============================================

// Car Filtering Strategy
class CarFilterStrategy {
  filter(cars, criteria) {
    throw new Error("filter method must be implemented")
  }
}

class TypeFilterStrategy extends CarFilterStrategy {
  filter(cars, types) {
    if (!types || types.length === 0) return cars
    return cars.filter((car) => types.includes(car.constructor.name))
  }
}

class StatusFilterStrategy extends CarFilterStrategy {
  filter(cars, statuses) {
    if (!statuses || statuses.length === 0) return cars
    return cars.filter((car) => statuses.includes(car.status))
  }
}

class PriceRangeFilterStrategy extends CarFilterStrategy {
  filter(cars, range) {
    if (!range || !range.min || !range.max) return cars
    return cars.filter((car) => car.price >= range.min && car.price <= range.max)
  }
}

// Booking Filtering Strategy
class BookingFilterStrategy {
  filter(bookings, criteria) {
    throw new Error("filter method must be implemented")
  }
}

class StatusBookingFilterStrategy extends BookingFilterStrategy {
  filter(bookings, statuses) {
    if (!statuses || statuses.length === 0) return bookings
    return bookings.filter((booking) => statuses.includes(booking.status))
  }
}

class DateRangeBookingFilterStrategy extends BookingFilterStrategy {
  filter(bookings, dateRange) {
    if (!dateRange || !dateRange.start || !dateRange.end) return bookings
    return bookings.filter((booking) => {
      const pickupDate = new Date(booking.pickupDate)
      return pickupDate >= new Date(dateRange.start) && pickupDate <= new Date(dateRange.end)
    })
  }
}

// =============================================
// Facade Pattern Implementation
// =============================================

// Dashboard Facade - Simplifies interactions with the dashboard
class DashboardFacade {
  constructor() {
    this.carController = new CarController()
    this.bookingController = new BookingController()
    this.userController = new UserController()
    this.supportController = new SupportController()
    this.analyticsController = new AnalyticsController()
  }

  initialize() {
    this.setupEventListeners()
    this.loadInitialData()
    this.setupNavigation()
  }

  setupEventListeners() {
    // Sidebar toggle
    document.getElementById("sidebar-toggle").addEventListener("click", () => {
      document.querySelector(".sidebar").classList.toggle("mobile-visible")
    })

    // Car modal events
    document.getElementById("add-car-btn").addEventListener("click", () => this.carController.showAddCarModal())
    document.getElementById("cancel-car").addEventListener("click", () => this.carController.hideCarModal())
    document.getElementById("save-car").addEventListener("click", () => this.carController.saveCarFromForm())
    document.querySelector(".close-modal").addEventListener("click", () => this.carController.hideCarModal())

    // Filter events
    const filterCheckboxes = document.querySelectorAll(".filter-option input")
    filterCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        if (
          checkbox.id.startsWith("filter-suv") ||
          checkbox.id.startsWith("filter-sedan") ||
          checkbox.id.startsWith("filter-electric")
        ) {
          this.carController.applyFilters()
        } else if (
          checkbox.id.startsWith("filter-active") ||
          checkbox.id.startsWith("filter-completed") ||
          checkbox.id.startsWith("filter-cancelled")
        ) {
          this.bookingController.applyFilters()
        }
      })
    })
  }

  loadInitialData() {
    this.carController.loadCars()
    this.bookingController.loadBookings()
    this.userController.loadUsers()
    this.supportController.loadTickets()
    this.analyticsController.initializeCharts()
  }

  setupNavigation() {
    const navItems = document.querySelectorAll(".nav-item")
    navItems.forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault()
        const section = item.dataset.section
        this.navigateToSection(section)
      })
    })
  }

  navigateToSection(section) {
    // Update active nav item
    document.querySelectorAll(".nav-item").forEach((item) => {
      item.classList.remove("active")
    })
    document.querySelector(`.nav-item[data-section="${section}"]`).classList.add("active")

    // Update section title
    document.getElementById("section-title").textContent = this.getSectionTitle(section)

    // Show active section
    document.querySelectorAll(".content-section").forEach((section) => {
      section.classList.remove("active")
    })
    document.getElementById(`${section}-section`).classList.add("active")

    // Close mobile sidebar if open
    document.querySelector(".sidebar").classList.remove("mobile-visible")
  }

  getSectionTitle(section) {
    const titles = {
      cars: "Manage Cars",
      bookings: "Manage Bookings",
      users: "Manage Users",
      analytics: "Analytics",
      support: "Support Requests",
    }
    return titles[section] || "Dashboard"
  }
}

// =============================================
// Controllers (Business Logic Layer)
// =============================================

// Car Controller
class CarController {
  constructor() {
    this.cars = []
    this.typeFilterStrategy = new TypeFilterStrategy()
    this.statusFilterStrategy = new StatusFilterStrategy()
    this.priceFilterStrategy = new PriceRangeFilterStrategy()
    this.carModal = document.getElementById("car-modal")
    this.carForm = document.getElementById("car-form")
    this.editingCarId = null
  }

  loadCars() {
    // In a real app, this would fetch from an API
    // For demo, we'll create some sample cars
    const sampleCars = [
      {
        name: "Toyota RAV4",
        type: "SUV",
        price: 65,
        seats: 5,
        transmission: "Automatic",
        fuel: "Petrol",
        status: "Available",
      },
      {
        name: "Honda Civic",
        type: "Sedan",
        price: 45,
        seats: 5,
        transmission: "Automatic",
        fuel: "Petrol",
        status: "Available",
      },
      {
        name: "Tesla Model 3",
        type: "Electric",
        price: 85,
        seats: 5,
        transmission: "Automatic",
        fuel: "Electric",
        status: "Available",
      },
      {
        name: "Ford Explorer",
        type: "SUV",
        price: 75,
        seats: 7,
        transmission: "Automatic",
        fuel: "Petrol",
        status: "Booked",
      },
      {
        name: "BMW 3 Series",
        type: "Sedan",
        price: 95,
        seats: 5,
        transmission: "Automatic",
        fuel: "Petrol",
        status: "Maintenance",
      },
      {
        name: "Nissan Leaf",
        type: "Electric",
        price: 55,
        seats: 5,
        transmission: "Automatic",
        fuel: "Electric",
        status: "Available",
      },
    ]

    this.cars = sampleCars.map((car) => {
      return CarFactory.createCar(car.type, car)
    })

    this.renderCars()
  }

  renderCars() {
    const container = document.getElementById("cars-container")
    container.innerHTML = ""

    this.cars.forEach((car) => {
      const carData = car.toJSON()
      const statusClass = carData.status.toLowerCase()

      const carElement = document.createElement("div")
      carElement.className = "car-card"
      carElement.innerHTML = `
                <div class="car-image">
                    <img src="${carData.image}" alt="${carData.name}">
                </div>
                <div class="car-details">
                    <div class="car-header">
                        <div class="car-title">
                            <h4>${carData.name}</h4>
                            <span class="car-type">${carData.type}</span>
                        </div>
                        <div class="car-price">
                            $${carData.price}<span>/day</span>
                        </div>
                    </div>
                    <div class="car-features">
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            ${carData.seats} Seats
                        </div>
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 12"></polyline><line x1="12" y1="8" x2="12" y2="16"></line></svg>
                            ${carData.transmission}
                        </div>
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22v-3"></path><path d="M7 22v-6"></path><path d="M11 22v-9"></path><path d="M15 22v-6"></path><path d="M19 22v-3"></path><path d="M3 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M7 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M11 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M15 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M19 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path></svg>
                            ${carData.fuel}
                        </div>
                    </div>
                    <div class="car-status">
                        <span class="status-badge ${statusClass}">${carData.status}</span>
                    </div>
                    <div class="car-actions">
                        <button class="action-btn edit-btn" data-id="${carData.id}">Edit</button>
                        <button class="action-btn delete-btn" data-id="${carData.id}">Delete</button>
                    </div>
                </div>
            `

      // Add event listeners
      carElement.querySelector(".edit-btn").addEventListener("click", () => {
        this.editCar(carData.id)
      })

      carElement.querySelector(".delete-btn").addEventListener("click", () => {
        this.deleteCar(carData.id)
      })

      container.appendChild(carElement)
    })
  }

  applyFilters() {
    let filteredCars = [...this.cars]

    // Get selected car types
    const selectedTypes = []
    if (document.getElementById("filter-suv").checked) selectedTypes.push("SUV")
    if (document.getElementById("filter-sedan").checked) selectedTypes.push("Sedan")
    if (document.getElementById("filter-electric").checked) selectedTypes.push("ElectricCar")

    // Apply type filter
    filteredCars = this.typeFilterStrategy.filter(filteredCars, selectedTypes)

    // Render filtered cars
    this.renderFilteredCars(filteredCars)
  }

  renderFilteredCars(filteredCars) {
    const container = document.getElementById("cars-container")
    container.innerHTML = ""

    if (filteredCars.length === 0) {
      container.innerHTML = '<div class="no-results">No cars match your filters</div>'
      return
    }

    // Use the same rendering logic but with filtered cars
    filteredCars.forEach((car) => {
      const carData = car.toJSON()
      const statusClass = carData.status.toLowerCase()

      const carElement = document.createElement("div")
      carElement.className = "car-card"
      carElement.innerHTML = `
                <div class="car-image">
                    <img src="${carData.image}" alt="${carData.name}">
                </div>
                <div class="car-details">
                    <div class="car-header">
                        <div class="car-title">
                            <h4>${carData.name}</h4>
                            <span class="car-type">${carData.type}</span>
                        </div>
                        <div class="car-price">
                            $${carData.price}<span>/day</span>
                        </div>
                    </div>
                    <div class="car-features">
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            ${carData.seats} Seats
                        </div>
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 12"></polyline><line x1="12" y1="8" x2="12" y2="16"></line></svg>
                            ${carData.transmission}
                        </div>
                        <div class="car-feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22v-3"></path><path d="M7 22v-6"></path><path d="M11 22v-9"></path><path d="M15 22v-6"></path><path d="M19 22v-3"></path><path d="M3 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M7 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M11 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M15 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path><path d="M19 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path></svg>
                            ${carData.fuel}
                        </div>
                    </div>
                    <div class="car-status">
                        <span class="status-badge ${statusClass}">${carData.status}</span>
                    </div>
                    <div class="car-actions">
                        <button class="action-btn edit-btn" data-id="${carData.id}">Edit</button>
                        <button class="action-btn delete-btn" data-id="${carData.id}">Delete</button>
                    </div>
                </div>
            `

      // Add event listeners
      carElement.querySelector(".edit-btn").addEventListener("click", () => {
        this.editCar(carData.id)
      })

      carElement.querySelector(".delete-btn").addEventListener("click", () => {
        this.deleteCar(carData.id)
      })

      container.appendChild(carElement)
    })
  }

  showAddCarModal() {
    this.editingCarId = null
    document.getElementById("modal-title").textContent = "Add New Car"
    this.carForm.reset()
    this.carModal.classList.add("active")
  }

  hideCarModal() {
    this.carModal.classList.remove("active")
  }

  editCar(carId) {
    const car = this.cars.find((car) => car.id === carId)
    if (!car) return

    this.editingCarId = carId
    document.getElementById("modal-title").textContent = "Edit Car"

    // Fill form with car data
    document.getElementById("car-name").value = car.name
    document.getElementById("car-type").value =
      car.constructor.name === "ElectricCar" ? "Electric" : car.constructor.name
    document.getElementById("car-price").value = car.price
    document.getElementById("car-seats").value = car.seats
    document.getElementById("car-transmission").value = car.transmission
    document.getElementById("car-fuel").value = car.fuel
    document.getElementById("car-status").value = car.status
    document.getElementById("car-id").value = car.id

    this.carModal.classList.add("active")
  }

  saveCarFromForm() {
    // Validate form
    if (!this.carForm.checkValidity()) {
      this.carForm.reportValidity()
      return
    }

    const carData = {
      name: document.getElementById("car-name").value,
      price: Number.parseFloat(document.getElementById("car-price").value),
      seats: Number.parseInt(document.getElementById("car-seats").value),
      transmission: document.getElementById("car-transmission").value,
      fuel: document.getElementById("car-fuel").value,
      status: document.getElementById("car-status").value,
    }

    const carType = document.getElementById("car-type").value

    if (this.editingCarId) {
      // Update existing car
      const index = this.cars.findIndex((car) => car.id === this.editingCarId)
      if (index !== -1) {
        // Create a new car with the updated data but preserve the ID
        carData.id = this.editingCarId
        this.cars[index] = CarFactory.createCar(carType, carData)
      }
    } else {
      // Add new car
      const newCar = CarFactory.createCar(carType, carData)
      this.cars.push(newCar)
    }

    this.renderCars()
    this.hideCarModal()
  }

  deleteCar(carId) {
    if (confirm("Are you sure you want to delete this car?")) {
      this.cars = this.cars.filter((car) => car.id !== carId)
      this.renderCars()
    }
  }
}

// Booking Controller
class BookingController {
  constructor() {
    this.bookings = []
    this.statusFilterStrategy = new StatusBookingFilterStrategy()
  }

  loadBookings() {
    // Sample bookings data
    const sampleBookings = [
      {
        customerId: "USR-1234",
        customerName: "John Doe",
        carId: "1",
        carName: "Toyota RAV4",
        pickupDate: "2023-05-15",
        returnDate: "2023-05-20",
        status: "Active",
        totalPrice: 325,
      },
      {
        customerId: "USR-5678",
        customerName: "Jane Smith",
        carId: "2",
        carName: "Honda Civic",
        pickupDate: "2023-05-10",
        returnDate: "2023-05-12",
        status: "Completed",
        totalPrice: 90,
      },
      {
        customerId: "USR-9012",
        customerName: "Bob Johnson",
        carId: "3",
        carName: "Tesla Model 3",
        pickupDate: "2023-05-18",
        returnDate: "2023-05-25",
        status: "Active",
        totalPrice: 595,
      },
      {
        customerId: "USR-3456",
        customerName: "Alice Brown",
        carId: "4",
        carName: "Ford Explorer",
        pickupDate: "2023-05-05",
        returnDate: "2023-05-08",
        status: "Completed",
        totalPrice: 225,
      },
      {
        customerId: "USR-7890",
        customerName: "Charlie Wilson",
        carId: "5",
        carName: "BMW 3 Series",
        pickupDate: "2023-05-20",
        returnDate: "2023-05-22",
        status: "Cancelled",
        totalPrice: 190,
      },
    ]

    this.bookings = sampleBookings.map((booking) => new Booking(booking))
    this.renderBookings()
  }

  renderBookings() {
    const tableBody = document.getElementById("bookings-table-body")
    tableBody.innerHTML = ""

    this.bookings.forEach((booking) => {
      const row = document.createElement("tr")

      let statusClass = ""
      if (booking.status === "Active") statusClass = "text-primary"
      else if (booking.status === "Completed") statusClass = "text-accent"
      else if (booking.status === "Cancelled") statusClass = "text-danger"

      row.innerHTML = `
                <td>${booking.id}</td>
                <td>${booking.customerName}</td>
                <td>${booking.carName}</td>
                <td>${booking.pickupDate}</td>
                <td>${booking.returnDate}</td>
                <td><span class="${statusClass}">${booking.status}</span></td>
                <td>
                    <div class="table-actions">
                        <button class="table-btn view-btn">View</button>
                        <button class="table-btn edit-btn">Edit</button>
                    </div>
                </td>
            `

      tableBody.appendChild(row)
    })
  }

  applyFilters() {
    let filteredBookings = [...this.bookings]

    // Get selected booking statuses
    const selectedStatuses = []
    if (document.getElementById("filter-active").checked) selectedStatuses.push("Active")
    if (document.getElementById("filter-completed").checked) selectedStatuses.push("Completed")
    if (document.getElementById("filter-cancelled").checked) selectedStatuses.push("Cancelled")

    // Apply status filter
    filteredBookings = this.statusFilterStrategy.filter(filteredBookings, selectedStatuses)

    // Render filtered bookings
    this.renderFilteredBookings(filteredBookings)
  }

  renderFilteredBookings(filteredBookings) {
    const tableBody = document.getElementById("bookings-table-body")
    tableBody.innerHTML = ""

    if (filteredBookings.length === 0) {
      const row = document.createElement("tr")
      row.innerHTML = '<td colspan="7" class="text-center">No bookings match your filters</td>'
      tableBody.appendChild(row)
      return
    }

    filteredBookings.forEach((booking) => {
      const row = document.createElement("tr")

      let statusClass = ""
      if (booking.status === "Active") statusClass = "text-primary"
      else if (booking.status === "Completed") statusClass = "text-accent"
      else if (booking.status === "Cancelled") statusClass = "text-danger"

      row.innerHTML = `
                <td>${booking.id}</td>
                <td>${booking.customerName}</td>
                <td>${booking.carName}</td>
                <td>${booking.pickupDate}</td>
                <td>${booking.returnDate}</td>
                <td><span class="${statusClass}">${booking.status}</span></td>
                <td>
                    <div class="table-actions">
                        <button class="table-btn view-btn">View</button>
                        <button class="table-btn edit-btn">Edit</button>
                    </div>
                </td>
            `

      tableBody.appendChild(row)
    })
  }
}

// User Controller
class UserController {
  constructor() {
    this.users = []
  }

  loadUsers() {
    // Sample users data
    const sampleUsers = [
      {
        name: "John Doe",
        email: "john.doe@example.com",
        role: "Admin",
        status: "Active",
        joinedDate: "2023-01-15",
      },
      {
        name: "Jane Smith",
        email: "jane.smith@example.com",
        role: "Customer",
        status: "Active",
        joinedDate: "2023-02-20",
      },
      {
        name: "Bob Johnson",
        email: "bob.johnson@example.com",
        role: "Staff",
        status: "Active",
        joinedDate: "2023-03-10",
      },
      {
        name: "Alice Brown",
        email: "alice.brown@example.com",
        role: "Customer",
        status: "Inactive",
        joinedDate: "2023-01-05",
      },
      {
        name: "Charlie Wilson",
        email: "charlie.wilson@example.com",
        role: "Customer",
        status: "Active",
        joinedDate: "2023-04-18",
      },
    ]

    this.users = sampleUsers.map((user) => new User(user))
    this.renderUsers()
  }

  renderUsers() {
    const tableBody = document.getElementById("users-table-body")
    tableBody.innerHTML = ""

    this.users.forEach((user) => {
      const row = document.createElement("tr")

      const statusClass = user.status === "Active" ? "text-accent" : "text-danger"
      let roleClass = ""
      if (user.role === "Admin") roleClass = "text-primary"
      else if (user.role === "Staff") roleClass = "text-warning"

      row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="${roleClass}">${user.role}</span></td>
                <td><span class="${statusClass}">${user.status}</span></td>
                <td>${user.joinedDate}</td>
                <td>
                    <div class="table-actions">
                        <button class="table-btn view-btn">View</button>
                        <button class="table-btn edit-btn">Edit</button>
                    </div>
                </td>
            `

      tableBody.appendChild(row)
    })
  }
}

// Support Controller
class SupportController {
  constructor() {
    this.tickets = []
  }

  loadTickets() {
    // Sample tickets data
    const sampleTickets = [
      {
        title: "Car AC not working",
        description:
          "I rented a Toyota RAV4 and the air conditioning is not working properly. It blows warm air even when set to the coldest setting.",
        userId: "USR-5678",
        userName: "Jane Smith",
        priority: "High",
        createdAt: "2023-05-14T10:30:00",
      },
      {
        title: "Booking extension request",
        description:
          "I would like to extend my current booking for 3 more days. Is this possible? My current booking ends on May 20th.",
        userId: "USR-9012",
        userName: "Bob Johnson",
        priority: "Medium",
        createdAt: "2023-05-15T14:45:00",
      },
      {
        title: "Refund for cancelled booking",
        description:
          "I cancelled my booking (ID: BK-7890) last week but haven't received my refund yet. Please check and process it as soon as possible.",
        userId: "USR-7890",
        userName: "Charlie Wilson",
        priority: "High",
        createdAt: "2023-05-13T09:15:00",
      },
      {
        title: "Car has a scratch",
        description:
          "I noticed a scratch on the passenger door of the car I rented. I want to report it before returning the car to avoid any issues.",
        userId: "USR-3456",
        userName: "Alice Brown",
        priority: "Low",
        createdAt: "2023-05-16T11:20:00",
      },
    ]

    this.tickets = sampleTickets.map((ticket) => new SupportTicket(ticket))
    this.renderTickets()
  }

  renderTickets() {
    const container = document.getElementById("tickets-container")
    container.innerHTML = ""

    this.tickets.forEach((ticket) => {
      const ticketElement = document.createElement("div")
      ticketElement.className = "ticket-card"

      let priorityClass = ""
      if (ticket.priority === "High") priorityClass = "priority-high"
      else if (ticket.priority === "Medium") priorityClass = "priority-medium"
      else if (ticket.priority === "Low") priorityClass = "priority-low"

      const date = new Date(ticket.createdAt)
      const formattedDate = `${date.toLocaleDateString()} ${date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}`

      ticketElement.innerHTML = `
                <div class="ticket-header">
                    <div class="ticket-info">
                        <h4>${ticket.title}</h4>
                        <div class="ticket-meta">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                ${formattedDate}
                            </span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                ${ticket.status}
                            </span>
                        </div>
                    </div>
                    <div class="priority-badge ${priorityClass}">
                        ${ticket.priority}
                    </div>
                </div>
                <div class="ticket-content">
                    <p>${ticket.description}</p>
                </div>
                <div class="ticket-footer">
                    <div class="ticket-user">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(ticket.userName)}&background=random" alt="${ticket.userName}">
                        <span>${ticket.userName}</span>
                    </div>
                    <button class="respond-btn">Respond</button>
                </div>
            `

      container.appendChild(ticketElement)
    })
  }
}

// Analytics Controller
class AnalyticsController {
  constructor() {
    // In a real app, this would fetch analytics data from an API
  }

  initializeCharts() {
    // This is a placeholder for chart initialization
    // In a real app, you would use a charting library like Chart.js

    // For this demo, we'll just add some placeholder content
    document.getElementById("bookings-chart").innerHTML = `
            <div style="height: 250px; display: flex; align-items: center; justify-content: center; background-color: var(--background-alt); border-radius: var(--radius);">
                <p>Bookings Chart Placeholder</p>
            </div>
        `

    document.getElementById("car-types-chart").innerHTML = `
            <div style="height: 250px; display: flex; align-items: center; justify-content: center; background-color: var(--background-alt); border-radius: var(--radius);">
                <p>Car Types Chart Placeholder</p>
            </div>
        `
  }
}

// =============================================
// Application Initialization
// =============================================

document.addEventListener("DOMContentLoaded", () => {
  const dashboard = new DashboardFacade()
  dashboard.initialize()
})
