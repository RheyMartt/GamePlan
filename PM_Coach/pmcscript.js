document.addEventListener("DOMContentLoaded", function() {
    // Get the modal
    var modal = document.getElementById("injuryModal");

    // Get the button that opens the modal
    var btn = document.querySelector(".classify-injured-btn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const rosterItems = document.querySelectorAll(".player-btn");

    rosterItems.forEach(item => {
        item.addEventListener("click", function () {
            const playerID = this.getAttribute("data-playerid");

            fetch("fetch_player.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "playerID=" + playerID
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error:", data.error);
                } else {
                    document.querySelector(".container .section").innerHTML = `
                        <h3>Bio</h3>
                        <p>Name: ${data.firstName} ${data.lastName}</p>
                        <p>Position: ${data.position}</p>
                        <p>Status: ${data.status}</p>
                        <p>Height: ${data.height} | Weight: ${data.weight}</p>
                    `;
                }
            })
            .catch(error => console.error("Error fetching player data:", error));
        });
    });
});
