<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ValidateRequestNonce
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = (string) $request->header('X-Request-Nonce', '');
        $timestamp = (int) $request->header('X-Request-Timestamp', 0);
        $ttl = (int) config('face.nonce_ttl_seconds', 120);
        $userId = (string) (optional($request->user())->id ?? 'guest');

        if ($nonce === '' || $timestamp <= 0) {
            return response()->json([
                'message' => 'Header keamanan request nonce tidak lengkap.',
            ], 422);
        }

        $now = time();
        if (abs($now - $timestamp) > $ttl) {
            return response()->json([
                'message' => 'Nonce kedaluwarsa. Ulangi permintaan.',
            ], 422);
        }

        $secret = (string) config('face.request_signing_secret', '');
        $requireSignature = (bool) config('face.require_request_signature', false);
        $providedSignature = (string) $request->header('X-Request-Signature', '');

        if ($secret !== '') {
            $canonical = implode('|', [
                strtoupper((string) $request->method()),
                '/'.ltrim((string) $request->path(), '/'),
                $userId,
                (string) $timestamp,
                $nonce,
            ]);
            $expected = hash_hmac('sha256', $canonical, $secret);

            if ($providedSignature !== '' && !hash_equals($expected, $providedSignature)) {
                return response()->json([
                    'message' => 'Signature request tidak valid.',
                ], 401);
            }

            if ($requireSignature && $providedSignature === '') {
                return response()->json([
                    'message' => 'Header X-Request-Signature wajib diisi.',
                ], 401);
            }
        } elseif ($requireSignature) {
            return response()->json([
                'message' => 'Konfigurasi signature belum aktif di server.',
            ], 500);
        }

        $cacheKey = sprintf('face_nonce:%s:%s', $userId, $nonce);
        if (!Cache::add($cacheKey, 1, now()->addSeconds($ttl))) {
            return response()->json([
                'message' => 'Nonce sudah pernah dipakai (replay terdeteksi).',
            ], 409);
        }

        return $next($request);
    }
}
