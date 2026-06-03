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

// Show Alert
function showAlert(message, type) {
    const alertBox = document.getElementById('alert-box');
    alertBox.textContent = message;
    alertBox.className = `alert ${type}`;
    alertBox.style.display = 'block';
    
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

// READ: Fetch all appointments
async function fetchAppointments(highlightId = null) {
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
    }
}

// Render Table
function renderTable(highlightId = null) {
    const tbody = document.getElementById('appointment-list');
    tbody.innerHTML = '';
    
    if (appointmentsData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="empty-state">No appointments found.</td></tr>`;
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
