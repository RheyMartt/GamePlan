const calendarContainer = document.getElementById('calendar-container');
const monthYearDisplay = document.getElementById('month-year');
const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Render the calendar
function renderCalendar(month, year) {
    // Clear the previous calendar
    calendarContainer.innerHTML = '';

    // Set month and year in the display
    monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

    // Determine the first day of the month and the number of days in the month
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Fill in blank cells for days before the first of the month
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('empty-cell');
        calendarContainer.appendChild(emptyCell);
    }

    // Add day cells for the current month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.textContent = day;
        dayCell.classList.add('day-cell');

        const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Highlight today's date
        if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
            dayCell.classList.add('today');
        }

        // Highlight event days
        if (eventDates.includes(formattedDate)) {
            dayCell.classList.add('event-day');
        }

        // Add click functionality to day cells to add reminders or request adjustments
        dayCell.addEventListener('click', () => {
            handleDayClick(day, month, year);
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


document.addEventListener('DOMContentLoaded', function() {
    const announcementsList = document.getElementById('schedule-list');
    
    // Loop through the personal schedules and display them
    personalSchedules.forEach(schedule => {
        const listItem = document.createElement('li');
        listItem.textContent = ` ${schedule.type} on ${schedule.schedDate} ${schedule.schedTime}, ${schedule.notes}`;
        announcementsList.appendChild(listItem);
    });
});


// Handle day click
function handleDayClick(day, month, year) {
    const date = `${monthNames[month]} ${day}, ${year}`;
    
    // Show the form to add new schedule
    const scheduleFormHtml = `
        <div class="modal">
            <div class="modal-content">
                <h2>Add Schedule for ${date}</h2>
                <form id="schedule-form">
                    <label for="type">Schedule Type:</label>
                    <input type="text" id="type" placeholder="e.g., Practice, Game" required><br>
                    <label for="sched-time">Time:</label>
                    <input type="time" id="sched-time" required><br>
                    <label for="notes">Notes:</label>
                    <input type="text" id="notes" placeholder="Optional"><br>
                    <button type="submit">Add Schedule</button>
                </form>
                <button id="close-modal">Close</button>
            </div>
        </div>
    `;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', scheduleFormHtml);

    // Close modal logic
    document.getElementById('close-modal').addEventListener('click', closeModal);

    // Handle form submission to add schedule
    document.getElementById('schedule-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

        const type = document.getElementById('type').value;
        const schedTime = document.getElementById('sched-time').value;
        const notes = document.getElementById('notes').value.trim() || "n/a"; // Default to 'n/a' if empty

        // Get the selected date from the handleDayClick function
        const schedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Send the data to the server using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'PlayerSM.php', true); // Pointing to the PHP script
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Schedule added successfully!');
                    closeModal(); // Close the modal after submission
                    location.reload(); // Reload the entire page to reflect the updated schedule
                } else {
                    alert('Failed to add schedule.');
                }
            }
        };

    xhr.send(`type=${encodeURIComponent(type)}&schedDate=${encodeURIComponent(schedDate)}&schedTime=${encodeURIComponent(schedTime)}&notes=${encodeURIComponent(notes)}`);
});
}

// Close modal logic
function closeModal() {
    const modal = document.querySelector('.modal');
    if (modal) modal.remove();
}