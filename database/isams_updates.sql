-- ============================================================
-- ISAMS Update 2026-09-25 — Complaints, Confiscated Item Letters,
-- Admin Messages. Run in Supabase SQL Editor. Idempotent.
-- ============================================================

-- CHANGE 3A — Complaints and Reports
CREATE TABLE IF NOT EXISTS complaints (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) DEFAULT 'Complaint',
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    status VARCHAR(30) DEFAULT 'Pending',
    admin_reply TEXT NULL,
    replied_at TIMESTAMP NULL,
    replied_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_complaints_student ON complaints(student_id);
CREATE INDEX IF NOT EXISTS idx_complaints_status ON complaints(status);
CREATE INDEX IF NOT EXISTS idx_complaints_anonymous ON complaints(is_anonymous);

-- CHANGE 4A — Confiscated Item Letters
CREATE TABLE IF NOT EXISTS confiscated_item_letters (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    item_description VARCHAR(500) NOT NULL,
    date_confiscated DATE NULL,
    confiscated_by VARCHAR(255) NULL,
    reason_confiscated TEXT NULL,
    letter_content TEXT NOT NULL,
    status VARCHAR(30) DEFAULT 'Submitted',
    admin_notes TEXT NULL,
    reviewed_at TIMESTAMP NULL,
    reviewed_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_letters_student ON confiscated_item_letters(student_id);
CREATE INDEX IF NOT EXISTS idx_letters_status ON confiscated_item_letters(status);

-- CHANGE 6A — Admin Messages (one-way admin → student)
CREATE TABLE IF NOT EXISTS admin_messages (
    id BIGSERIAL PRIMARY KEY,
    sent_by BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_messages_student ON admin_messages(student_id);
CREATE INDEX IF NOT EXISTS idx_messages_read ON admin_messages(is_read);

-- CHANGE 8 — ensure students.sex exists (already present in this database;
-- kept for completeness / other environments)
ALTER TABLE students ADD COLUMN IF NOT EXISTS sex VARCHAR(10);
