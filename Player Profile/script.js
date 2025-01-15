document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".form");

    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // Prevent default form submission

        const login = document.getElementById("login").value.trim();
        const password = document.getElementById("password").value.trim();

        try {
            // Fetch user data from JSON file
            const response = await fetch("users.json");

            if (!response.ok) {
                throw new Error("Failed to fetch users.");
            }

            const users = await response.json();

            // Validate credentials
            const user = users.find(
                (u) => u.username === login && u.password === password
            );

            if (user) {
                // Redirect to the respective dashboard
                window.location.href = user.redirect;
            } else {
                alert("Invalid login credentials. Please try again.");
            }
        } catch (error) {
            console.error("Error fetching user data:", error);
            alert("An error occurred. Please try again later.");
        }
    });
});
