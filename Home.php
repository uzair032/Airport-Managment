<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AirGate Solutions</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <!-- Custom CSS -->
  <style>
    body {
      /* background-color: #343a40; Dark theme background color */
      background-color: #28282B;
      color: #f8f9fa; /* Light text color */
    }
    
    .navbar {
      background-color: #007bff; /* Navbar background color */
    }

    .video {
      position: relative;
      overflow: hidden;
      margin-top: 5px;
    }
    
    .video .ratio {
      border-radius: 10px; /* Rounded corners */
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Subtle shadow */
      height: 350px; /* Set height for video */
    }
    
    .video video {
      width: 100%;
      height: 100%;
      object-fit: cover; /* Cover the entire container */
    }
    
    .hero {
      padding: 60px 0;
      text-align: center;
    }
    
    .hero h1, .hero p {
      margin-bottom: 20px;
    }
    
    .feature {
      padding: 20px;
      text-align: center;
    }
    
    .card {
      background-color: #495057; /* Dark card background color */
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    .card-body {
      color: #f8f9fa; /* Light card text color */
    }
  </style>
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#">AirGate Solutions</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="./login.php">Book a Flight</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./Location_data.php">Destination</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./Airline_data.php">Airlines Available</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Video Section -->
  <section class="video">
    <div class="container-fluid">
      <div class="ratio ratio-16x9">
        <video src="./resources/main_video.mp4" autoplay muted loop playsinline>
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </section>
  
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1 class="display-4">Welcome to AirGate Solutions</h1>
      <p class="lead">Where cutting-edge technology meets unparalleled convenience in airport and flight management.</p>
    </div>
  </section>

  <div class="container">
    <div class="row mt-4">
      <div class="col-md-12">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-arrow-right-circle"></i>
            </div>
            <h5 class="card-title">Seamless Integration and Efficiency</h5>
            <p class="card-text">Our platform offers seamless integration with existing airport systems, ensuring smooth operation without disrupting your current workflows.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-tools"></i>
            </div>
            <h5 class="card-title">Comprehensive Features</h5>
            <p class="card-text">From flight scheduling and passenger check-in to baggage handling and gate management, our system covers all bases.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-person-circle"></i>
            </div>
            <h5 class="card-title">User-Friendly Interface</h5>
            <p class="card-text">Our intuitive design ensures that both your staff and passengers can easily navigate the system, reducing training time.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3 justify-content-center">
      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h5 class="card-title">Real-Time Data and Analytics</h5>
            <p class="card-text">Stay ahead with real-time data and insightful analytics to make informed decisions and adapt quickly.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-heart-fill"></i>
            </div>
            <h5 class="card-title">Enhanced Passenger Experience</h5>
            <p class="card-text">Improve passenger satisfaction with advanced features that prioritize a smooth and enjoyable travel experience.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 feature">
        <div class="card">
          <div class="card-body">
            <div class="feature-icon">
              <i class="bi bi-headset"></i>
            </div>
            <h5 class="card-title">Reliable Support and Maintenance</h5>
            <p class="card-text">Our dedicated support team is here for you around the clock, ensuring your system runs smoothly.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  
  <!-- Custom JavaScript -->
  <script src="script.js"></script>
</body>
</html>