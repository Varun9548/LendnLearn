document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Get user details from the form
    //const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    //const city = document.getElementById('city').value;
    const password = document.getElementById('password').value; // You can validate the password here if needed

    // Store user details in localStorage
    //localStorage.setItem('username', username);
    localStorage.setItem('email', email);
    //localStorage.setItem('city', city);
    localStorage.setItem('memberSince', new Date().toLocaleDateString());

    // Set login status to true
    localStorage.setItem('isLoggedIn', 'true');

    // Redirect to "My Account" page
    window.location.href = 'my_account.html';
});
