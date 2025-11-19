// Function to generate random book data
function generateRandomBooks(num) {
    const books = [];
    for (let i = 1; i <= num; i++) {
        books.push({
            title: `Book Title ${i}`,
            author: `Author Name ${i}`,
            publisher: `Publisher Name ${i}`,
            publicationDate: `202${Math.floor(Math.random() * 10)}`,
            cover: 'https://via.placeholder.com/150'
        });
    }
    return books;
}

// Function to create book cards
function createBookCards(books) {
    const container = document.getElementById('book-container');
    books.forEach(book => {
        const card = document.createElement('div');
        card.className = 'book-card';
        card.innerHTML = `
            <img src="${book.cover}" alt="Book Cover">
            <h2>${book.title}</h2>
            <p>${book.author}</p>
            <p>${book.publisher}</p>
            <p>${book.publicationDate}</p>
            <button class="borrow-btn">Borrow</button>
        `;
        container.appendChild(card);
        
        // Add event listener to the borrow button
        const borrowBtn = card.querySelector('.borrow-btn');
        borrowBtn.addEventListener('click', () => {
            console.log(`${book.title} borrowed!`);
        });
    });
}

// Generate and display 100 book cards
const books = generateRandomBooks(100);
createBookCards(books);