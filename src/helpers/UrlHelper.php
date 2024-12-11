<?php

namespace Algom\Academia1\helpers;

class UrlHelper {
    private static $baseUrl = '/academia1';

    public static function getBaseUrl(): string {
        return self::$baseUrl;
    }

    public static function to(string $path): string {
        return self::$baseUrl . '/' . ltrim($path, '/');
    }

    public static function redirect(string $path): void {
        // Quitar .php y public/ de la ruta
        $path = str_replace('.php', '', $path);
        $path = str_replace('public/', '', $path);
        
        header('Location: /academia1/' . $path);
        exit();
    }

    public static function getCurrentUrl(): string {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $path;
    }

    public static function isCurrentUrl(string $path): bool {
        return self::getCurrentUrl() === self::to($path);
    }

    public static function getSegments(): array {
        $path = trim(str_replace(self::$baseUrl, '', self::getCurrentUrl()), '/');
        return $path ? explode('/', $path) : [];
    }

    public static function getSegment(int $index): ?string {
        $segments = self::getSegments();
        return $segments[$index] ?? null;
    }
}
