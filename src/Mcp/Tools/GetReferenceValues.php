<?php

namespace Uneca\Chimera\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Uneca\Chimera\Models\ReferenceValue;

#[Description('List available reference value indicator names. Reference values are precomputed comparison values (e.g. population counts, household counts) used to show diffs in scorecards/gauges or a reference/contrast line in indicator charts.

Reference values are optional — only include them if the user explicitly asks for a comparison or reference line. The sole metadata available is the indicator name, so rely on your understanding of the artefact\'s subject to select the correct one.

Usage: pass the name as referenceValueToInclude in any lastlyArea*() method on BreakoutQueryBuilder. The resulting column is called reference_value. Use it in your getData() — for scorecards/gauges compute diff = value - reference_value, for indicators add a separate Plotly trace.')]
class GetReferenceValues extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $indicators = ReferenceValue::query()
            ->selectRaw('indicator, COUNT(*) AS total_values, ARRAY_AGG(DISTINCT level ORDER BY level) AS levels')
            ->groupBy('indicator')
            ->orderBy('indicator')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->indicator,
                'total_values' => (int) $row->total_values,
                'levels' => $row->levels,
            ]);

        if ($indicators->isEmpty()) {
            return Response::text('No reference values found. The reference_values table is empty.');
        }

        return Response::structured([
            'indicators' => $indicators->toArray(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
