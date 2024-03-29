<?php

namespace App\Livewire\Dashboard;

use App\Models\District;
use App\Models\Stream;
use App\Models\Transaction as Transact;
use GuzzleHttp\Psr7\Request;
use Livewire\Component;
use Carbon\Carbon;

class ReportGenerator extends Component
{
    public $transactions,$report_type, $display, $from_date, $to_date; 
    public $labels = [];
    public $series = [];
    public $categories = [];
    public function mount()
    { 
        $this->display = $_GET['display'];
        $this->report_type = $_GET['report_type'];
        $this->from_date = $_GET['from_date'];
        $this->to_date = $_GET['to_date'];
    }
    public function render()
    { 
        switch ($this->report_type) {


            case 'summary':
                if ($this->display == 'table') {
                    $this->summary_table_data();
                } elseif ($this->display == 'bar') {
                    $this->summary_bar_data();
                } elseif ($this->display == 'pie') {
                    $this->summary_pie_data();
                }else{
                    $this->summary_line_data();
                }
            break;


            case 'collection':
                if ($this->display == 'table') {
                    $this->collection_table_data();
                } elseif ($this->display == 'bar') {
                    $this->collection_bar_chart();
                } elseif ($this->display == 'pie') {
                    $this->collection_pie_data();
                }else{
                    $this->collection_line_data();
                }
            break;
            
            case 'transaction':
                if ($this->display == 'table') {
                    $this->transaction_table_data();
                } elseif ($this->display == 'bar') {
                    $this->transaction_bar_chart();
                } elseif ($this->display == 'pie') {
                    $this->transaction_pie_data();
                }else{
                    $this->transaction_line_data();
                }
            break;

            default:
                $this->transactions = Transact::get();
            break;
        }
        
        return view('livewire.dashboard.report-generator')->layout('layouts.app');
    }


    // --------------- Summary Data
    public function summary_table_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        $this->transactions = Stream::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])
        ->get();
    }
    public function summary_bar_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        $this->transactions = Stream::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])
        ->get();
        foreach ($this->transactions as $key => $data) {
            $this->series[] = $data->transacts->sum('total_amount');
            $this->categories[] = $data->name;
        }
    }
    public function summary_pie_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        $this->transactions = Stream::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])
        ->get();
        
    
        foreach ($this->transactions as $data) {
            $totalAmount = $data->transacts->sum('total_amount');
            $this->series[] = $totalAmount;
            $this->labels[] = $data->name;
        }
    }
    
    public function summary_line_data()
    {
        $this->transactions = Transact::get();
    }




    // ------------ Collections Data
    public function collection_table_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
        $this->transactions = District::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])->get();
    }

    public function collection_bar_chart(){
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        $this->transactions = District::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])
        ->get();
        foreach ($this->transactions as $key => $data) {
            $this->series[] = $data->transacts->sum('total_amount');
            $this->categories[] = $data->name;
        }
    }

    public function collection_pie_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        $this->transactions = District::with(['transacts' => function ($query) use ($fromDate, $toDate) {
            // Use the $query variable to apply the whereBetween condition
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }])
        ->get();
        
    
        foreach ($this->transactions as $data) {
            $totalAmount = $data->transacts->sum('total_amount');
            $this->series[] = $totalAmount;
            $this->labels[] = $data->name;
        }
    }



    // ------------- Transaction
    public function transaction_table_data()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        // Fetch transactions within the date range
        $this->transactions = Transact::whereBetween('created_at', [$fromDate, $toDate])->get();
    
    }

    public function transaction_bar_chart()
    {
        $fromDate = Carbon::parse($this->from_date)->startOfDay();
        $toDate = Carbon::parse($this->to_date)->endOfDay();
    
        // Fetch transactions within the date range and group by created_at date
        $groupedTransactions = Transact::whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($transaction) {
                return $transaction->created_at->format('F j, Y'); // Format as "Month day, Year"
            });
    
        // Extract series and categories arrays
        $this->series = $groupedTransactions->map(function ($group) {
            return $group->sum('total_amount');
        })->values()->toArray();
    
        $this->categories = $groupedTransactions->keys()->toArray();
    }
    
    
    
}