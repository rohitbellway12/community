<?php
$baseUrl = "http://localhost/community/api/v1";

$collection = [
    "info" => [
        "name" => "REIAC Community API",
        "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
        "_postman_id" => "c355115a-fb02-4aa7-829c-eaec07a329ce",
        "description" => "Complete API Reference - All 88 endpoints organized by group for Web and Mobile App",
        "protocolProfileBehavior" => ["disableBodyPruning" => true],
    ],
    "item" => [],
    "variable" => [
        ["id" => "baseUrl", "key" => "baseUrl", "value" => $baseUrl, "type" => "string"],
        ["id" => "token", "key" => "token", "value" => "<YOUR_TOKEN>", "type" => "string"],
        ["id" => "post_id", "key" => "post_id", "value" => "1", "type" => "string"],
        ["id" => "comment_id", "key" => "comment_id", "value" => "1", "type" => "string"],
        ["id" => "user_id", "key" => "user_id", "value" => "2", "type" => "string"],
        ["id" => "follower_id", "key" => "follower_id", "value" => "3", "type" => "string"],
        ["id" => "group_id", "key" => "group_id", "value" => "1", "type" => "string"],
        ["id" => "test_id", "key" => "test_id", "value" => "1", "type" => "string"],
        ["id" => "attempt_id", "key" => "attempt_id", "value" => "1", "type" => "string"],
        ["id" => "slug", "key" => "slug", "value" => "my-event", "type" => "string"],
        ["id" => "id_or_username", "key" => "id_or_username", "value" => "admin", "type" => "string"],
    ],
];

// Helper: create a request item
function req($name, $method, $endpoint, $auth = false, $body = null, $extraHeaders = []) {
    $headers = [
        ["key" => "Accept", "value" => "application/json", "type" => "text"],
        ["key" => "Content-Type", "value" => "application/json", "type" => "text"],
    ];
    if ($auth) {
        $headers[] = ["key" => "Authorization", "value" => "Bearer {{token}}", "type" => "text"];
    }
    foreach ($extraHeaders as $h) {
        $parts = explode(": ", $h, 2);
        $headers[] = ["key" => $parts[0], "value" => $parts[1] ?? '', "type" => "text"];
    }
    $item = [
        "name" => $name,
        "request" => [
            "method" => $method,
            "header" => $headers,
            "url" => [
                "raw" => "{{baseUrl}}" . $endpoint,
                "host" => ["{{baseUrl}}"],
                "path" => explode("/", ltrim($endpoint, "/")),
                "query" => [],
            ],
        ],
        "response" => [],
    ];
    if ($body !== null) {
        $item["request"]["body"] = ["mode" => "raw", "raw" => $body, "options" => []];
    }
    return $item;
}

// =====================================================
// 🔓 GUEST APIs (No Auth)
// =====================================================
$guestFolder = [
    "name" => "🔓 Guest APIs (No Auth)",
    "item" => [
        // Auth (Public)
        [
            "name" => "🔑 Authentication",
            "item" => [
                req("Register", "POST", "/auth/register", false, '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123","country_id":1,"device_name":"Mobile"}'),
                req("Login", "POST", "/auth/login", false, '{"email":"test@example.com","password":"password123","device_name":"Mobile"}'),
                req("Forgot Password", "POST", "/auth/forgot-password", false, '{"email":"test@example.com"}'),
                req("Verify OTP", "POST", "/auth/verify-otp", false, '{"email":"test@example.com","otp":"123456"}'),
                req("Reset Password", "POST", "/auth/reset-password", false, '{"email":"test@example.com","password":"newpassword123","password_confirmation":"newpassword123","reset_token":"abc"}'),
            ],
        ],
        // Public Data
        [
            "name" => "🌍 Public Data",
            "item" => [
                req("Get Countries", "GET", "/countries"),
                req("Get Categories", "GET", "/categories"),
                req("Get Tags", "GET", "/tags"),
                req("Get Banners", "GET", "/banners"),
                req("Get Guidelines", "GET", "/guidelines"),
                req("Get Leaderboard", "GET", "/leaderboard"),
            ],
        ],
        // Posts (Public)
        [
            "name" => "📝 Posts (Public)",
            "item" => [
                req("Get All Posts", "GET", "/posts"),
                req("Get Single Post", "GET", "/posts/{post_id}"),
            ],
        ],
        // Comments (Public)
        [
            "name" => "💬 Comments (Public)",
            "item" => [
                req("Get Post Comments", "GET", "/posts/{post_id}/comments"),
                req("Get Comment Replies", "GET", "/comments/{comment_id}/replies"),
            ],
        ],
        // User Profiles (Public)
        [
            "name" => "👤 User Profiles (Public)",
            "item" => [
                req("Get User Profile", "GET", "/users/{id_or_username}"),
                req("Get User Posts", "GET", "/users/{id_or_username}/posts"),
                req("Get User Followers", "GET", "/users/{user_id}/followers"),
                req("Get User Following", "GET", "/users/{user_id}/following"),
            ],
        ],
        // Groups (Public)
        [
            "name" => "👥 Groups (Public)",
            "item" => [
                req("Get All Groups", "GET", "/groups"),
                req("Get Group Details", "GET", "/groups/{group_id}"),
            ],
        ],
        // Tests (Public)
        [
            "name" => "🎓 Tests (Public)",
            "item" => [
                req("Get All Tests", "GET", "/tests"),
                req("Get Single Test", "GET", "/tests/{test_id}"),
            ],
        ],
        // Events (Public)
        [
            "name" => "📅 Events (Public)",
            "item" => [
                req("Active Event", "GET", "/active-event"),
                req("Event Details", "GET", "/events/{slug}"),
                req("Event Leaderboard", "GET", "/events/{slug}/leaderboard"),
            ],
        ],
    ],
];

