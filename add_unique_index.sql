-- Add unique constraint to prevent duplicate registrations
-- Run this SQL to add an index on event_id and email to prevent duplicates

ALTER TABLE registrations 
ADD UNIQUE INDEX idx_event_email (event_id, email);

