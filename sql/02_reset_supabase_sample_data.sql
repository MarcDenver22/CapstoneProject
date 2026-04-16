-- Reset script for Supabase database
-- Use this when you want to wipe out existing/sample data
-- from the main application tables but keep the schema.
--
-- Run this in the Supabase SQL editor targeting your main database.

BEGIN;

-- Safely truncate tables only if they exist.
DO $$
BEGIN
    -- Child/detail tables first
    IF to_regclass('public.attendance_logs') IS NOT NULL THEN
        TRUNCATE TABLE public.attendance_logs RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.attendance') IS NOT NULL THEN
        TRUNCATE TABLE public.attendance RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.leave_requests') IS NOT NULL THEN
        TRUNCATE TABLE public.leave_requests RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.audit_logs') IS NOT NULL THEN
        TRUNCATE TABLE public.audit_logs RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.events') IS NOT NULL THEN
        TRUNCATE TABLE public.events RESTART IDENTITY CASCADE;
    END IF;

    -- Reference data / lookups
    IF to_regclass('public.departments') IS NOT NULL THEN
        TRUNCATE TABLE public.departments RESTART IDENTITY CASCADE;
    END IF;

    -- Auth-related tables owned by the app (NOT Supabase auth.users)
    IF to_regclass('public.sessions') IS NOT NULL THEN
        TRUNCATE TABLE public.sessions RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.password_reset_tokens') IS NOT NULL THEN
        TRUNCATE TABLE public.password_reset_tokens RESTART IDENTITY CASCADE;
    END IF;

    IF to_regclass('public.users') IS NOT NULL THEN
        TRUNCATE TABLE public.users RESTART IDENTITY CASCADE;
    END IF;
END $$;

COMMIT;

-- If you really want to drop and recreate everything, you can instead:
--   DROP TABLE ... CASCADE;  -- for each table you own
-- and then re-run 00_schema_core.sql.
-- Be careful not to touch the Supabase-managed schemas like auth, storage, realtime.
