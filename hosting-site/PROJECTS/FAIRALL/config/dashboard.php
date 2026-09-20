<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Defer dashboard charts
    |--------------------------------------------------------------------------
    |
    | When true, the initial dashboard response loads KPI cards and tables only.
    | Chart data is fetched asynchronously after the page renders.
    |
    */
    'defer_charts' => (bool) env('DASHBOARD_DEFER_CHARTS', false),

    'cache_minutes' => (int) env('DASHBOARD_CACHE_MINUTES', 5),

];
