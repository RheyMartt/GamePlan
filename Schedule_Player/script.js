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

    // Add schedule logic
    document.getElementById('schedule-form').addEventListener('submit', (e) => {
        e.preventDefault();
        addSchedule(date); // Add the schedule
    });
}

// Close modal logic
function closeModal() {
    const modal = document.querySelector('.modal');
    if (modal) modal.remove();
}