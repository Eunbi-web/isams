-- ─────────────────────────────────────────────────────────────────────────────
-- ISAMS Counselor Portal — Supabase SQL to run
-- ─────────────────────────────────────────────────────────────────────────────

-- 1) discipline_records table
CREATE TABLE IF NOT EXISTS discipline_records (
    id BIGSERIAL PRIMARY KEY,
    edp_number VARCHAR(20) NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    offense_category VARCHAR(20) DEFAULT 'Minor',
    description TEXT,
    incident_date DATE,
    guardian_name VARCHAR(255),
    guardian_contact VARCHAR(20),
    status VARCHAR(30) DEFAULT 'Open',
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_discipline_edp ON discipline_records(edp_number);
CREATE INDEX IF NOT EXISTS idx_discipline_category ON discipline_records(offense_category);
CREATE INDEX IF NOT EXISTS idx_discipline_status ON discipline_records(status);

-- 2) Follow-up columns used by "Complete Session" (counseling_sessions)
ALTER TABLE counseling_sessions ADD COLUMN IF NOT EXISTS follow_up_required BOOLEAN DEFAULT FALSE;
ALTER TABLE counseling_sessions ADD COLUMN IF NOT EXISTS follow_up_date DATE;

-- 3) Counselor test account (password is the word: password)
INSERT INTO users_all (name, email, password, role, is_active)
VALUES ('Guidance Counselor', 'counselor@isams.edu.ph', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'counselor', true);
