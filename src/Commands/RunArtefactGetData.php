<?php

namespace Uneca\Chimera\Commands;

use Illuminate\Console\Command;
use Uneca\Chimera\Models\Gauge;
use Uneca\Chimera\Models\Indicator;
use Uneca\Chimera\Models\MapIndicator;
use Uneca\Chimera\Models\Report;
use Uneca\Chimera\Models\Scorecard;
use Uneca\Chimera\Services\DashboardComponentFactory;

class RunArtefactGetData extends Command
{
    protected $signature = 'chimera:run-artefact-getdata
                            {type : Type of artefact (scorecard, gauge, indicator, map-indicator, report)}
                            {name : Full artefact name (e.g. "KenyaCensus/PopulationPyramid")}
                            {--filter-path= : Area filter path (defaults to national scope)}';

    protected $description = 'Execute getData() on an artefact in a fresh process and return JSON result';

    public function handle(): int
    {
        $type = $this->argument('type');
        $name = $this->argument('name');
        $filterPath = $this->option('filter-path') ?? '';

        $modelClass = match ($type) {
            'scorecard' => Scorecard::class,
            'gauge' => Gauge::class,
            'indicator' => Indicator::class,
            'map-indicator' => MapIndicator::class,
            'report' => Report::class,
            default => null,
        };

        if ($modelClass === null) {
            $this->error(json_encode(['success' => false, 'error' => "Invalid type '{$type}'"]));

            return self::FAILURE;
        }

        $model = $modelClass::withoutEagerLoads()->where('name', $name)->first();

        if ($model === null) {
            $this->error(json_encode(['success' => false, 'error' => "{$type} with name '{$name}' not found"]));

            return self::FAILURE;
        }

        $instance = match ($type) {
            'scorecard' => DashboardComponentFactory::makeScorecard($model),
            'gauge' => DashboardComponentFactory::makeGauge($model),
            'indicator' => DashboardComponentFactory::makeIndicator($model),
            'map-indicator' => DashboardComponentFactory::makeMapIndicator($model),
            'report' => DashboardComponentFactory::makeReport($model),
        };

        if ($instance === null) {
            $this->error(json_encode(['success' => false, 'error' => "Failed to instantiate {$type} '{$name}'"]));

            return self::FAILURE;
        }

        try {
            $data = $instance->getData($filterPath);
        } catch (\Throwable $e) {
            $this->error(json_encode(['success' => false, 'error' => 'getData() execution failed: '.$e->getMessage()]));

            return self::FAILURE;
        }

        if ($data === null || $data->isEmpty()) {
            $this->line(json_encode([
                'success' => true,
                'rows_returned' => 0,
                'columns' => [],
                'sample_data' => null,
            ]));

            return self::SUCCESS;
        }

        $firstRow = $data->first();
        $columns = array_keys((array) $firstRow);

        $this->line(json_encode([
            'success' => true,
            'rows_returned' => $data->count(),
            'columns' => $columns,
            'sample_data' => $firstRow,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
