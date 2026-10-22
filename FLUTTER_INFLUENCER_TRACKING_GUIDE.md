# REIAC Community — Flutter Influencer Campaign Tracking Integration Guide

This guide contains everything the Flutter developer needs to implement **Influencer Campaign Tracking (Clicks, Installs/Downloads, and Registrations)** in the **REIAC Mobile App** (`com.community.reiacuser`).

---

## 📌 1. How the Entire Flow Works (Architecture)

1. **Influencer Shareable Link**: 
   - Admin generates link: `https://your-domain.com/c/{CAMPAIGN_CODE}` (e.g. `INF2026`).
2. **User Clicks Link**:
   - Backend records the **Click**.
   - If App is **already installed** on the phone: Direct Deep Link (`reiac://campaign/{CODE}`) opens the App.
   - If App is **not installed**: User is redirected to Google Play Store with install referrer:
     `https://play.google.com/store/apps/details?id=com.community.reiacuser&referrer=utm_source%3D{CODE}%26utm_campaign%3D{CODE}`
3. **User Installs & Opens App (First Time)**:
   - App reads `utm_campaign` code via Google Play Install Referrer.
   - App calls **API 1: `/api/v1/influencer/track-install`** with the `code` and `device_id`.
   - Backend records **Install Count (+1)**.
4. **User Registers / Signs Up**:
   - App calls **API 2: `/api/v1/auth/register`** passing the same `device_id`.
   - Backend automatically matches `device_id` to the influencer campaign.
   - Backend records **Registration Count (+1)**.

---

## 🛠️ 2. Android Configuration (`AndroidManifest.xml`)

Open `android/app/src/main/AndroidManifest.xml` and add the following `<intent-filter>` inside the `<activity android:name=".MainActivity" ...>` tag:

```xml
<activity
    android:name=".MainActivity"
    android:exported="true"
    ...>

    <!-- Existing intent filters... -->

    <!-- 👇 REIAC INFLUENCER DEEP LINK 👇 -->
    <intent-filter>
        <action android:name="android.intent.action.VIEW" />
        <category android:name="android.intent.category.DEFAULT" />
        <category android:name="android.intent.category.BROWSABLE" />
        
        <!-- Scheme: reiac://campaign/{code} -->
        <data 
            android:scheme="reiac" 
            android:host="campaign" />
    </intent-filter>

</activity>
```

---

## 📦 3. Dependencies (`pubspec.yaml`)

Add these packages to your `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter

  # 1. To read Google Play Store referral code after app install
  android_play_install_referrer: ^0.3.1

  # 2. To handle deep link when app is already installed
  app_links: ^6.1.1

  # 3. To get unique hardware device ID
  device_info_plus: ^10.1.0

  # 4. To remember that install has already been tracked once
  shared_preferences: ^2.2.3

  # 5. For HTTP network requests
  http: ^1.2.0
```

Run:
```bash
flutter pub get
```

---

## 🌐 4. API Details

### API 1: Track Install API

* **When to Call**: 
  - Call on **Splash Screen / First App Open** (before user logs in or registers).
* **Method**: `POST`
* **URL**: `https://YOUR_DOMAIN/api/v1/influencer/track-install`
* **Headers**:
  ```http
  Content-Type: application/json
  Accept: application/json
  ```
* **Request Body (JSON)**:
  ```json
  {
      "code": "INF2026",
      "device_id": "9a8b7c6d5e4f3a2b",
      "platform": "android"
  }
  ```
* **Success Response (`200 OK`)**:
  ```json
  {
      "success": true,
      "message": "App install attributed successfully.",
      "data": {
          "campaign_code": "INF2026",
          "influencer_name": "CarryMinati",
          "device_id": "9a8b7c6d5e4f3a2b",
          "platform": "android",
          "is_first_install": true,
          "is_registered": false
      }
  }
  ```

---

### API 2: User Register API

* **When to Call**:
  - When user fills out the signup form and taps **Submit / Register**.
* **Method**: `POST`
* **URL**: `https://YOUR_DOMAIN/api/v1/auth/register`
* **Headers**:
  ```http
  Content-Type: application/json
  Accept: application/json
  ```
* **Request Body (JSON)**:
  ```json
  {
      "name": "Amit Sharma",
      "email": "amit@example.com",
      "password": "Password123!",
      "password_confirmation": "Password123!",
      "country_id": 1,
      "device_id": "9a8b7c6d5e4f3a2b"
  }
  ```
  > **Note**: Even if user leaves `campaign_code` or `referral_code` empty, the backend will automatically link the registration to the influencer campaign using `device_id`!

---

## 💻 5. Complete Flutter Code (Copy-Paste Ready)

Create a file `lib/services/campaign_tracker_service.dart`:

