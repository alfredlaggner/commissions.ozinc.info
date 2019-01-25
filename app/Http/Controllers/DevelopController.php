<?php

	namespace App\Http\Controllers;

	use App\SalesOrder;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Http\Request;
	use App\Commission;
	use App\SaleInvoice;
	use App\SalesPerson;
	use Carbon\Carbon;
	use App\Earning;
	use App\Earning2;
	use App\Traits\CommissionTrait;

	class DevelopController extends Controller
	{
		use CommissionTrait;

		public function calcCommissions(Request $request)
		{
			$commission_version = 2;
			SaleInvoice::where('sales_person_id', '>', 0)->where('invoice_status', '=', 'invoiced')->chunk(100, function ($items) {
				$commission_version = 2;
				foreach ($items as $item) {
					$commission_percent = $this->getCommission(round($item->margin, 0, PHP_ROUND_HALF_DOWN), $item->salesperson->region, $commission_version);
					//		$commission = $item->quantity * $item->unit_price * $commission_percent;

					$commission = ($item->amt_invoiced) * $commission_percent;

					$si = SaleInvoice::find($item->id);
					$si->commission = $commission;
					$si->comm_version = $commission_version;
					$si->comm_region = $item->salesperson->region;
					$si->comm_percent = $commission_percent;
					$si->save();
				}
			});
			dd('done with ');

		}

		public function calcRegions(Request $request)
		{
			$region = $request->get('region');
			if ($region == 'N') {
				;
				$data = $this->calcNorth();
				return view('monthly_north', $data);


			} elseif ($region == 'S') {
				$data = $this->calcSouth();
				return view('monthly_south', $data);

			} else {
				$data = $this->CalcAll();
				//	dd($data);
				return view('monthly', $data);

			}
			return "xxx";
		}

		public function calcAll()
		{
			$totalSales = 0;
			$totalCommission = 0;
			$totalAvgMargin = 0;
			$totalSO = 0;
			$title = "Sales per month";
			$monthItems = SaleInvoice::select(DB::raw('order_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(created_at) as year'))
				->orderBy('created_at', 'desc')
				->groupBy('month')
				->get();
			foreach ($monthItems as $item) {
				$totalSales += $item->month_sale;
				$totalCommission += $item->month_commission;
				$totalAvgMargin += $item->month_margin;
				$totalSO += $item->so_count;
			}
			$AvMarginTotal = $totalAvgMargin / $monthItems->count();
			$AllTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            (avg(margin) * 100) as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->orderBy('created_at', 'asc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			$months = \Lava::DataTable();
			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Salesorders');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ComboChart('Months', $months, [
				'title' => 'Sales per Month',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"],
				'seriesType' => 'bars',
				'series' => [1 => ['type' => 'line']]
			]);

// northern California
			$totalSales = 0;
			$totalCommission = 0;
			$totalAvgMargin = 0;
			$totalSO = 0;

			$monthItemsNorth = SaleInvoice::select(DB::raw('order_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(saleinvoices.created_at) as year'))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'N')
				->orderBy('saleinvoices.created_at', 'desc')
				->groupBy('month')
				->get();
			foreach ($monthItemsNorth as $item) {
				$totalSales += $item->month_sale;
				$totalCommission += $item->month_commission;
				$totalAvgMargin += $item->month_margin;
				$totalSO += $item->so_count;

			}
			$AvMarginTotal = $totalAvgMargin / $monthItemsNorth->count();
			$NorthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];


			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            avg(margin) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'N')
				->orderBy('saleinvoices.created_at', 'asc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			$months = \Lava::DataTable();
			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Salesorders');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ComboChart('MonthsNorth', $months, [
				'title' => 'Sales per Month Northern Region',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"],
				'seriesType' => 'bars',
				'series' => [1 => ['type' => 'line']]
			]);


// southern California
			$totalSales = 0;
			$totalCommission = 0;
			$totalAvgMargin = 0;


			$monthItemsSouth = SaleInvoice::select(DB::raw('order_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(saleinvoices.created_at) as year'))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'S')
				->orderBy('saleinvoices.created_at', 'desc')
				->groupBy('month')
				->get();
			foreach ($monthItemsSouth as $item) {
				$totalSales += $item->month_sale;
				$totalCommission += $item->month_commission;
				$totalAvgMargin += $item->month_margin;
				$totalSO += $item->so_count;

			}
			$AvMarginTotal = $totalAvgMargin / $monthItemsSouth->count();
			$SouthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];


