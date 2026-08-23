<?php

namespace App\Http\Resources;

class StatResult
{
    public function __construct(
        public string $title,
        public string $chartType,
        public string $description,
        public array $labels,
        public array $datasets,
        public array $meta = [],
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'chartType' => $this->chartType,
            'description' => $this->description,
            'labels' => $this->labels,
            'datasets' => $this->datasets,
            'meta' => $this->meta,
        ];
    }
}
