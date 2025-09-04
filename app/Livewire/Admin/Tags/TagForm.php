<?php

namespace App\Livewire\Admin\Tags;

use App\Models\Tag;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

class TagForm extends Component
{

    public $tagId = null;
    public $isView = false;

    public $name = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name,' . $this->tagId,
            'status' => 'required|in:active,disable',
        ];
    }

    public function submit()
    {
        $this->validate();

        $payload = [
            'name'   => trim($this->name),
            'status' => $this->status ?: 'active',
        ];

        if ($this->tagId) {
            // UPDATE
            $tag = Tag::find($this->tagId);
            if (!$tag) {
                $this->dispatch('toast', type: 'error', message: 'Tag not found.');
                return;
            }

            // Check if there are any real changes
            $hasChanges = false;
            foreach ($payload as $k => $v) {
                if ($tag->{$k} !== $v) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                $this->dispatch('toast', type: 'warning', message: 'Nothing to update.');
                $this->dispatch('tags:refresh');
                Flux::modal('tag-modal')->close();
                return;
            }

            // Persist
            $tag->fill($payload);
            $tag->save();

            $this->dispatch('toast', type: 'success', message: 'Tag updated successfully.');
        } else {
            // CREATE
            tag::create($payload);

            // Reset form for next entry
            $this->reset(['tagId', 'name']);
            $this->status = 'active';

            $this->dispatch('toast', type: 'success', message: 'Tag created successfully.');
        }

        $this->dispatch('tags:refresh');
        Flux::modal('tag-modal')->close();
    }

    #[On('open-tag-modal')]
    public function tagDetail($mode, $tag = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->tagId = $tag['id'];

            $this->name = $tag['name'];
            $this->status = $tag['status'];
        }
    }
    
    public function render()
    {
        return view('livewire.admin.tags.tag-form');
    }
}
