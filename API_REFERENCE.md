# 📡 REIAC Community API - Complete Reference
# Import into Postman: Use the JSON file OR follow curl commands below
# Base URL: http://localhost/community/api/v1

---

# ==========================================
# 🔐 AUTHENTICATION
# ==========================================

## 1. Register (Mobile App)
**POST** /auth/register
**Auth:** ❌ No Auth Required

### Headers:
```
Content-Type: application/json
```

### Body (JSON):
```json
{
    "name": "Rahul Sharma",
    "email": "rahul@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "country_id": 1,
    "device_name": "Samsung Galaxy S24",
    "fcm_token": "fcm_token_here_optional",
    "referral_code": "REIAC12345"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Rahul Sharma","email":"rahul@example.com","password":"password123","password_confirmation":"password123","country_id":1,"device_name":"Samsung Galaxy S24","fcm_token":"fcm_token_here"}'
```

### Response:
```json
{
    "success": true,
    "message": "Registration successful.",
    "data": {
        "user": { ... },
        "access_token": "eyJ0eXAi...",
        "token_type": "Bearer",
        "referral_code": "REIAC12345"
    }
}
```

---

## 2. Login (Mobile App)
**POST** /auth/login
**Auth:** ❌ No Auth Required

### Headers:
```
Content-Type: application/json
```

### Body (JSON):
```json
{
    "email": "rahul@example.com",
    "password": "password123",
    "device_name": "Samsung Galaxy S24",
    "fcm_token": "fcm_token_here"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"rahul@example.com","password":"password123","device_name":"Samsung Galaxy S24","fcm_token":"fcm_token_here"}'
```

---

## 3. Get My Profile
**GET** /auth/me
**Auth:** ✅ Bearer Token Required

### Headers:
```
Authorization: Bearer <YOUR_TOKEN>
Accept: application/json
```

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/auth/me \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Accept: application/json"
```

---

## 4. Logout
**POST** /auth/logout
**Auth:** ✅ Bearer Token Required

### Headers:
```
Authorization: Bearer <YOUR_TOKEN>
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/logout \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 5. Change Password
**POST** /auth/change-password
**Auth:** ✅ Bearer Token Required

### Headers:
```
Authorization: Bearer <YOUR_TOKEN>
Content-Type: application/json
```

### Body (JSON):
```json
{
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/change-password \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"current_password":"oldpassword","password":"newpassword123","password_confirmation":"newpassword123"}'
```

---

## 6. Delete Account
**DELETE** /auth/delete-account
**Auth:** ✅ Bearer Token Required

### Headers:
```
Authorization: Bearer <YOUR_TOKEN>
Content-Type: application/json
```

### Body (JSON):
```json
{
    "password": "password123"
}
```

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/auth/delete-account \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"password":"password123"}'
```

---

## 7. Forgot Password
**POST** /auth/forgot-password
**Auth:** ❌ No Auth Required

### Body (JSON):
```json
{
    "email": "rahul@example.com"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"rahul@example.com"}'