// =====================================================
// 🔐 AUTHENTICATED APIs
// =====================================================
$authFolder = [
    "name" => "🔐 Authenticated APIs (Auth Required)",
    "item" => [
        // Auth (Protected)
        [
            "name" => "🔑 Authentication",
            "item" => [
                req("Get My Profile", "GET", "/auth/me", true),
                req("Logout", "POST", "/auth/logout", true),
                req("Change Password", "POST", "/auth/change-password", true, '{"current_password":"oldpassword","password":"newpassword123","password_confirmation":"newpassword123"}'),
                req("Delete Account", "DELETE", "/auth/delete-account", true, '{"password":"password123"}'),
            ],
        ],
        // User Presence & Device
        [
            "name" => "📡 User Presence & Device",
            "item" => [
                req("Ping (Heartbeat)", "POST", "/ping", true, '{}', ["X-Device-Type: Mobile", "X-Device-OS: Android 14", "X-Browser: Chrome"]),
                req("Register Device", "POST", "/device", true, '{"device_name":"Mobile","device_type":"Mobile","device_os":"Android 14","browser":"Chrome","app_version":"1.0.4"}'),
                req("Get Device Info", "GET", "/device", true),
                req("Register FCM Token", "POST", "/device-tokens", true, '{"fcm_token":"fcm_token_here","device_type":"android","app_version":"1.0.4"}'),
                req("Get Device Tokens", "GET", "/device-tokens", true),
                req("Delete Device Token", "DELETE", "/device-tokens/{token}", true),
            ],
        ],
        // Posts
        [
            "name" => "📝 Posts",
            "item" => [
                req("Get Saved Posts", "GET", "/posts/saved", true),
                req("Create Post", "POST", "/posts", true, '{"title":"My Discussion Post","content":"Hello Community! This is my post.","category_id":1,"tags":[1,2],"visibility":"public"}'),
                req("Update Post", "PUT", "/posts/{post_id}", true, '{"title":"Updated Title","content":"Updated content."}'),
                req("Delete Post", "DELETE", "/posts/{post_id}", true),
                req("Like Post", "POST", "/posts/{post_id}/like", true),
                req("Save Post", "POST", "/posts/{post_id}/save", true),
                req("Share Post", "POST", "/posts/{post_id}/share", true, '{"message":"Check this discussion!"}'),
                req("Mark Solved", "POST", "/posts/{post_id}/mark-solved", true),
            ],
        ],
        // Comments
        [
            "name" => "💬 Comments",
            "item" => [
                req("Create Comment", "POST", "/posts/{post_id}/comments", true, '{"content":"This is a comment.","parent_id":null}'),
                req("Update Comment", "PUT", "/comments/{comment_id}", true, '{"content":"Updated comment content."}'),
                req("Delete Comment", "DELETE", "/comments/{comment_id}", true),
                req("Like Comment", "POST", "/comments/{comment_id}/like", true),
            ],
        ],
        // Notifications
        [
            "name" => "🔔 Notifications",
            "item" => [
                req("Get Notifications", "GET", "/notifications", true),
                req("Unread Count", "GET", "/notifications/unread-count", true),
                req("Mark Notification Read", "PUT", "/notifications/{id}/read", true),
                req("Mark All Read", "PUT", "/notifications/read-all", true),
                req("Delete Notification", "DELETE", "/notifications/{id}", true),
            ],
        ],
        // Profile & Activity
        [
            "name" => "👤 Profile & Activity",
            "item" => [
                req("Update Profile", "POST", "/profile/update", true, '{"name":"User Name","username":"username123","bio":"Community Member","location":"Seoul, Korea","country_id":1}'),
                req("Profile Activities", "GET", "/profile/activities", true),
                req("User Activity", "GET", "/user/activity", true),
            ],
        ],
        // Reports
        [
            "name" => "🚩 Reports",
            "item" => [
                req("Report Content", "POST", "/reports", true, '{"reportable_type":"App\\Models\\Post","reportable_id":1,"reason":"Spam","description":"Spam discussion"}'),
            ],
        ],
        // Follow
        [
            "name" => "👥 Follow",
            "item" => [
                req("Get My Followers", "GET", "/user/followers", true),
                req("Get My Following", "GET", "/user/following", true),
                req("Follow Status", "GET", "/users/{user_id}/follow/status", true),
                req("Follow User", "POST", "/users/{user_id}/follow", true),
                req("Unfollow User", "DELETE", "/users/{user_id}/follow", true),
                req("Remove Follower", "DELETE", "/users/{follower_id}/follower/remove", true),
            ],
        ],
        // Groups
        [
            "name" => "👥 Groups",
            "item" => [
                req("Create Group", "POST", "/groups", true, '{"name":"Study Group","description":"Group for exams and studies","visibility":"public","members":[2,3]}'),
                req("Update Group", "PUT", "/groups/{group_id}", true, '{"name":"Updated Study Group","description":"Updated description","visibility":"public"}'),
                req("Delete Group", "DELETE", "/groups/{group_id}", true),
                req("Join Group", "POST", "/groups/{group_id}/join", true),
                req("Leave Group", "DELETE", "/groups/{group_id}/leave", true),
                req("Invite Member", "POST", "/groups/{group_id}/invite", true, '{"user_id":3}'),
                req("Remove Member", "DELETE", "/groups/{group_id}/members/{user_id}", true),
                req("Get Members", "GET", "/groups/{group_id}/members", true),
                req("Get Requests", "GET", "/groups/{group_id}/requests", true),
                req("Get Invitations", "GET", "/groups/{group_id}/invitations", true),
                req("Create Group Post", "POST", "/groups/{group_id}/posts", true, '{"title":"Announcement","content":"Welcome members!"}'),
                req("Accept Request", "POST", "/groups/{group_id}/requests/{user_id}/accept", true),
                req("Reject Request", "POST", "/groups/{group_id}/requests/{user_id}/reject", true),
                req("Accept Invitation", "POST", "/groups/{group_id}/invitations/accept", true),
                req("Reject Invitation", "POST", "/groups/{group_id}/invitations/reject", true),
            ],
        ],
        // Events (Auth)
        [
            "name" => "📅 Events (Auth)",
            "item" => [
                req("My Event Rank", "GET", "/events/{slug}/my-rank", true),
            ],
        ],
        // Referral
        [
            "name" => "🏆 Referral",
            "item" => [
                req("My Referral", "GET", "/my-referral", true),
            ],
        ],
        // Tests (Auth)
        [
            "name" => "🎓 Online Tests",
            "item" => [
                req("My Attempts", "GET", "/tests/my-attempts", true),
                req("Start Test", "POST", "/tests/{test_id}/start", true),
                req("Take Test", "GET", "/tests/{test_id}/attempts/{attempt_id}", true),
                req("Save Answer", "POST", "/tests/{test_id}/attempts/{attempt_id}/answer", true, '{"question_id":1,"selected_option_id":2}'),
                req("Clear Answer", "POST", "/tests/{test_id}/attempts/{attempt_id}/clear-answer", true, '{"question_id":1}'),
                req("Mark for Review", "POST", "/tests/{test_id}/attempts/{attempt_id}/mark-review", true, '{"question_id":1}'),
                req("Submit Test", "POST", "/tests/{test_id}/attempts/{attempt_id}/submit", true),
                req("Get Test Result", "GET", "/tests/{test_id}/attempts/{attempt_id}/result", true),
            ],
        ],
    ],
];

$collection["item"] = [$guestFolder, $authFolder];

$json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$filename = __DIR__ . '/REIAC Community API.postman_collection.json';
file_put_contents($filename, $json);

echo "Postman collection generated: $filename\n";

function countEndpoints($items) {
    $count = 0;
    foreach ($items as $item) {
        if (isset($item['request'])) {
            $count++;
        } elseif (isset($item['item'])) {
            $count += countEndpoints($item['item']);
        }
    }
    return $count;
}

echo "Total endpoints: " . countEndpoints($collection["item"]) . "\n";
