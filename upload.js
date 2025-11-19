// Check login status
function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
        window.location.href = 'login.html'; // Redirect to login if not logged in
    }
}

// Call the function immediately when the page loads
checkLoginStatus();

// Upload Form Image Preview
const uploadForm = document.getElementById('uploadForm');
const coverInput = document.getElementById('cover');
const coverPreview = document.createElement('img');

coverInput.parentElement.appendChild(coverPreview);
coverPreview.style.display = 'none';
coverPreview.style.maxWidth = '200px';
coverPreview.style.marginTop = '20px';
coverPreview.style.borderRadius = '10px';
coverPreview.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';

coverInput.addEventListener('change', function() {
    const file = coverInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            coverPreview.src = e.target.result;
            coverPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        coverPreview.style.display = 'none';
        coverPreview.src = '';
    }
});

// Form Validation
uploadForm.addEventListener('submit', function(event) {
    const title = document.getElementById('title').value.trim();
    const author = document.getElementById('author').value.trim();
    const description = document.getElementById('description').value.trim();
    const genre = document.getElementById('genre').value;
    const cover = coverInput.files[0];

    if (!title || !author || !description || !genre || !cover) {
        alert('Please fill out all required fields.');
        event.preventDefault();
    } else {
        alert('Book uploaded successfully!');
    }
});