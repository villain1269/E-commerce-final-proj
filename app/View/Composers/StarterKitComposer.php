<?php

namespace App\View\Composers;

use App\Services\StarterKitService;
use Illuminate\View\View;

class StarterKitComposer
{
    public function __construct(private readonly StarterKitService $kits)
    {
    }

    public function compose(View $view): void
    {
        $view->with('kitBusinessLabels', $this->kits->businessLabels());
    }
}
