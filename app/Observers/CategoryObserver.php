<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    protected function flush(): void
    {
        Cache::forget('navbar_categories_v1');
    }

    public function created(Category $c) { $this->flush(); }
    public function updated(Category $c) { $this->flush(); }
    public function deleted(Category $c) { $this->flush(); }
    public function restored(Category $c){ $this->flush(); }
}
