

function performSearch(event) {
    const query = document.getElementById('search').value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const match = Array.from(cells).some(cell => 
            cell.textContent.toLowerCase().includes(query)
        );
        row.style.display = match ? '' : 'none';
    });

    // Prevent default form submission if Enter key is pressed
    if (event && event.key === 'Enter') {
        event.preventDefault();
    }
}