```

---

## 8. Verify OTP
**POST** /auth/verify-otp
**Auth:** ❌ No Auth Required

### Body (JSON):
```json
{
    "email": "rahul@example.com",
    "otp": "123456"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{"email":"rahul@example.com","otp":"123456"}'
```

---

## 9. Reset Password
**POST** /auth/reset-password
**Auth:** ❌ No Auth Required

### Body (JSON):
```json
{
    "email": "rahul@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123",
    "reset_token": "abc123reset_token",
    "device_name": "Samsung Galaxy S24"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{"email":"rahul@example.com","password":"newpassword123","password_confirmation":"newpassword123","reset_token":"abc123"}'
```

---

# ==========================================
# 📡 USER PRESENCE & DEVICE
# ==========================================

## 10. Ping (Heartbeat - Mark Online)
**POST** /ping
**Auth:** ✅ Bearer Token Required
**Description:** Updates last_seen_at, device info, IP address, and optionally FCM token

### Headers:
```
Authorization: Bearer <YOUR_TOKEN>
Content-Type: application/json
X-Device-Type: Mobile
X-Device-OS: Android 14
X-Browser: Chrome
```

### Body (JSON) - FCM Token optional:
```json
{
    "fcm_token": "fcm_token_here_optional"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/ping \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -H "X-Device-Type: Mobile" \
  -H "X-Device-OS: Android 14" \
  -H "X-Browser: Chrome" \
  -d '{"fcm_token":"fcm_token_here"}'
```

---

## 11. Register Device
**POST** /device
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "device_name": "Samsung Galaxy S24",
    "device_type": "Mobile",
    "device_os": "Android 14",
    "browser": "Chrome",
    "app_version": "1.0.4"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/device \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"device_name":"Samsung Galaxy S24","device_type":"Mobile","device_os":"Android 14","browser":"Chrome","app_version":"1.0.4"}'
```

---

## 12. Get Device Info
**GET** /device
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/device \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 13. Register FCM Token
**POST** /device-tokens
**Auth:** ✅ Bearer Token Required
**Description:** Primary way to register FCM token for push notifications

### Body (JSON):
```json
{
    "fcm_token": "fcm_token_from_mobile_app",
    "device_type": "android",
    "app_version": "1.0.4"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/device-tokens \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"fcm_token":"fcm_token_here","device_type":"android","app_version":"1.0.4"}'
```

---

## 14. Get All Device Tokens
**GET** /device-tokens
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/device-tokens \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 15. Delete Device Token (Logout)
**DELETE** /device-tokens/{token}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/device-tokens/fcm_token_here \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 📝 POSTS
# ==========================================

## 16. Get All Posts (Public)
**GET** /posts
**Auth:** ❌ No Auth Required
**Query Params:** ?page=1&per_page=15

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/posts
```

---

## 17. Get Single Post (Public)
**GET** /posts/{post_id}
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/posts/1
```

---

## 18. Get Saved Posts
**GET** /posts/saved
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/posts/saved \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 19. Create Post
**POST** /posts
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "title": "My New Post",
    "content": "This is the content of my post",
    "category_id": 1,
    "group_id": null,
    "visibility": "public"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"title":"My New Post","content":"This is the content","category_id":1}'
```

---

## 20. Update Post
**PUT** /posts/{post_id}
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "title": "Updated Title",
    "content": "Updated content",
    "status": "published"
}
```

### Curl:
```bash
curl -X PUT http://localhost/community/api/v1/posts/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"title":"Updated Title","content":"Updated content","status":"published"}'
```

---

## 21. Delete Post
**DELETE** /posts/{post_id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/posts/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 22. Like Post
**POST** /posts/{post_id}/like
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts/1/like \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 23. Save Post
**POST** /posts/{post_id}/save
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts/1/save \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 24. Share Post
**POST** /posts/{post_id}/share
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "message": "Check this out!"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts/1/share \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"message":"Check this out!"}'
```

---

## 25. Mark Post as Solved
**POST** /posts/{post_id}/mark-solved
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts/1/mark-solved \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 💬 COMMENTS
# ==========================================

## 26. Get Post Comments (Public)
**GET** /posts/{post_id}/comments
**Auth:** ❌ No Auth Required
**Query Params:** ?page=1

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/posts/1/comments
```

---

## 27. Get Comment Replies (Public)
**GET** /comments/{comment_id}/replies
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/comments/1/replies
```

---

## 28. Create Comment
**POST** /posts/{post_id}/comments
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "body": "Great post! Thanks for sharing."
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/posts/1/comments \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"body":"Great post!"}'
```

---

## 29. Update Comment
**PUT** /comments/{comment_id}
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "body": "Updated comment text"
}
```

### Curl:
```bash
curl -X PUT http://localhost/community/api/v1/comments/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"body":"Updated comment"}'
```

---

## 30. Delete Comment
**DELETE** /comments/{comment_id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/comments/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 31. Like Comment
**POST** /comments/{comment_id}/like
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/comments/1/like \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 🔔 NOTIFICATIONS
# ==========================================

## 32. Get Notifications
**GET** /notifications
**Auth:** ✅ Bearer Token Required
**Query Params:** ?page=1

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/notifications \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 33. Get Unread Count
**GET** /notifications/unread-count
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/notifications/unread-count \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 34. Mark Single Notification as Read
**PUT** /notifications/{id}/read
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X PUT http://localhost/community/api/v1/notifications/1/read \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 35. Mark All Notifications as Read
**PUT** /notifications/read-all
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X PUT http://localhost/community/api/v1/notifications/read-all \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 36. Delete Notification
**DELETE** /notifications/{id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/notifications/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 👥 FOLLOW
# ==========================================

## 37. Get Follow Status
**GET** /users/{user_id}/follow/status
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/users/2/follow/status \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 38. Follow User
**POST** /users/{user_id}/follow
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/users/2/follow \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 39. Unfollow User
**DELETE** /users/{user_id}/follow
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/users/2/follow \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 40. Remove Follower (Remove someone who follows you)
**DELETE** /users/{follower_id}/follower/remove
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/users/3/follower/remove \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 👤 PROFILE & ACTIVITY
# ==========================================

## 41. Update Profile
**POST** /profile/update
**Auth:** ✅ Bearer Token Required
**Content-Type:** multipart/form-data (for file uploads)

### Body (Form Data):
```
name: "New Name"
username: "newname"
bio: "New bio"
location: "Mumbai"
country_id: 1
avatar: [file] (optional)
cover_image: [file] (optional)
remove_avatar: "0"
remove_cover: "0"
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/profile/update \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -F "name=New Name" \
  -F "username=newname" \
  -F "bio=New bio" \
  -F "location=Mumbai" \
  -F "country_id=1"
```

---

## 42. Get Profile Activities
**GET** /profile/activities
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/profile/activities \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 43. Get User Activity
**GET** /user/activity
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/user/activity \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 🚩 REPORTS
# ==========================================

## 44. Report Content
**POST** /reports
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "reportable_type": "App\\Models\\Post",
    "reportable_id": 1,
    "reason": "Spam",
    "description": "This post contains spam content"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/reports \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"reportable_type":"App\\Models\\Post","reportable_id":1,"reason":"Spam","description":"This is spam"}'
```

---

# ==========================================
# 👥 GROUPS
# ==========================================

## 45. Create Group
**POST** /groups
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "name": "My Group",
    "description": "A test group",
    "category_id": 1
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name":"My Group","description":"A test group","category_id":1}'
```

---

## 46. Update Group
**PUT** /groups/{group_id}
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "name": "Updated Group Name",
    "description": "Updated description"
}
```

### Curl:
```bash
curl -X PUT http://localhost/community/api/v1/groups/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Group","description":"Updated desc"}'
```

---

## 47. Delete Group
**DELETE** /groups/{group_id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/groups/1 \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 48. Join Group
**POST** /groups/{group_id}/join
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/join \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 49. Leave Group
**DELETE** /groups/{group_id}/leave
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/groups/1/leave \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 50. Invite Member
**POST** /groups/{group_id}/invite
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "user_id": 3
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/invite \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"user_id":3}'
```

---

## 51. Remove Member
**DELETE** /groups/{group_id}/members/{user_id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X DELETE http://localhost/community/api/v1/groups/1/members/3 \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 52. Get Group Members
**GET** /groups/{group_id}/members
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/groups/1/members \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 53. Get Group Requests
**GET** /groups/{group_id}/requests
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/groups/1/requests \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 54. Get Group Invitations
**GET** /groups/{group_id}/invitations
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/groups/1/invitations \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 55. Create Group Post
**POST** /groups/{group_id}/posts
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "title": "Group Announcement",
    "content": "Welcome to the group!"
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/posts \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"title":"Group Announcement","content":"Welcome!"}'
```

---

## 56. Accept Join Request
**POST** /groups/{group_id}/requests/{user_id}/accept
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/requests/3/accept \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 57. Reject Join Request
**POST** /groups/{group_id}/requests/{user_id}/reject
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/requests/3/reject \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 58. Accept Invitation
**POST** /groups/{group_id}/invitations/accept
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "invitation_id": 1
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/invitations/accept \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"invitation_id":1}'
```

---

## 59. Reject Invitation
**POST** /groups/{group_id}/invitations/reject
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "invitation_id": 1
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/groups/1/invitations/reject \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"invitation_id":1}'
```

