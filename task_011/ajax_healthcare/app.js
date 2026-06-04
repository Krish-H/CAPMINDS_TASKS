document.addEventListener('DOMContentLoaded', () => {
    initApp();
});



let csrfToken = '';
let appointmentsData = [];

async function initApp() {
    await fetchCsrfToken();
    await fetchAppointments();

    // Set min date to today
    const dateInput = document.getElementById('appointment_date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

    // Form submit listener
    document.getElementById('appointment-form').addEventListener('submit', handleFormSubmit);
}

// Fetch CSRF Token
async function fetchCsrfToken() {
    try {
        const response = await fetch('api.php?csrf=1');
        const data = await response.json();
        if (response.ok) {
            csrfToken = data.data.csrf_token;
            document.getElementById('csrf_token').value = csrfToken;
        }
    } catch (error) {
        console.error('Error fetching CSRF token:', error);
    }
}

// Show Alert (Toast)
function showAlert(message, type) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const iconSvg = type === 'success'
        ? `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
        : `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;

    toast.innerHTML = `${iconSvg} <span>${message}</span>`;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-hide');
        toast.addEventListener('animationend', () => toast.remove());
    }, 4000);
}

// Show/Hide Spinner
function toggleSpinner(show) {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = show ? 'flex' : 'none';
    }
}

// READ: Fetch all appointments
async function fetchAppointments(highlightId = null) {
    toggleSpinner(true);
    try {
        const response = await fetch('api.php');
        const data = await response.json();

        if (response.ok) {
            appointmentsData = data.data;
            renderTable(highlightId);
        } else {
            showAlert(data.message || 'Failed to load appointments', 'error');
        }
    } catch (error) {
        showAlert('Network or Server error', 'error');
    } finally {
        toggleSpinner(false);
    }
}

// Render Table
function renderTable(highlightId = null) {
    const tbody = document.getElementById('appointment-list');
    tbody.innerHTML = '';

    if (appointmentsData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="empty-state">
            <div class="empty-state-content">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h3>No appointments available</h3>
                <p>There are currently no patient appointments scheduled.</p>
            </div>
        </td></tr>`;
        return;
    }

    appointmentsData.forEach(app => {
        const tr = document.createElement('tr');

        if (app.id == highlightId) {
            tr.className = 'highlight';
        }

        const statusClass = `status-${app.status.toLowerCase()}`;

        tr.innerHTML = `
            <td>#${app.id}</td>
            <td>
                <strong>${app.patient_name}</strong><br>
                <small style="color: #6c757d;">${app.email}</small><br>
                <small style="color: #6c757d;">${app.mobile}</small>
            </td>
            <td>${app.doctor_name}</td>
            <td>
                ${app.appointment_date}<br>
                <small>${app.appointment_time}</small>
            </td>
            <td>
                <select class="status-select ${statusClass}" onchange="updateStatus(${app.id}, this.value)">
                    <option value="Pending" ${app.status === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Confirmed" ${app.status === 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                    <option value="Cancelled" ${app.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                </select>
            </td>
            <td>
                <button type="button" class="action-btn btn-edit" onclick="editAppointment(${app.id})">Edit</button>
                <button type="button" class="action-btn btn-delete" onclick="deleteAppointment(${app.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// CREATE / UPDATE: Submit Form
async function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const isUpdate = !!data.id;
    const method = isUpdate ? 'PUT' : 'POST';

    toggleSpinner(true);
    try {
        const response = await fetch('api.php', {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message, 'success');
            resetForm();
            // Assuming we get the ID back, or we just highlight if it's an update
            await fetchAppointments(isUpdate ? data.id : null);
        } else {
            showAlert(result.message || 'Error saving appointment', 'error');
        }
    } catch (error) {
        showAlert('Network or Server error', 'error');
    } finally {
        toggleSpinner(false);
    }
}

// DELETE: Delete appointment
async function deleteAppointment(id) {
    if (!confirm('Are you sure you want to delete this appointment?')) return;

    try {
        const response = await fetch(`api.php?id=${id}&csrf_token=${encodeURIComponent(csrfToken)}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message, 'success');
            await fetchAppointments();
        } else {
            showAlert(result.message || 'Error deleting appointment', 'error');
        }
    } catch (error) {
        showAlert('Network or Server error', 'error');
    }
}

// STATUS UPDATE: Update only status
async function updateStatus(id, newStatus) {
    try {
        const response = await fetch('api.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                status: newStatus,
                status_only: true,
                csrf_token: csrfToken
            })
        });

        const result = await response.json();

        if (response.ok) {
            showAlert('Status updated successfully', 'success');
            await fetchAppointments(id); // Highlight row
        } else {
            showAlert(result.message || 'Error updating status', 'error');
            await fetchAppointments(); // Revert change if error
        }
    } catch (error) {
        showAlert('Network or Server error', 'error');
        await fetchAppointments();
    }
}

// Populate form for Edit
function editAppointment(id) {


    const app = appointmentsData.find(a => a.id == id);
    if (!app) return;

    document.getElementById('appointment_id').value = app.id;
    document.getElementById('patient_name').value = app.patient_name;
    document.getElementById('email').value = app.email;
    document.getElementById('mobile').value = app.mobile;
    document.getElementById('doctor_name').value = app.doctor_name;
    document.getElementById('appointment_date').value = app.appointment_date;

    // API returns full time like "14:30:00", inputs type="time" expect "HH:mm"
    const timeParts = app.appointment_time.split(':');
    if (timeParts.length >= 2) {
        document.getElementById('appointment_time').value = `${timeParts[0]}:${timeParts[1]}`;
    }

    document.getElementById('form-title').innerText = 'Edit Appointment';
    document.getElementById('submit-btn').innerText = 'Update Appointment';
    document.getElementById('cancel-btn').style.display = 'block';

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Reset form
function resetForm() {
    document.getElementById('appointment-form').reset();
    document.getElementById('appointment_id').value = '';

    // Re-assign CSRF because reset() clears hidden fields if not careful, though hidden fields usually persist, just to be safe
    document.getElementById('csrf_token').value = csrfToken;

    document.getElementById('form-title').innerText = 'Book Appointment';
    document.getElementById('submit-btn').innerText = 'Save Appointment';
    document.getElementById('cancel-btn').style.display = 'none';
}
