<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use App\Services\MetaCatalogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class SyncProductToMeta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(private readonly Product $product)
    {
        $this->onQueue('integrations');
    }

    public function handle(MetaCatalogService $meta): void
    {
        $remoteId = $meta->sync($this->product);

        if ($remoteId) {
            $this->product->updateQuietly(['facebook_product_id' => $remoteId]);
        }
    }

    public function failed(Throwable $exception): void
    {
        logger()->error('Meta Catalog sync failed', [
            'product_id' => $this->product->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
