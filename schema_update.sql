-- For PostgreSQL (Supabase)
ALTER TABLE book_master ADD COLUMN IF NOT EXISTS price NUMERIC(10, 2) NOT NULL DEFAULT 0.00;

-- For MySQL (uncomment if using MySQL)
-- ALTER TABLE book_master ADD COLUMN price DECIMAL(10, 2) NOT NULL DEFAULT 0.00;
