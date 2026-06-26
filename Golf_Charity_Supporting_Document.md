---
title: Golf Charity Subscription Platform Supporting Document
author: Development Team
date: 2026-06-11
---

# Golf Charity Subscription Platform Supporting Document

## 1. Introduction & Abstract
The **Golf Charity Subscription Platform** is a full-stack web application designed to support a golf charity organization. It provides a seamless interface for managing user memberships, facilitating secure charity donations, and handling subscription fees. The platform centralizes member engagement through automated payment processing and a comprehensive administrative dashboard.

## 2. Key Features
- **User Authentication & Authorization**: Secure sign-up, login, and access control for general members and administrators.
- **Subscription Management**: Complete workflow for users to subscribe to different charity tiers, view their active memberships, and manage renewals.
- **Payment Gateway Integration**: Live payment processing powered by **Razorpay**, ensuring secure transactions for donations and subscription fees.
- **Administrator Dashboard**: A central command interface for administrators to track active subscriptions, monitor revenue, and manage user accounts.
- **Responsive Interface**: Server-side rendered views designed to provide a smooth experience across devices.

## 3. Technology Stack
The platform is built on a scalable Node.js architecture:
- **Backend Environment**: Node.js and Express.js (handling routing, business logic, and API integrations).
- **Frontend / Templating**: EJS (Embedded JavaScript) for dynamic, server-side rendered HTML views.
- **Database & Storage**: Supabase (PostgreSQL) utilized for robust relational data storage and secure backend services.
- **Payment Processing**: Razorpay API integration for end-to-end payment management.

## 4. System Architecture & Flow
- **Client-Server Interaction**: The client interacts with EJS views served by the Node.js Express application.
- **Payment Flow**: When a user initiates a subscription, the backend creates an order via the Razorpay API. The client completes the transaction using the Razorpay checkout, and the backend verifies the payment signature before updating the user's subscription status in the database.
- **Data Management**: User profiles, subscription histories, and transaction logs are securely stored in the Supabase PostgreSQL database.

## 5. Setup & Deployment Instructions
### Local Environment
1. Clone the repository containing the Node.js application.
2. Install dependencies by running `npm install` in the project root.
3. Configure environment variables in a `.env` file, including:
   - `PORT` (e.g., 3000)
   - `SUPABASE_URL` and `SUPABASE_KEY`
   - `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET`
4. Start the development server using `npm run dev` or `node index.js`.
5. Access the application locally at `http://localhost:3000`.

### Production Environment
- Ensure all environment variables are securely set in the hosting provider's dashboard.
- The application can be containerized or directly hosted on platforms supporting Node.js (e.g., Render, Heroku).

## 6. Conclusion
By integrating modern backend technologies with reliable payment processing and secure data storage, the Golf Charity Subscription Platform successfully streamlines the donation and membership workflow. It ensures transparency for administrators and a frictionless experience for members supporting the charity.
