


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Manufacturing Centre - Facility Booking</title>
    <link rel="stylesheet" href="../styles.css">
</head>
</head>
<body>
    <nav>
        <div class="title">Advanced Manufacturing Centre</div>
        <ul class="nav-links">
            <li><a href="#" class="active">Facilities</a></li>
            <li><a href="user_bookings.php">My Bookings</a></li>
            <li><a href="#">Profile</a></li>
        </ul>
    </nav>
    <section class="hero">
        <div class="container">

            <h2>Available Facilities</h2>

            <div class="grid">

                <!-- Facility Card 1 -->
                <div class="card">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=60" alt="Robotics & Automation Centre">
                    <div class="card-body">
                        <div class="title">Robotics & Automation Centre</div>
                        <div class="desc">Level 2 • Safety briefing required</div>

                        <!-- User is redirected to booking form with facility pre-filled -->
                        <a class="btn" href="booking_form.php?facility_id=1&facility_name=Robotics%20%26%20Automation%20Centre">
                            Select
                        </a>
                    </div>
                </div>

                <!-- Facility Card 2 -->
                <div class="card">
                    <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=60" alt="TP-HRG Robotics Innovation Centre">
                    <div class="card-body">
                        <div class="title">TP-HRG Robotics Innovation Centre</div>
                        <div class="desc">Level 1 • PLA / ABS supported</div>

                        <a class="btn" href="booking_form.php?facility_id=2&facility_name=TP-HRG%20Robotics%20Innovation%20Centre">
                            Select
                        </a>
                    </div>
                </div>

                <div class="card">
                    <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=60" alt="T-Nvidia AI Technology Centre">
                    <div class="card-body">
                        <div class="title">T-Nvidia AI Technology Centre</div>
                        <div class="desc">Level 1 • PLA / ABS supported</div>

                        <a class="btn" href="booking_form.php?facility_id=3&facility_name=T-Nvidia%20AI%20Technology%20Centre">
                            Select
                        </a>
                    </div>
                </div>

                <div class="card">
                    <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=60" alt="Digital Fabrication & Additive Manufacturing Centre">
                    <div class="card-body">
                        <div class="title">Digital Fabrication & Additive Manufacturing Centre</div>
                        <div class="desc">Level 1 • PLA / ABS supported</div>

                        <a class="btn" href="booking_form.php?facility_id=4&facility_name=Digital%20Fabrication%20%26%20Additive%20Manufacturing%20Centre">
                            Select
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</body>
</html>