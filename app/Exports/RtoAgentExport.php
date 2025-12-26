<?php

namespace App\Exports;

use App\Models\RtoAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RtoAgentExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = RtoAgent::with('cities');

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $agents = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=rto_agents.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Number', 'Cities', 'Status', 'Created At'];

        $callback = function() use ($agents, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($agents as $agent) {
                $cities = $agent->cities->pluck('city_name')->implode(', ');
                $row = [
                    $agent->id,
                    $agent->name,
                    $agent->number,
                    $cities,
                    $agent->is_active ? 'Active' : 'Blocked',
                    $agent->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
