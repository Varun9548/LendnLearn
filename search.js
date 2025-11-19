// Check login status
function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
        window.location.href = 'login.html'; // Redirect to login if not logged in
    }
}

// Call the function immediately when the page loads
checkLoginStatus();

// Search Form and Results
const searchForm = document.getElementById('searchForm');
const searchResults = document.getElementById('searchResults');
searchForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const searchQuery = document.getElementById('searchQuery').value.trim().toLowerCase();
    const books = [
        { title: "Book Title 1", author: "Author 1", description: "A great book about..." },
        { title: "Book Title 2", author: "Author 2", description: "Another interesting story..." },
        { title: "Book Title 3", author: "Author 3", description: "A thrilling mystery..." },
    ];
    const results = books.filter(book =>
        book.title.toLowerCase().includes(searchQuery) ||
        book.author.toLowerCase().includes(searchQuery)
    );

    searchResults.innerHTML = '';
    if (results.length > 0) {
        results.forEach(book => {
            const bookItem = document.createElement('div');
            bookItem.classList.add('book-item');
            bookItem.innerHTML = `
                <h3>${book.title}</h3>
                <p><strong>Author:</strong> ${book.author}</p>
                <p>${book.description}</p>
            `;
            searchResults.appendChild(bookItem);
        });
    } else {
        searchResults.innerHTML = '<p>No books found matching your search.</p>';
    }
});


