<?php

namespace App\Observers;

use App\Models\CompanyInfo;
use Illuminate\Support\Facades\Cache;

class CompanyInfoObserver
{
    protected function flush(): void
    {
        Cache::forget('business_settings_v1');
    }

    public function created(CompanyInfo $m){ $this->flush(); }
    public function updated(CompanyInfo $m){ $this->flush(); }
    public function deleted(CompanyInfo $m){ $this->flush(); }
    public function restored(CompanyInfo $m){ $this->flush(); }
}
