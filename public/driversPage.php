<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 Drivers</title>
    <link rel="stylesheet" href="css/drivers.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchDrivers();
        });

        function fetchDrivers() {
            fetch('/drivers')
                .then(response => response.json())
                .then(drivers => {
                    const driverList = document.querySelector('.driver-list');
                    drivers.forEach(driver => {
                        const driverItem = document.createElement('div');
                        driverItem.classList.add('driver-item');
                        driverItem.innerHTML = `
                                <h3>${driver.first_name} ${driver.last_name}</h3>
                                <p><strong>Driver Number:</strong> ${driver.driver_number}</p>
                                <p><strong>Birthday:</strong> ${driver.birthday}</p>
                                <div class="driver-info">
                                    <p><strong>Team:</strong> ${driver.team_id}</p>
                                    <p><strong>Nationality:</strong> ${driver.nationality_id}</p>
                                </div>
                                <div class="driver-stats">
                                    <p><span>Career Points:</span> ${driver.career_points}</p>
                                    <p><span>Career Wins:</span> ${driver.career_wins}</p>
                                    <p><span>Career Podiums:</span> ${driver.career_podiums}</p>
                                    <p><span>Championships:</span> ${driver.championships}</p>
                                </div>
                            `;
                        driverList.appendChild(driverItem);
                    });
                })
                .catch(error => console.error('Error fetching drivers:', error));
        }
    </script>
</head>
<body>
<header>
    <div class="container">
        <h1>Formula 1 Drivers</h1>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/tracksPage">Tracks</a></li>
                <li><a href="/eventsPage">Events</a></li>
                <li><a href="/driversPage">Drivers</a></li>
                <li><a href="/teamsPage">Teams</a></li>
                <li><a href="/carsPage">Cars</a></li>
                <li><a href="/signin" class="signin-btn">Sign In</a></li>
            </ul>
        </nav>
    </div>
</header>
<section id="drivers" class="section">
    <div class="container">
        <h2>Formula 1 Drivers</h2>
        <p>Meet the drivers of Formula 1 and learn more about their careers and achievements.</p>
        <div class="driver-list"></div>
    </div>
</section>
<footer>
    <div class="container">
        <p>&copy; 2024 Formula 1 Center. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
