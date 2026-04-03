-- PostgreSQL Schema for Supabase

CREATE TABLE IF NOT EXISTS user_master (
    id SERIAL PRIMARY KEY,
    user_name VARCHAR(70) NOT NULL,
    email_id VARCHAR(70) NOT NULL,
    password VARCHAR(50) NOT NULL,
    user_type VARCHAR(5) NOT NULL,
    status SMALLINT NOT NULL,
    create_by VARCHAR(20) NOT NULL,
    create_on TIMESTAMP NOT NULL,
    update_by VARCHAR(20) NOT NULL,
    update_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subscription_tier VARCHAR(10) NOT NULL DEFAULT 'FREE' CHECK (subscription_tier IN ('FREE', 'PREMIUM'))
);

CREATE TABLE IF NOT EXISTS book_master (
    id SERIAL PRIMARY KEY,
    ref_no VARCHAR(50) NOT NULL,
    email_id VARCHAR(150) NOT NULL,
    book_title VARCHAR(255) NOT NULL,
    book_author VARCHAR(255) NOT NULL,
    book_description TEXT,
    book_genre VARCHAR(100) DEFAULT NULL,
    book_location VARCHAR(120) DEFAULT NULL,
    book_cover_image VARCHAR(255) DEFAULT NULL,
    status SMALLINT NOT NULL DEFAULT 1,
    create_by VARCHAR(150) DEFAULT NULL,
    create_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_data (
    id SERIAL PRIMARY KEY,
    userid VARCHAR(50) DEFAULT NULL,
    log_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS borrow_requests (
    id SERIAL PRIMARY KEY,
    book_id INTEGER NOT NULL,
    requester_email VARCHAR(70) NOT NULL,
    owner_email VARCHAR(70) NOT NULL,
    request_message VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    request_on TIMESTAMP NOT NULL,
    update_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Function and Triggers to emulate MySQL's ON UPDATE CURRENT_TIMESTAMP
CREATE OR REPLACE FUNCTION update_modified_column() 
RETURNS TRIGGER AS $$
BEGIN
    NEW.update_on = CURRENT_TIMESTAMP;
    RETURN NEW;   
END;
$$ language 'plpgsql';

CREATE TRIGGER update_user_master_modtime 
    BEFORE UPDATE ON user_master 
    FOR EACH ROW EXECUTE FUNCTION update_modified_column();

CREATE TRIGGER update_login_data_modtime 
    BEFORE UPDATE ON login_data 
    FOR EACH ROW EXECUTE FUNCTION update_modified_column();

CREATE TRIGGER update_borrow_requests_modtime 
    BEFORE UPDATE ON borrow_requests 
    FOR EACH ROW EXECUTE FUNCTION update_modified_column();

-- Seed Default users
INSERT INTO user_master (user_name, email_id, password, user_type, status, create_by, create_on, update_by)
SELECT 'Demo User', 'demo@lendnlearn.local', 'demo123', 'USER', 1, 'seed', NOW(), 'seed'
WHERE NOT EXISTS (
    SELECT 1 FROM user_master WHERE email_id = 'demo@lendnlearn.local'
);

INSERT INTO user_master (user_name, email_id, password, user_type, status, create_by, create_on, update_by)
SELECT 'Admin User', 'admin@lendnlearn.local', 'admin123', 'ADMIN', 1, 'seed', NOW(), 'seed'
WHERE NOT EXISTS (
    SELECT 1 FROM user_master WHERE email_id = 'admin@lendnlearn.local'
);
