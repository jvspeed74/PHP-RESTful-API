<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 Tracks</title>
    <link rel="stylesheet" href="css/tracks.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchTracks();
        });

        function fetchTracks() {
            fetch('http://localhost:8080/api/tracks')
                .then(response => response.json())
                .then(tracks => {
                    const trackList = document.querySelector('.track-list');
                    tracks.forEach(track => {
                        const trackItem = document.createElement('div');
                        trackItem.classList.add('track-item');
                        trackItem.innerHTML = `
                                <h3>${track.name}</h3>
                                <p><strong>Length:</strong> ${track.length_km} km</p>
                                <p><strong>Continent:</strong> ${track.continent}</p>
                                <p><strong>Description:</strong> ${track.description}</p>
                            `;
                        trackList.appendChild(trackItem);
                    });
                })
                .catch(error => console.error('Error fetching tracks:', error));
        }
    </script>
</head>
<body>
<header>
    <div class="container">
        <h1>Formula 1 Tracks</h1>
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
<section id="tracks" class="section">
    <div class="container">
        <h2>F1 Tracks</h2>
        <p>Explore the iconic Formula 1 tracks from around the world.</p>
        <div class="track-list">
        </div>
    </div>
</section>
<footer>
    <div class="container">
        <p>&copy; 2024 Formula 1 Center. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
