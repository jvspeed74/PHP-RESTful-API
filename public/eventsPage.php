<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 Events</title>
    <link rel="stylesheet" href="css/events.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchEvents();
        });

        function fetchEvents() {
            fetch('http://localhost:8080/api/events')
                .then(response => response.json())
                .then(events => {
                    const eventList = document.querySelector('.event-list');
                    events.forEach(event => {
                        const eventItem = document.createElement('div');
                        eventItem.classList.add('event-item');
                        eventItem.innerHTML = `
                                <h3>${event.title}</h3>
                                <p><strong>Date:</strong> ${event.scheduled_date}</p>
                                <p><strong>Status:</strong> ${event.status}</p>
                            `;
                        eventList.appendChild(eventItem);
                    });
                })
                .catch(error => console.error('Error fetching events:', error));
        }
    </script>
</head>
<body>
<header>
    <div class="container">
        <h1>Formula 1 Events</h1>
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
<section id="events" class="section">
    <div class="container">
        <h2>Formula 1 Events</h2>
        <p>Stay up to date with the latest Formula 1 events around the world.</p>
        <div class="event-list">
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
