-- Fix unique constraints for project-scoped entities
-- This script changes unique constraints from global to project-scoped

-- 1. Fix services table
-- Drop the old unique index on 'name'
DROP INDEX IF EXISTS services_name_unique;
-- Create new composite unique index on (project_id, name)
CREATE UNIQUE INDEX services_project_id_name_unique ON services(project_id, name);

-- 2. Fix matches table
-- Drop the old unique index on 'name'
DROP INDEX IF EXISTS matches_name_unique;
-- Create new composite unique index on (project_id, name)
CREATE UNIQUE INDEX matches_project_id_name_unique ON matches(project_id, name);

-- 3. Fix deltas table
-- Drop the old unique index on 'name'
DROP INDEX IF EXISTS deltas_name_unique;
-- Create new composite unique index on (project_id, name)
CREATE UNIQUE INDEX deltas_project_id_name_unique ON deltas(project_id, name);

-- 4. Fix rules table
-- Drop the old unique index on 'name'
DROP INDEX IF EXISTS rules_name_unique;
-- Create new composite unique index on (project_id, name)
CREATE UNIQUE INDEX rules_project_id_name_unique ON rules(project_id, name);
