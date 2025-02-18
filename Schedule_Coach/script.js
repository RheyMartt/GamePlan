const calendarContainer = document.getElementById('calendar-container');
const monthYearDisplay = document.getElementById('month-year');
const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

function renderCalendar(month, year) {
    calendarContainer.innerHTML = '';
    monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Add empty cells for alignment
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('empty-cell');
        calendarContainer.appendChild(emptyCell);
    }

    // Populate days in the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.textContent = day;
        dayCell.classList.add('day-cell');

        const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Highlight event days
        if (eventDates.includes(formattedDate)) {
            dayCell.classList.add('event-day');
        }

        // Highlight today
        if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
            dayCell.classList.add('today');
        }

        dayCell.addEventListener("click", function () {
            document.getElementById("date").value = formattedDate;
        });

        calendarContainer.appendChild(dayCell);
    }
}


// Handle Previous and Next Month Buttons
document.getElementById('prev-month').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar(currentMonth, currentYear);
});

document.getElementById('next-month').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
});

// Initial Render
renderCalendar(currentMonth, currentYear);

document.getElementById('add-to-calendar').addEventListener('click', function () {
    const gameType = document.getElementById('game-type').value;
    const opponentSelect = document.getElementById('opponent');
    const opponent = opponentSelect.value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const locationSelect = document.getElementById('location');
    const locationName = locationSelect.options[locationSelect.selectedIndex].text;

    if (!gameType || opponent === "" || opponentSelect.selectedIndex === 0 || 
        !date || !time || locationSelect.selectedIndex === 0) {
        alert("Please fill out all fields correctly.");
        return;
    }

    const formData = new FormData();
    formData.append("gameType", gameType);
    formData.append("opponent", opponent);
    formData.append("date", date);
    formData.append("time", time);
    formData.append("location", locationName); 

    fetch("SM.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            location.reload(); 
        } else {
            alert(data.message); 
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Failed to communicate with the server.");
    });
});

dayCell.addEventListener("click", function () {
    document.getElementById("date").value = formattedDate;

    // Remove previous selection
    document.querySelectorAll('.day-cell').forEach(cell => cell.classList.remove('selected-date'));

    // Add class to highlight the clicked date
    this.classList.add('selected-date');
});

