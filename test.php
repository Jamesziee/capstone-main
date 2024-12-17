<div id="childInfoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Student Information</h3>
            <div class="student-info">
                <div class="student-photo">
                    <img id="child-image" src="" alt="Student photo">
                </div>
                <div class="info-field">
                    <label for="child-name">Child Name</label>
                    <input type="text" id="child-name" readonly>
                </div>
                <div class="info-field">
                    <label for="child-id">Student ID</label>
                    <input type="text" id="child-id" readonly>
                </div>
                <div class="info-field">
                    <label for="child-section">Section</label>
                    <input type="text" id="child-section" readonly>
                </div>
                <div class="info-field">
                    <label for="child-grade">Grade</label>
                    <input type="text" id="child-grade" readonly>
                </div>
                <div class="info-field">
                    <label for="child-age">Age</label>
                    <input type="text" id="child-age" readonly>
                </div>
                <div class="info-field">
                    <label for="child-address">Address</label>
                    <input type="text" id="child-address" readonly>
                </div>
                <div class="info-field">
                    <label for="child-teacher">Adviser</label>
                    <input type="text" id="child-teacher" readonly>
                </div>
                <div class="button-group">
                    <button class="edit-btn">Edit Info</button>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openModal()">Open Modal</button>

<style>
    /* Enhanced Modal Design */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7); /* Darker background for better focus */
}

.modal-content {
    background: #ffffff; /* Clean white background */
    border-radius: 12px;
    padding: 20px;
    max-width: 600px;
    width: 90%;
    margin: 10% auto;
    position: relative;
    animation: slideDown 0.4s ease; /* Smooth slide-down effect */
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    overflow: hidden; /* Prevent overflowing content */
}

.close-btn {
    font-size: 24px;
    position: absolute;
    top: 15px;
    right: 20px;
    cursor: pointer;
    color: #333;
    transition: color 0.3s;
}

.close-btn:hover {
    color: #7c3aed; /* Matches button hover color */
}

.modal h3 {
    font-size: 22px;
    margin-bottom: 20px;
    text-align: center;
    color: #7c3aed; /* Highlighted heading color */
}

.student-info {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Better spacing */
}

.student-photo img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #7c3aed; /* Highlighted border */
    margin: 0 auto; /* Center the image */
}

.info-field {
    display: flex;
    flex-direction: column;
    gap: 5px; /* Spacing between label and input */
}

.info-field label {
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

.info-field input {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    font-size: 14px;
    color: #333;
    outline: none;
    transition: border-color 0.3s;
}

.info-field input:focus {
    border-color: #7c3aed;
    background-color: #fff;
}

.button-group {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

button.edit-btn {
    padding: 10px 30px;
    border: none;
    border-radius: 8px;
    background-color: #7c3aed;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

button.edit-btn:hover {
    background-color: #5a28b8;
    transform: translateY(-2px); /* Subtle lift effect */
}

@keyframes slideDown {
    from {
        transform: translateY(-50%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 20px;
    }

    .student-photo img {
        width: 100px;
        height: 100px;
    }

    button.edit-btn {
        font-size: 14px;
        padding: 8px 20px;
    }
}


    .qr-btn {
        background-color: #10b981;
        color: white;
    }

    .add-btn {
        background-color: #e0e7ff;
        color: #4338ca;
        width: 100%;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .add-btn i {
        margin-left: 5px;
    }

    #authorized-persons-list {
        margin-bottom: 10px;
    }

    .person {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 5px;
        background-color: #f3f4f6;
        border-radius: 4px;
    }

    .person img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .remove-btn {
        background-color: #ef4444;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    i {
        cursor: pointer; /* Changes the cursor to a pointer */
        margin: 0 5px; /* Optional: Adds spacing between icons */
        transition: transform 0.2s; /* Optional: Adds a hover effect */
    }

    i:hover {
        transform: scale(1.1); /* Optional: Slightly enlarges the icon on hover */
    }

</style>

<script>
function openModal() {
    const modal = document.getElementById('childInfoModal');
    modal.style.display = 'block'; // Set the modal to display
}

function closeModal() {
    const modal = document.getElementById('childInfoModal');
    modal.style.display = 'none'; // Hide the modal
}

// Event listener for outside click
window.onclick = function(event) {
    const modal = document.getElementById('childInfoModal');
    if (event.target === modal) {
        closeModal();
    }
};

// Debug fetch example
function showChildInfo(parentId) {
    console.log('Fetching data for parent ID:', parentId);
    fetch(`child_info.php?parent_id=${parentId}`)
        .then(response => response.text())
        .then(html => {
            const container = document.querySelector('.student-info');
            container.innerHTML = html || '<p>No data available</p>';
            openModal();
        })
        .catch(err => {
            console.error('Error fetching child info:', err);
            alert('Failed to load child information.');
        });
}


</script>