---

# ==========================================
# 📅 EVENTS
# ==========================================

## 60. Get Active Event
**GET** /active-event
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/active-event
```

---

## 61. Get Event Details (Public)
**GET** /events/{slug}
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/events/my-event-slug
```

---

## 62. Get Event Leaderboard (Public)
**GET** /events/{slug}/leaderboard
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/events/my-event-slug/leaderboard
```

---

## 63. Get My Event Rank
**GET** /events/{slug}/my-rank
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/events/my-event-slug/my-rank \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 🏆 REFERRAL
# ==========================================

## 64. Get My Referral
**GET** /my-referral
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/my-referral \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 🎓 ONLINE TESTS
# ==========================================

## 65. Get All Tests (Public)
**GET** /tests
**Auth:** ❌ No Auth Required
**Query Params:** ?page=1

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tests
```

---

## 66. Get Single Test (Public)
**GET** /tests/{test_id}
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tests/1
```

---

## 67. Get My Attempts
**GET** /tests/my-attempts
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tests/my-attempts \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 68. Start Test
**POST** /tests/{test_id}/start
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/tests/1/start \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 69. Take Test
**GET** /tests/{test_id}/attempts/{attempt_id}
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tests/1/attempts/1/take \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 70. Save Answer
**POST** /tests/{test_id}/attempts/{attempt_id}/answer
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "question_id": 1,
    "answer_id": 2
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/tests/1/attempts/1/answer \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"question_id":1,"answer_id":2}'
```

