<?php

	namespace App\Http\Controllers;

	use Illuminate\Http\Request;

	use App\BccRetailer;
	use App\Traits\CommissionTrait;

	class ChartsController extends Controller
	{
		use CommissionTrait;

		public function unUsedCustomers(Request $request)
		{

			//	$sales_person_id = $request->get('sales_person_id');
			$sales_person_id = 38;
			$bccCustomers = BccRetailer::
			select(\DB::raw('customers.name as "0",customers.zip as "1"'))
				->join('customers', 'customers.name', 'like', 'bcc_retailers.business_name')
				->join('saleinvoices', 'saleinvoices.customer_id', '=', 'customers.ext_id')
				->where('bcc_retailers.sales_person_id', $sales_person_id)
				->get()->toArray();
//		dd($bccCustomers);
			$dispensaries = \Lava::DataTable();
			$dispensaries->addStringColumn('Dispensary');
			$dispensaries->addnumberColumn('quantity');
			$dispensaries->addRows($bccCustomers);

//dd($dispensaries);
			\Lava::GeoChart('Dispensary', $dispensaries,
				['displayMode' => 'markers', 'region' => 'US-CA',
					'resolution' => "provinces",
					'colorAxis' => ['colors' => ['red', 'green']],
				]);
			return view('charts.bcc_customers');

		}
		public function geoChartBrands(Request $request)
		{
			$brand = '';
			$brand = $request->get('brand') ? $request->get('brand') : 0;
			$brand = strtoupper($brand);
			$month = $request->get('month') ? $request->get('month') : 0;
			$dispensaries = \Lava::DataTable();

			$saleslines = Customer::
			select(\DB::raw('customers.name as "0", sum(quantity) as "1", count(customer_id) as "2"'))
				->join('saleinvoices', 'saleinvoices.customer_id', '=', 'customers.ext_id')
				->when($brand, function ($query, $brand) {
					return $query->whereRaw("upper(saleinvoices.name) LIKE '%" . $brand . "%'");
				})
				->when($month, function ($query, $month) {
					return $query->whereMonth('saleinvoices.created_at', $month);
				})
				->groupby('saleinvoices.customer_id')
				->get()->toArray();
			dd($saleslines);
			//    dd($data);
			$dispensaries->addStringColumn('Dispensary')
				->addnumberColumn('Units Sold')
				->addnumberColumn('# Sales')
				->addRows($saleslines);

			\Lava::GeoChart('Dispensary', $dispensaries, ['displayMode' => 'markers', 'region' => 'US-CA',
				'resolution' => "provinces",
				'colorAxis' => ['colors' => ['red', 'green']],
			]);

			$title = [["Sales of: " . $brand],["Month: " . $month],["Dispensaries: " .  count($saleslines)]];
			return view('charts.geocharts.brands', ['title' => $title, 'months' => Month::all()]);

		}

	}
