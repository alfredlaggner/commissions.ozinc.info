<?php

	namespace App\Http\Controllers;

	use Illuminate\Support\Facades\DB;
	use Illuminate\Http\Request;
	use App\Earning;
	use App\Commission;
	use App\SaleInvoice;
	use App\SalesPerson;
	use App\Customer;
	use App\Month;
	use Carbon\Carbon;
	use App\Traits\CommissionTrait;

	class CommissionController extends Controller
	{
		use CommissionTrait;

		public function index(Request $request)
		{

			if ($request->session()->exists('data')) {
				$data = $request->session()->get('data');

			} else {
				$data = [
					'month' => $request->get('month'),
					'salesperson_id' => $request->get('salesperson_id')];
			}

			$salesperson = Salesperson::all();
			$months = Month::all();

			return view('home', ['data' => $data, 'months' => $months, 'currentMonth' => Carbon::now()->month, 'salesperson' => $salesperson]);
		}

		public function calcCommissions(Request $request)
		{
			$data = [
				'month' => $request->get('month'),
				'salesperson_id' => $request->get('salesperson_id'),
				'commission_version' => 2
			];
			session(['data' => $data]);

			$salesperson_id = $data['salesperson_id'];
			$customerItems = $this->commissionsPerCustomer($data);
			$brandItems = $this->commissionsPerBrand($data);
			$monthItems = $this->commissionsPerMonth($data);

			$salesorderItems = $this->salesOrdersPerSalesPerson($data);

			$lineItems = $this->salesOrdersLinesPerSalesOrder($data);
			if (
				(!$customerItems[0]['customers']->count() and !$customerItems[1]['customers']->count())
				or (!$brandItems[0]['brands']->count() and !$brandItems[1]['brands']->count())
				or !$monthItems->count()
				or !$salesorderItems
				or !$lineItems
			) {
				return view('nodata');
			}
			$items = $lineItems[0]['items'];
			$so_items = $salesorderItems[0]['so_items'];
			return view('commissions', compact('lineItems', 'items', 'salesorderItems', 'so_items', 'brandItems', 'monthItems', 'customerItems', 'data'));
		}

		public function salesOrdersPerSalesPerson($data)
		{
			$commission_version = $data['commission_version'];
			$returnValues = [];
			$month = $data['month'];
			$salesperson_id = $data['salesperson_id'];

			for ($i = 0; $i < 3; $i++) {

				if ($month <= 0) {
					$month = 12;
				}
				$so_items = SaleInvoice::
				select(\DB::raw('customer_id,invoice_number,sales_person_id, order_date, count(order_id) as salesorders,	sum(commission) as order_commission, sum(amt_invoiced) as order_total,
			avg(margin) as margin_average, customers.name as customer_name
			'))
					->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
					->where('sales_person_id', '=', $salesperson_id)
					->whereRaw('MONTH(saleinvoices.created_at) = ?', ($month))
					->orderby('order_commission', 'desc')
					->groupBy('invoice_number')
					->get();
				$average_margin_sum = 0;
				$salesorder_count = 0;
				$salesperson_name = '';

				foreach ($so_items as $so_item) {

					$average_margin_sum += $so_item->margin_average;
					$salesorder_count += 1;

					$salesperson_name = $so_item->salesperson->name;
				}
				$av_margin = 0;
				if ($salesorder_count) {
					$av_margin = $average_margin_sum / $salesorder_count;
				}
				$data = ['so_items' => $so_items, 'salesorder_count' => $salesorder_count, 'margin_average' => $av_margin, 'salesperson_name' => $salesperson_name];
				$data['dMonth'] = date("F", mktime(0, 0, 0, $month, 1));
				$data['margin_average'] = number_format($data['margin_average'], 2) . " %";
				array_push($returnValues, $data);
				$month = $month - 1;
			}
			//			dd($returnValues);
			return $returnValues;
		}


		public function salesOrdersLinesPerSalesOrder($data)
		{
			$commission_version = $data['commission_version'];
			$returnValues = [];
			$month = $data['month'];
			$salesperson_id = $data['salesperson_id'];
			for ($i = 0; $i < 3; $i++) {
				$month = $month - $i;
				if ($month <= 0) {
					$month = 12;
				}
				$items = SaleInvoice::
				when($salesperson_id, function ($query, $salesperson_id) {
					return $query->where('sales_person_id', '=', $salesperson_id);
				})
					->whereRaw('MONTH(saleinvoices.created_at) =?', ($month))
					->orderby('comm_percent', 'desc')
					->get();
				if ($items->toArray()) {
					$total_commission = 0;
					$total_sales = 0;
					foreach ($items as $item) {
						$commission_percent = $this->getCommission(round($item->margin, 0, PHP_ROUND_HALF_DOWN), $item->salesperson->region, $commission_version);
						$commission = ($item->amt_invoiced) * $commission_percent;
						$total_commission += $commission;
						$total_sales += $item->amt_invoiced;
						$development = true;
						if ($development) {
							$si = SaleInvoice::find($item->id);
							$si->commission = $commission;
							$si->comm_percent = $commission_percent;
							$si->save();
						};
					}
					$data = ['month' => $month, 'items' => $items, 'commission_percent' => $commission_percent, 'commission' => $commission, 'total_commission' => $total_commission, 'total_sales' => $total_sales];
					$data['total_commission'] = '$' . number_format($data['total_commission'], 2, '.', ',');
					$data['total_sales'] = '$' . number_format($data['total_sales'], 2, '.', ',');
					$data['items_count'] = $data['items']->count();
					array_push($returnValues, $data);
				} else {
					$data = ['month' => $month, 'items' => [], 'commission_percent' => 0, 'commission' => 0, 'total_commission' => 0, 'total_sales' => 0];
					$data['total_commission'] = '0';
					$data['items_count'] = 0;
					array_push($returnValues, $data);
				}
			}
			//	dd($returnValues);
			return $returnValues;
		}

		function commissionsPerCustomer($data)
		{
			$commission_version = 2;
			$commission_version = $data['commission_version'];
			$month = $data['month'];
			$returnValues = [];
			$salesperson_id = $data['salesperson_id'];
			for ($i = 0; $i < 2; $i++) {
				$month = $month - $i;
				if ($month <= 0) {
					$month = 12;
				}
				$dMonth = date("F", mktime(0, 0, 0, $month, 1));

				$customerItems = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
					count(distinct(invoice_number)) as customer_count,
					sum(commission) as customer_commission,
					sum(amt_invoiced) as customer_volume,
					avg(margin) as customer_margin
					'))
					->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
					->where('sales_person_id', '=', $salesperson_id)
					->whereRaw('MONTH(saleinvoices.created_at) = ?', ($month))
					->orderBy('customer_commission', 'desc')
					->groupBy('customer_id')
					->get();


				$chartItems = SaleInvoice::select(DB::raw('
					customers.name as "0",
					sum(commission) as "1",
					avg(margin) as "2",
					count(distinct(invoice_number)) "3"
					'))
					->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
					->where('sales_person_id', '=', $salesperson_id)
					->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($month))
					->orderBy("1", 'desc')
					->groupBy('customer_id')
					->get()->toArray();

				$customers = \Lava::DataTable();
				$title = "Sales for " . $salesperson_id;
				$customers->addStringColumn('Customer');
				$customers->addnumberColumn('Commission $');
				$customers->addnumberColumn('Avg. Margin %');
				$customers->addnumberColumn('Sales #');

				if ($chartItems) {
					$customers->addRows($chartItems);
				}
				\Lava::BarChart('Customer' . $i, $customers, [
					'title' => 'Sales per Customer in ' . $dMonth,
					'height' => 600,
					'width' => 450,
					'isStacked' => true,
					'is3D' => false,
					'bar' => ['groupWidth' => "50%"],
					'vAxis' => ['textPosition' => 'none']
				]);
				$data = ['month' => $month, 'dMonth' => $dMonth, 'customers' => $customerItems];
				array_push($returnValues, $data);
			}
			return ($returnValues);
		}

		function commissionsPerBrand($data)
		{
			$commission_version = 2;
			$salesperson_id = $data['salesperson_id'];
			$month = $data['month'];

			$title = "Sales for " . $salesperson_id;
			$month = $data['month'];
			$returnValues = [];
			$salesperson_id = $data['salesperson_id'];
			for ($i = 0; $i < 2; $i++) {
				$month = $month - $i;
				if ($month <= 0) {
					$month = 12;
				}
				$dMonth = date("F", mktime(0, 0, 0, $month, 1));
				$brandItems = SaleInvoice::select(DB::raw('brands.name as brand_name,
					avg(margin) as brand_margin,
					sum(commission) as brand_commission,
					count(brand_id) as brand_count,
					sum(amt_invoiced) as brand_volume
					'))
					->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
					->where('brands.is_active', '=', true)
					->where('sales_person_id', '=', $salesperson_id)
					->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($month))
					->orderBy('brand_commission', 'desc')
					->groupBy('brand_id')
					->get();

				$chartItems = SaleInvoice::select(DB::raw('brands.name as "0",
					sum(commission) as "1",
					avg(margin) as "2",
					count(brand_id) "3"
					'))
					->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
					->where('brands.is_active', '=', true)
					->where('sales_person_id', '=', $salesperson_id)
					->where('sales_person_id', '=', $salesperson_id)
					->whereRaw(
						'MONTH(saleinvoices.created_at) = ? ', ($month))
					->orderBy("1", 'desc')
					->groupBy('brand_id')
					->get()->toArray();

				$brands = \Lava::DataTable();
				$brands->addStringColumn('Brand');
				$brands->addnumberColumn(' Commission $');
				$brands->addnumberColumn(' Avg. Margin %');
				$brands->addnumberColumn(' Sales #');
				if (count($chartItems)) {
					$brands->addRows($chartItems);
				}
				\Lava::BarChart('Brand' . $i, $brands, [
					'title' => 'Sales per Brand in ' . $dMonth,
					'height' => 900,
					'width' => 450,
					'isStacked' => true,
					'bar' => ['groupWidth' => "50%"],
					'vAxis' => ['textPosition' => 'none']
				]);

				$data = ['month' => $month, 'dMonth' => $dMonth, 'brands' => $brandItems];
				array_push($returnValues, $data);

			}
			return ($returnValues);
		}

		public
		function commissionsPerMonth($data)
		{
			$months = \Lava::DataTable();
			$title = "Sales per month";
			$month = $data['month'];

			$salesperson_id = $data['salesperson_id'];
			$monthItems = SaleInvoice::select(DB::raw('sales_person_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        avg(margin) as month_margin,
        count(distinct(invoice_number)) as month_sold,
        MONTH(saleinvoices.created_at) as month, 
        YEAR(created_at) as year'))
				->has('salesperson')
				->where('sales_person_id', '=', $salesperson_id)
				->orderBy('created_at', 'desc')
				->orderBy('month_commission')
				->groupBy('month')
				->groupBy('sales_person_id')
				->get();

			$monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            avg(margin) as "2",
            count(distinct(invoice_number)) as "3"
        '))
				->has('salesperson')
				->where('sales_person_id', '=', $salesperson_id)
				->orderBy('created_at', 'desc')
				->orderBy("1")
				->groupBy("0")
				->get()->toArray();

			$months->addStringColumn('Month');
			$months->addnumberColumn(' Commission $');
			$months->addnumberColumn(' Avg. Margin %');
			$months->addnumberColumn(' Sales #');
			if (count($monthChartItems)) {
				$months->addRows($monthChartItems);
			}
			\Lava::ColumnChart('Months', $months, [
				'title' => 'Sales per Month',
				'height' => 600,
				'width' => 780,
				'bar' => ['groupWidth' => "50%"]
			]);
			return $monthItems;
		}

		function commissionsPerCustomerBrand($customer_id, $customer_name, $salesperson_id, $month)
		{
			$commission_version = 2;

			$customerBrandItems = SaleInvoice::select(DB::raw('brands.name as "0",sum(commission) as "1"'))
				->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
				->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
				->where('sales_person_id', '=', $salesperson_id)
				->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($month))
				->where('customer_id', '=', $customer_id)
				->orderBy('customer_id')
				->orderBy('brand_id')
				->groupBy('brand_id')
				->get()->toArray();

			$customerBrands = \Lava::DataTable();
			$title = "Sales for " . $month;
			$customerBrands->addStringColumn('Customer');
			$customerBrands->addnumberColumn('Brands');
			if ($customerBrandItems) {
				$customerBrands->addRows($customerBrandItems);
			}
			$dmonth = date("F", mktime(0, 0, 0, $month, 1));

			\Lava::DonutChart('CustomerBrand', $customerBrands, [
				'title' => 'Brands for ' . $dmonth,
			]);

			$prevMonth = $month - 1;
			if ($prevMonth <= 0)
				$prevMonth = 12;

			$customerBrandItems2 = SaleInvoice::select(DB::raw('brands.name as "0",sum(commission) as "1"'))
				->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
				->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
				->where('sales_person_id', '=', $salesperson_id)
				->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($prevMonth))
				->where('customer_id', '=', $customer_id)
				->orderBy('customer_id')
				->orderBy('brand_id')
				->groupBy('brand_id')
				->get()->toArray();
			$customerBrands2 = \Lava::DataTable();
			$title = "Sales for " . $prevMonth;

			$customerBrands2->addStringColumn('Customer');
			$customerBrands2->addnumberColumn('Brands');
			if (empty($customerBrandItems2)) {
				$customerBrands2->addRows([['Not found'], [0]]);
			} else {
				$customerBrands2->addRows($customerBrandItems2);
			}
			$dprevMonth = date("F", mktime(0, 0, 0, $prevMonth, 1));
			\Lava::DonutChart('CustomerBrand2', $customerBrands2, [
				'title' => 'Brands for ' . $dprevMonth,
			]);
			return (view('customer_donut', compact('customer_name')));
		}


		public
		function geoChart()
		{

			$dispensaries = \Lava::DataTable();
			$order_date = 12;
			$title = "Sales for " . $order_date;

			//     $data = BccRetailer::select("city as 0", "business_name as 1")->limit(20)->get()->toArray();
			$salesorders = Customer::
			select(\DB::raw('name as "0", avg(amount_total) as "1", count(customer_id) as "2"'))
				->join('salesorders', 'salesorders.customer_id', '=', 'ext_id')
				->whereMonth('salesorders.order_date', $order_date)
				->groupby('salesorders.customer_id')
				->get()->toArray();
			$dispensaries->addStringColumn('Dispensary')
				->addnumberColumn(' Avg Sold')
				->addnumberColumn('# Sales')
				->addRows($salesorders);
			\Lava::GeoChart('Dispensary', $dispensaries, ['displayMode' => 'markers', 'region' => 'US-CA',
				'resolution' => "metros",
				'colorAxis' => ['colors' => ['red', 'green']],
				//         'sizeAxis' => ['minValue' => 0, 'maxValue'=> 20000]
			]);


			return view('charts.geocharts.sales', compact('title'));
		}

		public
		function testchart()
		{
			$finances = \Lava::DataTable();; // See note below for Laravel

			$finances->addDateColumn('Year')
				->addNumberColumn('Genre')
				->addNumberColumn('Fantasy & Sci Fi')
				->addNumberColumn('Romance')
				->addNumberColumn('General')
				->addNumberColumn('Western')
				->addNumberColumn('Literature')
				->addNumberColumn('Mystery?Crime')
				->setDateTimeFormat('Y')
				->addRow(['2010', 10, 24, 20, 32, 18, 5])
				->addRow(['2020', 16, 22, 23, 30, 16, 9])
				->addRow(['2030', 28, 19, 29, 30, 12, 13]);

			\Lava::ColumnChart('Finances', $finances, [
				'title' => 'Company Performance',
				'titleTextStyle' => [
					'color' => '#eb6b2c',
					'fontSize' => 14,
				],
				'isStacked' => true
			]);
			return $finances;
		}
	}
