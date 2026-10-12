<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | that can be used to restrict which domains can access your API.
    |
    */

    /**
     * Paths that should be applicable to the CORS rules.
     * กำหนด Path ที่ต้องการให้ CORS มีผล (โดยปกติคือ API endpoints ทั้งหมด)
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /**
     * Allowed origins (domains) to access the resource.
     * กำหนด Origin (โดเมน) ที่อนุญาตให้เข้าถึง Server ได้
     * * 🔑 สำหรับการทดสอบ Android (สำคัญ): ถ้าคุณใช้แอปพลิเคชันมือถือ
     * และไม่ได้มี Origin ที่ชัดเจน (หรือต้องการให้ทุก Origin เข้าถึงได้)
     * ให้ใช้ ['*']
     */
    'allowed_origins' => ['*'], 
    
    /**
     * Allowed origins patterns. (ใช้ได้กับ Laravel 8+)
     * กำหนดรูปแบบ (Pattern) ของ Origin ที่อนุญาต
     */
    'allowed_origins_patterns' => [],

    /**
     * Allowed HTTP methods.
     * กำหนด HTTP Method ที่อนุญาต (GET, POST, PUT, DELETE, ฯลฯ)
     */
    'allowed_methods' => ['*'], // หรือระบุเฉพาะที่ใช้: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']

    /**
     * Allowed headers.
     * กำหนด Headers ที่อนุญาตให้ Client ส่งมาพร้อมกับคำขอ
     */
    'allowed_headers' => ['*'], // อนุญาตทุก Header

    /**
     * Exposed headers.
     * กำหนด Headers ที่อนุญาตให้ Client (Browser/App) อ่านค่าได้
     */
    'exposed_headers' => [],

    /**
     * Max age (in seconds) for the preflight request.
     * กำหนดระยะเวลา (วินาที) ที่ Preflight Request (OPTIONS) สามารถ Cache ได้
     */
    'max_age' => 0,

    /**
     * Supports credentials.
     * อนุญาตให้ส่ง Cookie, HTTP Authentication หรือ SSL Client Certificate
     * (ใช้ 'true' เมื่อ Client และ Server ต้องมีการแลกเปลี่ยนข้อมูลการรับรองตัวตน)
     */
    'supports_credentials' => false,

];