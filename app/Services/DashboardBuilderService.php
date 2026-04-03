<?php

namespace App\Services;

use Carbon\Carbon;

class DashboardBuilderService
{
    public function build(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $collection = collect($rows);
        $columns = array_keys($rows[0]);

        // =========================
        // DETECT COLUMN TYPES
        // =========================
        $numericColumns = [];
        $dateColumns = [];
        $categoryColumns = [];

        foreach ($columns as $col) {

            $sample = $collection->pluck($col)->filter()->first();

            if (is_numeric($sample)) {
                $numericColumns[] = $col;
            } elseif ($this->isDate($sample)) {
                $dateColumns[] = $col;
            } else {
                $categoryColumns[] = $col;
            }
        }

        $metric = $numericColumns[0] ?? null;
        $dateCol = $dateColumns[0] ?? null;
        $category = $categoryColumns[0] ?? null;

        // =========================
        // CARDS
        // =========================
        $cards = [];

        if ($metric) {
            $cards = [
                [
                    'title' => "Total $metric",
                    'value' => $collection->sum($metric)
                ],
                [
                    'title' => "Average $metric",
                    'value' => round($collection->avg($metric), 2)
                ],
                [
                    'title' => "Max $metric",
                    'value' => $collection->max($metric)
                ],
                [
                    'title' => "Min $metric",
                    'value' => $collection->min($metric)
                ]
            ];
        }

        // =========================
        // LINE CHART (DATE TREND)
        // =========================
        $lineChart = null;

        if ($dateCol && $metric) {

            $sorted = $collection->sortBy($dateCol);

            $lineChart = [
                'type' => 'line',
                'title' => "$metric over time",
                'labels' => $sorted->pluck($dateCol)->values(),
                'data' => $sorted->pluck($metric)->values()
            ];
        }

        // =========================
        // BAR CHART (CATEGORY)
        // =========================
        $barChart = null;

        if ($category && $metric) {

            $grouped = $collection->groupBy($category)
                ->map(fn($items) => $items->sum($metric));

            $barChart = [
                'type' => 'bar',
                'title' => "$metric by $category",
                'labels' => $grouped->keys()->values(),
                'data' => $grouped->values()
            ];
        }

        // =========================
        // PIE CHART
        // =========================
        $pieChart = null;

        if ($category && $metric) {

            $grouped = $collection->groupBy($category)
                ->map(fn($items) => $items->sum($metric));

            $pieChart = [
                'type' => 'pie',
                'title' => "$metric distribution",
                'labels' => $grouped->keys()->values(),
                'data' => $grouped->values()
            ];
        }

        // =========================
        // TABLE
        // =========================
        $table = [
            'columns' => $columns,
            'rows' => $rows
        ];

        return [
            'meta' => [
                'detected' => [
                    'metric' => $metric,
                    'date' => $dateCol,
                    'category' => $category
                ]
            ],
            'cards' => $cards,
            'charts' => array_values(array_filter([
                $lineChart,
                $barChart,
                $pieChart
            ])),
            'table' => $table
        ];
    }

    // =========================
    // DATE DETECTION HELPER
    // =========================
    private function isDate($value): bool
    {
        try {
            Carbon::parse($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}