-- Core schema for Supabase Postgres
-- Users, roles, departments, attendance, events, leave requests, audit logs, attendance_logs

-- Note: run this in your Supabase project's SQL editor.

-- USERS TABLE (application users; separate from Supabase auth.users)
CREATE TABLE IF NOT EXISTS public.users (
    id                 bigserial PRIMARY KEY,
    name               varchar(255) NOT NULL,
    email              varchar(255) NOT NULL UNIQUE,
    email_verified_at  timestamp with time zone NULL,
    password           varchar(255) NOT NULL,
    role               varchar(50) NOT NULL DEFAULT 'employee', -- employee, hr, admin, super_admin
    faculty_id         varchar(255) UNIQUE NULL,
    position           varchar(255) NULL,
    department_id      bigint NULL,
    -- Face recognition fields (keep in sync with Laravel migration 2026_03_15_add_face_data_to_users_table)
    face_encodings     jsonb NULL,
    face_enrolled      boolean NOT NULL DEFAULT false,
    face_samples_count integer NOT NULL DEFAULT 0,
    face_enrolled_at   timestamp with time zone NULL,
    remember_token     varchar(100) NULL,
    created_at         timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at         timestamp(0) without time zone DEFAULT now() NOT NULL
);

-- Ensure roles are constrained to expected values
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'users_role_check_enum'
    ) THEN
        -- Ensure role column exists even if users table was pre-existing
        ALTER TABLE public.users
        ADD COLUMN IF NOT EXISTS role varchar(50) NOT NULL DEFAULT 'employee';

        ALTER TABLE public.users
        ADD CONSTRAINT users_role_check_enum
        CHECK (role IN ('employee', 'hr', 'admin', 'super_admin'));
    END IF;
END $$;

-- PASSWORD RESET TOKENS
CREATE TABLE IF NOT EXISTS public.password_reset_tokens (
    email       varchar(255) PRIMARY KEY,
    token       varchar(255) NOT NULL,
    created_at  timestamp(0) without time zone NULL
);

-- SESSIONS TABLE (Laravel session driver: database)
CREATE TABLE IF NOT EXISTS public.sessions (
    id           varchar(255) PRIMARY KEY,
    user_id      bigint NULL,
    ip_address   varchar(45) NULL,
    user_agent   text NULL,
    payload      text NOT NULL,
    last_activity integer NOT NULL
);

CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON public.sessions(last_activity);
CREATE INDEX IF NOT EXISTS sessions_user_id_index ON public.sessions(user_id);

-- DEPARTMENTS
CREATE TABLE IF NOT EXISTS public.departments (
    id          bigserial PRIMARY KEY,
    name        varchar(255) NOT NULL UNIQUE,
    code        varchar(50) UNIQUE NULL,
    description text NULL,
    is_active   boolean NOT NULL DEFAULT true,
    created_at  timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at  timestamp(0) without time zone DEFAULT now() NOT NULL
);

-- Ensure users.department_id FK
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'users_department_id_foreign'
    ) THEN
        -- Ensure department_id column exists even if users table was pre-existing
        ALTER TABLE public.users
        ADD COLUMN IF NOT EXISTS department_id bigint NULL;

        ALTER TABLE public.users
        ADD CONSTRAINT users_department_id_foreign
        FOREIGN KEY (department_id)
        REFERENCES public.departments(id)
        ON DELETE SET NULL;
    END IF;
END $$;

