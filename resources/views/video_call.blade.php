<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الاستشارة الإلكترونية - مكالمة فيديو</title>
    <style>
        body { margin: 0; padding: 0; font-family: Tahoma, sans-serif; }
        #root { width: 100vw; height: 100vh; }
    </style>
</head>
<body>

    <!-- حاوية عرض المكالمة -->
    <div id="root"></div>

    <!-- استدعاء مكتبة ZegoCloud UIKit للويب -->
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
    <script>
        window.onload = function () {
            // 1. ضع هنا الـ AppID الخاص بك ( كرقم صحيح )
            const appID = 318131853; 
            
            // 2. ضع هنا الـ ServerSecret الذي نسخته من الموقع (بين علامات تنصيص)
            const serverSecret = "8dda5485611e81c2fa899a0f863c7361";
            
            // 3. رقم الغرفة الفريد يعتمد على رقم الموعد لكي يدخل الطبيب والمريض لنفس الغرفة تماماً
            const roomID = "appointment_{{ $appointment_id }}";
            
            // 4. معرف واسم الطبيب الحالي في النظام
            const userID = "doctor_" + "{{ auth()->id() }}";
            const userName = "د. " + "{{ auth()->user()->name ?? 'الطبيب' }}";

            // توليد التوكين الخاص بالاتصال الآمن
            const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(appID, serverSecret, roomID, userID, userName);
            
            // إنشاء واجهة المكالمة وبدء التشغيل
            const zp = ZegoUIKitPrebuilt.create(kitToken);
            zp.joinRoom({
                container: document.querySelector('#root'),
                scenario: {
                    mode: ZegoUIKitPrebuilt.OneONoneCall, // مكالمة فردية (طبيب ومريض)
                },
                showScreenSharingButton: true, // السماح للطبيب بمشاركة شاشته (عرض صور أشعة مثلاً)
                showUserList: true,
            });
        };
    </script>
</body>
</html>