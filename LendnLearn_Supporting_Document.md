---
title: LendnLearn Project Supporting Document
author: LendnLearn Development Team
date: 2026-06-11
---

# LendnLearn Project Supporting Document

## 1. Introduction & Abstract
**LendnLearn** is a comprehensive book exchange and library management platform designed to facilitate the lending and borrowing of books among users. The platform bridges the gap between readers who have books to share and those seeking new knowledge, fostering a community of continuous learning. It includes secure user authentication, advanced book search, borrow request management, and a dedicated administrator dashboard.

## 2. Key Features
- **User Authentication & Authorization**: Secure login and registration with distinct roles for Regular Users and Administrators. Includes session management and tracking.
- **Subscription Tiers**: Users can access features based on their subscription status (`FREE` vs `PREMIUM`).
- **Book Management**: Users can upload books they wish to share, providing details such as Title, Author, Description, Genre, Location, and a Cover Image.
- **Borrowing Workflow**: Integrated request system allowing users to request a book from its owner, who can then review and manage pending borrow requests.
- **Advanced Search**: Filter and search through the book directory to easily find books by title, genre, or author.
- **Administrator Dashboard**: A central interface for admins to manage users, oversee platform activity, and monitor the book catalog.

## 3. Technology Stack
The platform is built using a robust and scalable architecture:
- **Frontend**: HTML5, Vanilla JavaScript, CSS3 (Custom styling including `book-style.css`, `login.css`, `styles.css`)
- **Backend**: PHP (Core backend logic, API endpoints, session handling)
- **Database**: PostgreSQL (managed via Supabase) for robust data storage. (Initial prototypes used MySQL as defined in `database.sql`).
- **Infrastructure/Deployment**: Render, Docker (containerized deployment using `Dockerfile`), Supabase Storage for cover image hosting.

## 4. System Architecture & Database Schema
The system utilizes a relational database model encompassing the following core entities:
- **`user_master`**: Stores user profiles, authentication details, role (`USER`, `ADMIN`), and subscription tier.
- **`book_master`**: Contains records of all uploaded books, including references to the uploader, metadata (title, author, genre), and current status.
- **`borrow_requests`**: Facilitates the transaction between borrowers and lenders. Tracks `book_id`, `requester_email`, `owner_email`, and request `status` (Pending, Approved, Rejected).
- **`login_data`**: Maintains an audit trail of user login sessions, timestamps, and IP addresses.

## 5. Security & Data Flow
- All user passwords and sensitive data are processed securely via the backend PHP scripts (`login_verify.php`, `register.php`).
- Session states are monitored (`sessionExpire.php`, `logout.php`) to ensure data protection and proper access control.
- Input fields are validated on the client side using JavaScript (`login.js`, `upload.js`) and sanitized on the server to prevent SQL injection.

## 6. Setup & Deployment Instructions
### Local Environment
1. Clone the repository and navigate to the project directory.
2. Start a local PHP server or use XAMPP/WAMP.
   ```bash
   php -S localhost:8000
   ```
3. Import the `database.sql` (or `database_supabase.sql`) into your database.
4. Update connection strings in the `config` directory to match your local database credentials.

### Production Environment
The project is containerized and configured for automated deployment via Render.
- Push changes to the main branch connected to Render.
- Ensure environment variables for the Supabase Database and Storage are securely configured in the Render Dashboard.
- The `Dockerfile` handles the necessary PHP extensions and setup for the cloud environment.

## 7. Conclusion
LendnLearn provides an intuitive, reliable, and scalable foundation for a digital book exchange community. By leveraging modern cloud tools (Render, Supabase) and a lightweight PHP backend, the platform is designed for high availability and easy maintenance.
