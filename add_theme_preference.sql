-- Select the database
USE skillswap;

-- Add Theme_Preference column to users table
ALTER TABLE skillswap.users ADD COLUMN Theme_Preference VARCHAR(10) DEFAULT 'light';

-- Update existing users to have a default theme preference
UPDATE skillswap.users SET Theme_Preference = 'light' WHERE Theme_Preference IS NULL;