-- ATTENDANCE
CREATE TABLE IF NOT EXISTS public.attendance (
    id               bigserial PRIMARY KEY,
    user_id          bigint NOT NULL,
    attendance_date  date NOT NULL,
    time_in          time NULL,
    time_out         time NULL,
    status           varchar(20) NOT NULL DEFAULT 'absent',
    notes            text NULL,
    liveness_verified boolean NOT NULL DEFAULT false,
    created_at       timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at       timestamp(0) without time zone DEFAULT now() NOT NULL
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'attendance_status_check_enum'
    ) THEN
        -- Ensure status column exists on pre-existing attendance table
        ALTER TABLE public.attendance
        ADD COLUMN IF NOT EXISTS status varchar(20) NOT NULL DEFAULT 'absent';

        ALTER TABLE public.attendance
        ADD CONSTRAINT attendance_status_check_enum
        CHECK (status IN ('present', 'absent', 'late', 'half_day', 'leave'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'attendance_user_id_foreign'
    ) THEN
        -- Ensure user_id column exists even if attendance table was pre-existing
        ALTER TABLE public.attendance
        ADD COLUMN IF NOT EXISTS user_id bigint NOT NULL;

        ALTER TABLE public.attendance
        ADD CONSTRAINT attendance_user_id_foreign
        FOREIGN KEY (user_id)
        REFERENCES public.users(id)
        ON DELETE CASCADE;
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS attendance_user_id_index ON public.attendance(user_id);
CREATE INDEX IF NOT EXISTS attendance_attendance_date_index ON public.attendance(attendance_date);
CREATE UNIQUE INDEX IF NOT EXISTS attendance_user_date_unique ON public.attendance(user_id, attendance_date);

-- EVENTS
CREATE TABLE IF NOT EXISTS public.events (
    id          bigserial PRIMARY KEY,
    title       varchar(255) NOT NULL,
    description text NULL,
    start_date  timestamp(0) without time zone NOT NULL,
    end_date    timestamp(0) without time zone NULL,
    location    varchar(255) NULL,
    status      varchar(20) NOT NULL DEFAULT 'upcoming',
    created_by  bigint NULL,
    created_at  timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at  timestamp(0) without time zone DEFAULT now() NOT NULL
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'events_status_check_enum'
    ) THEN
        -- Ensure status column exists even if events table was pre-existing
        ALTER TABLE public.events
        ADD COLUMN IF NOT EXISTS status varchar(20) NOT NULL DEFAULT 'upcoming';

        ALTER TABLE public.events
        ADD CONSTRAINT events_status_check_enum
        CHECK (status IN ('upcoming', 'ongoing', 'completed', 'cancelled'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'events_created_by_foreign'
    ) THEN
        -- Ensure created_by column exists even if events table was pre-existing
        ALTER TABLE public.events
        ADD COLUMN IF NOT EXISTS created_by bigint NULL;

        ALTER TABLE public.events
        ADD CONSTRAINT events_created_by_foreign
        FOREIGN KEY (created_by)
        REFERENCES public.users(id)
        ON DELETE SET NULL;
    END IF;
END $$;

-- LEAVE REQUESTS
CREATE TABLE IF NOT EXISTS public.leave_requests (
    id               bigserial PRIMARY KEY,
    user_id          bigint NOT NULL,
    leave_type       varchar(20) NOT NULL DEFAULT 'vacation',
    start_date       date NOT NULL,
    end_date         date NOT NULL,
    reason           text NULL,
    status           varchar(20) NOT NULL DEFAULT 'pending',
    approved_by      bigint NULL,
    rejection_reason text NULL,
    created_at       timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at       timestamp(0) without time zone DEFAULT now() NOT NULL
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'leave_requests_leave_type_check_enum'
    ) THEN
        -- Ensure leave_type column exists on pre-existing leave_requests table
        ALTER TABLE public.leave_requests
        ADD COLUMN IF NOT EXISTS leave_type varchar(20) NOT NULL DEFAULT 'vacation';

        ALTER TABLE public.leave_requests
        ADD CONSTRAINT leave_requests_leave_type_check_enum
        CHECK (leave_type IN ('sick', 'vacation', 'personal', 'emergency', 'other'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'leave_requests_status_check_enum'
    ) THEN
        -- Ensure status column exists on pre-existing leave_requests table
        ALTER TABLE public.leave_requests
        ADD COLUMN IF NOT EXISTS status varchar(20) NOT NULL DEFAULT 'pending';

        ALTER TABLE public.leave_requests
        ADD CONSTRAINT leave_requests_status_check_enum
        CHECK (status IN ('pending', 'approved', 'rejected'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'leave_requests_user_id_foreign'
    ) THEN
        -- Ensure user_id column exists even if leave_requests table was pre-existing
        ALTER TABLE public.leave_requests
        ADD COLUMN IF NOT EXISTS user_id bigint NOT NULL;

        ALTER TABLE public.leave_requests
        ADD CONSTRAINT leave_requests_user_id_foreign
        FOREIGN KEY (user_id)
        REFERENCES public.users(id)
        ON DELETE CASCADE;
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'leave_requests_approved_by_foreign'
    ) THEN
        -- Ensure approved_by column exists even if leave_requests table was pre-existing
        ALTER TABLE public.leave_requests
        ADD COLUMN IF NOT EXISTS approved_by bigint NULL;

        ALTER TABLE public.leave_requests
        ADD CONSTRAINT leave_requests_approved_by_foreign
        FOREIGN KEY (approved_by)
        REFERENCES public.users(id)
        ON DELETE SET NULL;
    END IF;
END $$;

-- AUDIT LOGS
CREATE TABLE IF NOT EXISTS public.audit_logs (
    id           bigserial PRIMARY KEY,
    user_id      bigint NULL,
    action       varchar(255) NOT NULL,
    model_type   varchar(255) NULL,
    model_id     bigint NULL,
    changes      text NULL,
    ip_address   varchar(45) NULL,
    user_agent   text NULL,
    created_at   timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at   timestamp(0) without time zone DEFAULT now() NOT NULL
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'audit_logs_user_id_foreign'
    ) THEN
        -- Ensure user_id column exists even if audit_logs table was pre-existing
        ALTER TABLE public.audit_logs
        ADD COLUMN IF NOT EXISTS user_id bigint NULL;

        ALTER TABLE public.audit_logs
        ADD CONSTRAINT audit_logs_user_id_foreign
        FOREIGN KEY (user_id)
        REFERENCES public.users(id)
        ON DELETE SET NULL;
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS audit_logs_user_id_index ON public.audit_logs(user_id);
CREATE INDEX IF NOT EXISTS audit_logs_created_at_index ON public.audit_logs(created_at);

-- ATTENDANCE LOGS
CREATE TABLE IF NOT EXISTS public.attendance_logs (
    id              bigserial PRIMARY KEY,
    employee_id     bigint NOT NULL,
    log_date        date NOT NULL,
    period          varchar(2) NOT NULL DEFAULT 'AM',
    punch_type      varchar(3) NOT NULL DEFAULT 'IN',
    punched_at      timestamp(0) without time zone NOT NULL,
    method          varchar(50) NOT NULL DEFAULT 'face_recognition',
    confidence      numeric(5,2) NULL,
    liveness_passed boolean NOT NULL DEFAULT false,
    photo_path      varchar(255) NULL,
    notes           text NULL,
    created_at      timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at      timestamp(0) without time zone DEFAULT now() NOT NULL
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'attendance_logs_period_check_enum'
    ) THEN
        -- Ensure period column exists on pre-existing attendance_logs table
        ALTER TABLE public.attendance_logs
        ADD COLUMN IF NOT EXISTS period varchar(2) NOT NULL DEFAULT 'AM';

        ALTER TABLE public.attendance_logs
        ADD CONSTRAINT attendance_logs_period_check_enum
        CHECK (period IN ('AM', 'PM'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'attendance_logs_punch_type_check_enum'
    ) THEN
        -- Ensure punch_type column exists on pre-existing attendance_logs table
        ALTER TABLE public.attendance_logs
        ADD COLUMN IF NOT EXISTS punch_type varchar(3) NOT NULL DEFAULT 'IN';

        ALTER TABLE public.attendance_logs
        ADD CONSTRAINT attendance_logs_punch_type_check_enum
        CHECK (punch_type IN ('IN', 'OUT'));
    END IF;
END $$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'attendance_logs_employee_id_foreign'
    ) THEN
        -- Ensure employee_id column exists even if attendance_logs table was pre-existing
        ALTER TABLE public.attendance_logs
        ADD COLUMN IF NOT EXISTS employee_id bigint NOT NULL;

        ALTER TABLE public.attendance_logs
        ADD CONSTRAINT attendance_logs_employee_id_foreign
        FOREIGN KEY (employee_id)
        REFERENCES public.users(id)
        ON DELETE CASCADE;
    END IF;
END $$;

-- Ensure log_date column exists on pre-existing attendance_logs table
ALTER TABLE public.attendance_logs
    ADD COLUMN IF NOT EXISTS log_date date;

CREATE INDEX IF NOT EXISTS attendance_logs_employee_id_index ON public.attendance_logs(employee_id);
CREATE INDEX IF NOT EXISTS attendance_logs_log_date_index ON public.attendance_logs(log_date);
CREATE INDEX IF NOT EXISTS attendance_logs_period_index ON public.attendance_logs(period);
CREATE INDEX IF NOT EXISTS attendance_logs_employee_date_period_index ON public.attendance_logs(employee_id, log_date, period);
