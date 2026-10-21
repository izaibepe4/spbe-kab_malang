<?php

namespace App\View\Composers;

use App\Services\VisitorCounterService;
use Illuminate\View\View;

class VisitorCountComposer
{
    protected $visitorService;

    public function __construct(VisitorCounterService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('visitorCount', $this->visitorService->getFormattedVisitors());
    }
}