---

## 71. Clear Answer
**POST** /tests/{test_id}/attempts/{attempt_id}/clear-answer
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "question_id": 1
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/tests/1/attempts/1/clear-answer \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"question_id":1}'
```

---

## 72. Mark for Review
**POST** /tests/{test_id}/attempts/{attempt_id}/mark-review
**Auth:** ✅ Bearer Token Required

### Body (JSON):
```json
{
    "question_id": 1
}
```

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/tests/1/attempts/1/mark-review \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"question_id":1}'
```

---

## 73. Submit Test
**POST** /tests/{test_id}/attempts/{attempt_id}/submit
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X POST http://localhost/community/api/v1/tests/1/attempts/1/submit \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

## 74. Get Test Result
**GET** /tests/{test_id}/attempts/{attempt_id}/result
**Auth:** ✅ Bearer Token Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tests/1/attempts/1/result \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

---

# ==========================================
# 🌍 PUBLIC (No Auth)
# ==========================================

## 75. Get Countries
**GET** /countries
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/countries
```

---

## 76. Get Categories
**GET** /categories
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/categories
```

---

## 77. Get Tags
**GET** /tags
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/tags
```

---

## 78. Get Banners
**GET** /banners
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/banners
```

---

## 79. Get Guidelines
**GET** /guidelines
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/guidelines
```

---

## 80. Get Leaderboard
**GET** /leaderboard
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/leaderboard
```

---

# ==========================================
# 👤 PUBLIC USER PROFILES
# ==========================================

## 81. Get User Profile (Public)
**GET** /users/{id_or_username}
**Auth:** ❌ No Auth Required

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/users/rahul
```

---

## 82. Get User Posts (Public)
**GET** /users/{id_or_username}/posts
**Auth:** ❌ No Auth Required
**Query Params:** ?page=1

### Curl:
```bash
curl -X GET http://localhost/community/api/v1/users/rahul/posts
```

---

# ==========================================
# 📊 TOTAL COUNT: 82 API ENDPOINTS
# ==========================================
