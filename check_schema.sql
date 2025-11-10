-- Check table structures
.schema services
.schema matches
.schema deltas
.schema rules

-- Check indexes
SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='services';
SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='matches';
SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='deltas';
SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='rules';
