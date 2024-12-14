<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formula 1 Teams</title>
    <link rel="stylesheet" href="css/teams.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchTeams();
        });

        function fetchTeams() {
            fetch('/teams')  <!-- Assuming the team data will be fetched from this endpoint -->
                .then(response => response.json())
                .then(teams => {
                    const teamList = document.querySelector('.team-list');
                    teams.forEach(team => {
                        const teamItem = document.createElement('div');
                        teamItem.classList.add('team-item');
                        teamItem.innerHTML = `
                            <h3>${team.official_name} (${team.short_name})</h3>
                            <p><strong>Headquarters:</strong> ${team.headquarters}</p>
                            <p><strong>Team Principal:</strong> ${team.team_principal}</p>
                        `;
                        teamList.appendChild(teamItem);
                    });
                })
                .catch(error => console.error('Error fetching teams:', error));
        }
    </script>
</head>
<body>
<header>
    <div class="container">
        <h1>Formula 1 Teams</h1>
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
<section id="teams" class="section">
    <div class="container">
        <h2>Formula 1 Teams</h2>
        <p>Discover the top teams competing in Formula 1.</p>
        <div class="team-list"></div>
    </div>
</section>
<footer>
    <div class="container">
        <p>&copy; 2024 Formula 1 Center. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
