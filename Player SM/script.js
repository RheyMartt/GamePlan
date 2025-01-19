const calendarContainer = document.getElementById('calendar-container');
const monthYearDisplay = document.getElementById('month-year');
const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

let reminders = []; // Array to hold reminders
let scheduleRequests = []; // Array to hold schedule adjustment requests

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

        // Highlight today's date
        if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
            dayCell.classList.add('today');
        }

        // Add click functionality to day cells to add reminders or request adjustments
        dayCell.addEventListener('click', () => {
            handleDayClick(day, month, year);
        });

        calendarContainer.appendChild(dayCell);
    }
}

// Handle day click
function handleDayClick(day, month, year) {
    const date = `${monthNames[month]} ${day}, ${year}`;
    const action = prompt(
        `Selected date: ${date}\nChoose an action:\n1. Add Reminder\n2. Request Schedule Adjustment`
    );

    if (action === '1') {
        addReminder(date);
    } else if (action === '2') {
        requestScheduleAdjustment(date);
    } else {
        alert('Invalid option. Please select 1 or 2.');
    }
}

// Add a reminder
function addReminder(date) {
    const reminderText = prompt(`Enter your reminder for ${date}:`);
    if (reminderText) {
        reminders.push({ date, text: reminderText });
        updateRemindersList();
        alert(`Reminder added for ${date}: "${reminderText}"`);
    }
}

// Request a schedule adjustment
function requestScheduleAdjustment(date) {
    const adjustmentDetails = prompt(`Enter your schedule adjustment request for ${date}:`);
    if (adjustmentDetails) {
        scheduleRequests.push({ date, details: adjustmentDetails });
        alert(`Schedule adjustment request submitted for ${date}: "${adjustmentDetails}"`);
    }
}

// Update reminders list in the UI
function updateRemindersList() {
    const reminderList = document.getElementById('reminders-list');
    reminderList.innerHTML = ''; // Clear the list

    reminders.forEach((reminder, index) => {
        const reminderItem = document.createElement('li');
        reminderItem.textContent = `${reminder.date}: ${reminder.text}`;

        // Add delete button
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.style.marginLeft = '10px';
        deleteButton.addEventListener('click', () => {
            reminders.splice(index, 1);
            updateRemindersList();
        });

        reminderItem.appendChild(deleteButton);
        reminderList.appendChild(reminderItem);
    });
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
// Array to hold schedule announcements
let scheduleAnnouncements = [];

// Function to display announcements in the list
function updateAnnouncementsList() {
    const announcementsList = document.getElementById('announcements-list');
    announcementsList.innerHTML = ''; // Clear the list

    if (scheduleAnnouncements.length === 0) {
        announcementsList.innerHTML = "<p>No announcements yet.</p>";
        return;
    }

    scheduleAnnouncements.forEach((announcement, index) => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <strong>${announcement.title}</strong> 
            (${announcement.date})<br>
            ${announcement.description}
        `;
        announcementsList.appendChild(listItem);
    });
}

// Function to add a new announcement (for admins or coaches only)
function addScheduleAnnouncement(title, description, date) {
    scheduleAnnouncements.push({ title, description, date });
    updateAnnouncementsList();
}

// Function to simulate announcements added by the coach/admin
function preloadAnnouncements() {
    addScheduleAnnouncement("Game Update", "The game has been moved to Jan 22, 2025.", "Jan 20, 2025");
    addScheduleAnnouncement("Team Meeting", "Mandatory meeting after practice on Jan 21.", "Jan 19, 2025");
}

// Call preload announcements on page load
preloadAnnouncements();
// Data structure to hold player schedules
let playerSchedules = {};

// Function to handle clicking on a day
function handleDayClick(day, month, year) {
    const selectedDate = `${monthNames[month]} ${day}, ${year}`;
    const schedulesForDate = playerSchedules[selectedDate] || [];

    // Show a modal or popup with the schedules
    const schedulesHtml = schedulesForDate
        .map(
            (schedule, index) => `
        <li>
            <strong>${schedule.type}</strong> (${schedule.time}) - ${schedule.notes}
            <button onclick="deleteSchedule('${selectedDate}', ${index})">Delete</button>
        </li>
    `
        )
        .join('');

    const modalHtml = `
        <div class="modal">
            <div class="modal-content">
                <h2>Schedules for ${selectedDate}</h2>
                <ul>${schedulesHtml || "<p>No schedules for this date.</p>"}</ul>
                <h3>Add New Schedule</h3>
                <form id="add-schedule-form">
                    <label for="schedule-type">Type:</label>
                    <input type="text" id="schedule-type" placeholder="e.g., Practice, Game" required><br>
                    
                    <label for="schedule-time">Time:</label>
                    <input type="time" id="schedule-time" required><br>
                    
                    <label for="schedule-notes">Notes:</label>
                    <input type="text" id="schedule-notes" placeholder="Optional notes"><br>
                    
                    <button type="submit">Add Schedule</button>
                </form>
                <button id="close-modal">Close</button>
            </div>
        </div>
    `;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Close modal logic
    document.getElementById('close-modal').addEventListener('click', closeModal);

    // Add schedule logic
    document.getElementById('add-schedule-form').addEventListener('submit', (e) => {
        e.preventDefault();
        addSchedule(selectedDate);
    });
}

// Add a new schedule to the selected date
function addSchedule(date) {
    const type = document.getElementById('schedule-type').value;
    const time = document.getElementById('schedule-time').value;
    const notes = document.getElementById('schedule-notes').value;

    if (!playerSchedules[date]) {
        playerSchedules[date] = [];
    }

    playerSchedules[date].push({ type, time, notes });
    alert(`Added schedule for ${date}`);
    closeModal(); // Close the modal after adding
    renderCalendar(currentMonth, currentYear); // Refresh calendar
}

// Delete a schedule
function deleteSchedule(date, index) {
    playerSchedules[date].splice(index, 1);
    alert(`Schedule removed for ${date}`);
    closeModal(); // Close and reopen modal to refresh
    handleDayClick(date.split(' ')[1], currentMonth, currentYear);
}

// Close modal logic
function closeModal() {
    const modal = document.querySelector('.modal');
    if (modal) modal.remove();
}