```dart
import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:android_play_install_referrer/android_play_install_referrer.dart';
import 'package:app_links/app_links.dart';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

class CampaignTrackerService {
  // Replace with your actual backend base URL
  static const String baseUrl = 'https://YOUR_DOMAIN_HERE.COM';

  static final AppLinks _appLinks = AppLinks();

  /// 1. Get unique hardware device ID
  static Future<String> getDeviceId() async {
    final deviceInfo = DeviceInfoPlugin();
    try {
      if (Platform.isAndroid) {
        final androidInfo = await deviceInfo.androidInfo;
        return androidInfo.id; // Unique Android hardware build id
      } else if (Platform.isIOS) {
        final iosInfo = await deviceInfo.iosInfo;
        return iosInfo.identifierForVendor ?? 'ios_unknown';
      }
    } catch (e) {
      debugPrint("Error fetching device ID: $e");
    }
    return 'unknown_device';
  }

  /// 2. Initialize tracking on Splash Screen
  static Future<void> initialize() async {
    final deviceId = await getDeviceId();

    // CASE A: Deep link listener (When app is already installed and opened from link)
    _listenDeepLinks(deviceId);

    // CASE B: Play Store Install Referrer (When app was just installed from Play Store)
    await _checkPlayStoreReferrer(deviceId);
  }

  /// Deep link listener for `reiac://campaign/{code}`
  static void _listenDeepLinks(String deviceId) {
    _appLinks.uriLinkStream.listen((Uri? uri) {
      if (uri != null && uri.scheme == 'reiac' && uri.host == 'campaign') {
        final code = uri.pathSegments.isNotEmpty ? uri.pathSegments.first : null;
        if (code != null && code.isNotEmpty) {
          debugPrint("Deep link detected with campaign: $code");
          sendTrackInstall(code: code, deviceId: deviceId);
        }
      }
    }, onError: (err) {
      debugPrint("Deep link stream error: $err");
    });
  }

  /// Check Google Play Install Referrer
  static Future<void> _checkPlayStoreReferrer(String deviceId) async {
    if (!Platform.isAndroid) return;

    final prefs = await SharedPreferences.getInstance();

    // Only send once per installation
    if (prefs.getBool('campaign_install_tracked') == true) {
      return;
    }

    try {
      final details = await AndroidPlayInstallReferrer.installReferrer;
      final referrer = details.installReferrer; // e.g. utm_source=INF2026&utm_campaign=INF2026

      debugPrint("Play Store Referrer String: $referrer");

      if (referrer != null && referrer.contains('utm_campaign=')) {
        final uri = Uri.parse('https://reiac.in?' + referrer);
        final campaignCode = uri.queryParameters['utm_campaign'] ?? uri.queryParameters['utm_source'];

        if (campaignCode != null && campaignCode.isNotEmpty) {
          final success = await sendTrackInstall(code: campaignCode, deviceId: deviceId);
          if (success) {
            await prefs.setBool('campaign_install_tracked', true);
            await prefs.setString('campaign_code', campaignCode);
          }
        }
      }
    } catch (e) {
      debugPrint("Error reading Google Play referrer: $e");
    }
  }

  /// API 1 Call: POST /api/v1/influencer/track-install
  static Future<bool> sendTrackInstall({
    required String code,
    required String deviceId,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/api/v1/influencer/track-install'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'code': code.toUpperCase().trim(),
          'device_id': deviceId,
          'platform': Platform.isAndroid ? 'android' : 'ios',
        }),
      );

      debugPrint("Track Install API Response [${response.statusCode}]: ${response.body}");
      return response.statusCode == 200;
    } catch (e) {
      debugPrint("Network error calling track-install: $e");
      return false;
    }
  }
}
```

---

## 🚀 6. Where to Hook This in Flutter

### Step A: On App Startup (Splash Screen)
In your `SplashScreen` or initial screen `initState()`:

```dart
@override
void initState() {
  super.initState();
  // Initialize Campaign Tracking
  CampaignTrackerService.initialize();
}
```

### Step B: On Signup / Registration Form
In your `register()` method:

```dart
Future<void> submitRegistration() async {
  final deviceId = await CampaignTrackerService.getDeviceId();

  final response = await http.post(
    Uri.parse('https://YOUR_DOMAIN/api/v1/auth/register'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'name': nameController.text.trim(),
      'email': emailController.text.trim(),
      'password': passwordController.text,
      'password_confirmation': confirmPasswordController.text,
      'country_id': selectedCountryId,
      'device_id': deviceId, // 👈 PASS THIS DEVICE ID
    }),
  );
  
  // Handle response...
}
```

---

## 🧪 7. How to Test (Developer Testing Steps)

### Test Case 1: Deep Link (App Already Installed)
Run this command from your terminal while phone is connected via ADB:
```bash
adb shell am start -a android.intent.action.VIEW -d "reiac://campaign/INF2026" com.community.reiacuser
```
*Expected*: App opens directly, log shows `Deep link detected with campaign: INF2026`, and `/api/v1/influencer/track-install` is called.

### Test Case 2: Play Store Install Referrer (Simulate New Install)
Run this ADB broadcast command to simulate Play Store passing referrer:
```bash
adb shell am broadcast -a com.android.vending.INSTALL_REFERRER -n com.community.reiacuser/.MainActivity --es "referrer" "utm_source=INF2026&utm_campaign=INF2026"
```
*Expected*: App reads the referrer and sends the install attribution to the server.

### Test Case 3: Registration Test
Register a new account from the app.
*Expected*: In Admin Panel (`/admin/influencer-campaigns/{id}`), **App Installs = 1** and **Attributed Registrations = 1**.
