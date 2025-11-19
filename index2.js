// Get Started Button functionality (redirects to login page)
document.addEventListener('DOMContentLoaded', function() {
    const getStartedButton = document.getElementById('getStartedButton');

    if (getStartedButton) { // Check if button is found
        getStartedButton.addEventListener('click', function() {
            window.location.href = 'login.html'; // Redirects to the login page
        });
    } else {
        console.error("Get Started button not found.");
    }

    // Display "Member Since" date
    const memberSinceDate = localStorage.getItem('memberSince') || new Date().toLocaleDateString();
    document.getElementById('memberSinceDisplay').innerText = memberSinceDate;

    // Example data for uploaded books - replace with actual data as needed
    const uploadedBooks = [
        "Book Title 1",
        "Book Title 2",
        "Book Title 3"
    ];

    // Display uploaded books
    const uploadedBooksList = document.getElementById('uploadedBooks');
    uploadedBooks.forEach(book => {
        const li = document.createElement('li');
        li.textContent = book;
        uploadedBooksList.appendChild(li);
    });
});