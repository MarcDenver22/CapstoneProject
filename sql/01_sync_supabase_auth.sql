-- Optional: link Supabase auth.users to public.users
-- This script assumes you are using Supabase Auth for primary authentication
-- and keeping application profile data in public.users.

-- 1) Ensure auth schema exists (managed by Supabase) and extension enabled
-- DO NOT modify auth schema structure directly; we only reference it.

-- 2) Add column to public.users to track auth.user id (UUID)
ALTER TABLE public.users
ADD COLUMN IF NOT EXISTS auth_user_id uuid UNIQUE;

-- 3) Helper function: upsert user profile when a Supabase auth user is created/updated
CREATE OR REPLACE FUNCTION public.sync_profile_from_auth()
RETURNS trigger AS $$
BEGIN
  -- If a matching profile exists, update it; otherwise insert new
  INSERT INTO public.users (auth_user_id, name, email, password, role, created_at, updated_at)
  VALUES (NEW.id, COALESCE(NEW.raw_user_meta_data->>'name', NEW.email), NEW.email, '', 'employee', now(), now())
  ON CONFLICT (auth_user_id) DO UPDATE
  SET
    email = EXCLUDED.email,
    name = COALESCE(EXCLUDED.name, public.users.name),
    updated_at = now();

  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- 4) Trigger on auth.users to keep public.users in sync
DROP TRIGGER IF EXISTS on_auth_user_change ON auth.users;

CREATE TRIGGER on_auth_user_change
AFTER INSERT OR UPDATE ON auth.users
FOR EACH ROW EXECUTE FUNCTION public.sync_profile_from_auth();
