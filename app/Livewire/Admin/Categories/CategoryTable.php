<?php

namespace App\Livewire\Admin\Categories;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Category;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class CategoryTable extends DataTableComponent
{
    protected $model = Category::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setSearchEnabled();
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Image", "image")->hideIf(true),
            ImageColumn::make("Image", "image")
                ->location(
                    fn($row) => asset($row->image)
                )
                ->attributes(fn($row) => [
                    'class' => 'rounded-full h-12 w-12 object-cover',
                    'alt' => $row->name,
                ]),


            Column::make("Name", "name")
                ->searchable()
                ->sortable(),
            Column::make("Slug", "slug")
                ->sortable(),

            Column::make("Status", "status")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => $this->renderStatus($value)
                )
                ->html(),

            Column::make("Created at", "created_at")
                ->sortable(),

            Column::make("Actions")
                ->label(
                    fn($row, Column $column)  => view('livewire.admin.categories.actions')->with(['cat' => $row])
                )
                ->html(),
        ];
    }

    public function renderStatus(string $status): string
    {
        $colors = [
            'active' => 'green',
            'disable' => 'red',
        ];

        $colors = $colors[$status] ?? 'gray';

        $label = ucfirst($status);

        return "<span class=\"px-2 py-1 rounded-lg text-xs font-medium text-{$colors}-400 border\">{$label}</span>";
    }
}
