<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class PwaAssetsTest extends TestCase
{
    public function test_manifest_is_installable_and_has_android_icons(): void
    {
        $manifest = json_decode((string) file_get_contents(__DIR__.'/../../public/manifest.webmanifest'), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertContains('512x512', array_column($manifest['icons'], 'sizes'));
        $this->assertFileExists(__DIR__.'/../../public/images/icons/icon-192.png');
        $this->assertFileExists(__DIR__.'/../../public/images/icons/icon-512.png');
    }

    public function test_service_worker_has_offline_fallback_and_avoids_sensitive_pages(): void
    {
        $worker = (string) file_get_contents(__DIR__.'/../../public/service-worker.js');

        $this->assertStringContainsString("caches.match('/offline')", $worker);
        $this->assertStringContainsString("url.pathname.startsWith('/admin')", $worker);
        $this->assertStringContainsString("['/checkout', '/bag']", $worker);
    }
}