//dd($monthItemsSouth->toArray());

			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            avg(margin) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'S')
				->orderBy('saleinvoices.created_at', 'asc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			$months = \Lava::DataTable();
			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Salesorders');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ComboChart('MonthsSouth', $months, [
				'title' => 'Sales per Month Southern Region',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"],
				'seriesType' => 'bars',
				'series' => [1 => ['type' => 'line']]

			]);
			return ['monthItems' => $monthItems, 'AllTotals' => $AllTotals,
				'monthItemsNorth' => $monthItemsNorth, 'NorthernTotals' => $NorthernTotals,
				'monthItemsSouth' => $monthItemsSouth, 'SouthernTotals' => $SouthernTotals
			];
		}


		public function calcNorth()
		{
			// northern California

			$totalSales = 0;
			$totalCommission = 0;
			$totalAvgMargin = 0;
			$totalSO = 0;

			$monthItemsNorth = SaleInvoice::select(DB::raw('order_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(saleinvoices.created_at) as year'))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'N')
				->orderBy('saleinvoices.created_at', 'desc')
				->groupBy('month')
				->get();

			foreach ($monthItemsNorth as $item) {
				$totalSales += $item->month_sale;
				$totalCommission += $item->month_commission;
				$totalAvgMargin += $item->month_margin;
				$totalSO += $item->so_count;

			}
			$AvMarginTotal = $totalAvgMargin / $monthItemsNorth->count();
			$NorthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];


			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            avg(margin) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'N')
				->orderBy('saleinvoices.created_at', 'asc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			$months = \Lava::DataTable();
			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Salesorders');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ComboChart('MonthsNorth', $months, [
				'title' => 'Sales per Month Northern Region',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"],
				'seriesType' => 'bars',
				'series' => [1 => ['type' => 'line']]
			]);

			return ['monthItemsNorth' => $monthItemsNorth, 'NorthernTotals' => $NorthernTotals];


		}

		public function calcSouth()
		{// southern California
			$totalSales = 0;
			$totalCommission = 0;
			$totalAvgMargin = 0;
			$totalSO = 0;

			$monthItemsSouth = SaleInvoice::select(DB::raw('order_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(saleinvoices.created_at) as year'))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'S')
				->orderBy('saleinvoices.created_at', 'desc')
				->groupBy('month')
				->get();


//dd($monthItemsSouth->toArray());

			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            avg(margin) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->join('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
				->where('sales_persons.region', '=', 'S')
				->orderBy('saleinvoices.created_at', 'asc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			foreach ($monthItemsSouth as $item) {
				$totalSales += $item->month_sale;
				$totalCommission += $item->month_commission;
				$totalAvgMargin += $item->month_margin;
				$totalSO += $item->so_count;

			}
			$AvMarginTotal = $totalAvgMargin / $monthItemsSouth->count();
			$SouthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

			$months = \Lava::DataTable();
			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Salesorders');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ComboChart('MonthsSouth', $months, [
				'title' => 'Sales per Month Southern Region',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"],
				'seriesType' => 'bars',
				'series' => [1 => ['type' => 'line']]
			]);

			return ['monthItemsSouth' => $monthItemsSouth, 'SouthernTotals' => $SouthernTotals];
		}

		function allCommissions()
		{
			$items = SaleInvoice::select(DB::raw('sales_person_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        MONTH(created_at) as month, 
        YEAR(created_at) as year'))
				->has('salesperson')
				->where('sales_person_id', '>', 0)
				->orderBy('created_at')
				->orderBy('month_commission')
				->groupBy('month')
				->groupBy('sales_person_id')
				->get();
			//	return $items;
			foreach ($items as $item) {
				Earning2::updateOrCreate(
					['sales_person_id' => $item->sales_person_id,
						'month' => $item->month,
						'year' => $item->year],
					['name' => $item->salesperson->name,
						'commission' => $item->month_commission,
						'sale' => $item->month_sale]
				);
			}

			dd("done");
		}

		function commissionsPerAccount()
		{

			$items = SaleInvoice::select(DB::raw('*,sum(commission) as month_commission,MONTH(created_at) as month, YEAR(created_at) as year'))
				->where('sales_person_id', '>', 0)
				->orderBy('created_at')
				->groupBy('month')
				->groupBy('sales_person_id')
				->get();

			foreach ($items as $item) {
				echo $item->sales_person_id . " - " . $item->month . " - " . $item->month_commission . '<br>';
				Earning2::updateOrCreate(
					['sales_person_id' => $item->sales_person_id,
						'commission' => $item->month_commission,
						'month' => $item->month,
						'year' => $item->year]
				);
			}
			dd("done");
		}

	}