// Check login status
function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
        window.location.href = 'login.html'; // Redirect to login if not logged in
    }
}

// Call the function immediately when the page loads
checkLoginStatus();

// Sample user data (replace this with actual data fetching if necessary)
const username = localStorage.getItem('username') || 'Guest';
const email = localStorage.getItem('email') || 'Not Available';
const city = localStorage.getItem('city') || 'Not Available';
const memberSince = localStorage.getItem('memberSince') || 'Not Available';
const uploadedBooks = JSON.parse(localStorage.getItem('uploadedBooks')) || [];

// Display user data in the account section
document.getElementById('usernameDisplay').innerText = username;
document.getElementById('emailDisplay').innerText = email;
document.getElementById('cityDisplay').innerText = city;
document.getElementById('memberSinceDisplay').innerText = memberSince;

// Display user's uploaded books
const uploadedBooksList = document.getElementById('uploadedBooks');
uploadedBooks.forEach(book => {
    const bookItem = document.createElement('li');
    bookItem.innerText = book; // Assuming each book is just a title; adjust if it's an object
    uploadedBooksList.appendChild(bookItem);
});

// Logout function
function logout() {
    // Clear any stored user session data
    localStorage.clear();

    // Redirect the user to the login page
    window.location.href = 'login.php';
}

// Optional: Define an editAccount function if needed
function editAccount() {
    // Add functionality here if you want to allow users to edit their account details
    alert("Edit account functionality is currently unavailable.");
}
