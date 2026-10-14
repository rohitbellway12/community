-- ============================================================================
-- ONE-TIME MIGRATION: Shift Historical UTC Timestamps to Korea Standard Time (KST, +09:00)
-- Cut-off: 2026-10-13 16:51:40 (Records after this are already saved in KST)
-- ============================================================================

START TRANSACTION;

-- 1. Users Table (created_at, updated_at, last_seen_at)
UPDATE `users`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END,
    `last_seen_at` = CASE 
        WHEN `last_seen_at` IS NOT NULL AND `last_seen_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`last_seen_at`, INTERVAL 9 HOUR) 
        ELSE `last_seen_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 2. Posts Table (created_at, updated_at, deleted_at)
UPDATE `posts`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END,
    `deleted_at` = CASE 
        WHEN `deleted_at` IS NOT NULL AND `deleted_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`deleted_at`, INTERVAL 9 HOUR) 
        ELSE `deleted_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 3. Comments Table (created_at, updated_at, deleted_at)
UPDATE `comments`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END,
    `deleted_at` = CASE 
        WHEN `deleted_at` IS NOT NULL AND `deleted_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`deleted_at`, INTERVAL 9 HOUR) 
        ELSE `deleted_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 4. Likes Table (created_at, updated_at)
UPDATE `likes`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 5. User Visits Table (created_at, updated_at)
UPDATE `user_visits`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 6. Referrals Table (created_at, updated_at)
UPDATE `referrals`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 7. Notifications Table (created_at, updated_at)
UPDATE `notifications`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 8. Profiles Table (created_at, updated_at)
UPDATE `profiles`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 9. Follows Table (created_at, updated_at)
UPDATE `follows`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 10. User Stats Table (created_at, updated_at)
UPDATE `user_stats`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

-- 11. Saved Posts & Shares (created_at, updated_at)
UPDATE `saved_posts`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

UPDATE `shares`
SET 
    `created_at` = DATE_ADD(`created_at`, INTERVAL 9 HOUR),
    `updated_at` = CASE 
        WHEN `updated_at` IS NOT NULL AND `updated_at` < '2026-10-13 16:51:40' 
        THEN DATE_ADD(`updated_at`, INTERVAL 9 HOUR) 
        ELSE `updated_at` 
    END
WHERE `created_at` < '2026-10-13 16:51:40';

COMMIT;
