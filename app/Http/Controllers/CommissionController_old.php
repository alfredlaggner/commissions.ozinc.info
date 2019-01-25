<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Earning;
use App\Commission;
use App\SaleInvoice;
use App\SalesPerson;
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
        $salesperson_id = $data['salesperson_id'];
        $commission_version = 2;
        'salesperson_id' =>  $request->get('salesperson_id')
        ];
        session(['data' => $data]);

        $month = $data['month'];
        $customerItems = $this->commissionsPerCustomer($data);
        $brandItems = $this->commissionsPerBrand($data);
        $monthItems = $this->commissionsPerMonth($data);
//dd($data['month']);
        $so_items = SaleInvoice::
        select(\DB::raw('invoice_number,order_date, count(order_id) as salesorders,	sum(commission) as order_commission, sum(amt_invoiced) as order_total,
			avg(margin) as avg_margin
			'))
            ->when($salesperson_id, function ($query, $salesperson) {
                return $query->where('sales_person_id', '=', $salesperson);
            })
            ->whereRaw('MONTH(saleinvoices.created_at) = ?', ($month))
            ->orderby('order_commission', 'desc')
            ->groupBy('invoice_number')
            ->get();

        $items = SaleInvoice::
        when($salesperson_id, function ($query, $salesperson_id) {
            return $query->where('sales_person_id', '=', $salesperson_id);
        })
            //    ->where('invoice_status','=', 'invoiced')
            ->whereRaw('MONTH(saleinvoices.created_at) =?', ($month))
            ->orderby('comm_percent', 'desc')
            ->get();

        $average_margin_sum = 0;
        $salesorder_count = 0;
        foreach ($so_items as $so_item) {
            if ($so_item->avg_margin > -100 and $so_item->avg_margin < 100) {
                $average_margin_sum += $so_item->avg_margin;
                $salesorder_count += 1;
            }
        }
        $av_margin = $average_margin_sum / $salesorder_count;
        // table 1
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


        $data['commission'] = $total_commission;
        $salesperson_name = '';
        if ($salesperson_id) {
            $sp = SalesPerson::where('sales_person_id', '=', $salesperson_id)->get();
            foreach ($sp as $s) {
                $salesperson_name = $s->name;
            }
        }
        $data['salesperson'] = $salesperson_name;

        $data['dMonth'] = date("F", mktime(0, 0, 0, $data['month'], 1));
        $data['margin_average'] = number_format($av_margin, 2) . " %";
        $data['commission'] = '$' . number_format($total_commission, 2);
        $data['sales'] = '$' . number_format($total_sales, 2);
        $data['items'] = $items->count();
        //		dd($data);
        if ($request->get('display') == 'display') {
            return view('commissions', compact('items', 'so_items', 'brandItems', 'monthItems', 'customerItems', 'data'));
        } else {
            return \Excel::download(new SalesLines($items), 'commissions.xlsx');
        }

        //  dd("Commission = " . $total_commission);
    }

    function commissionsPerCustomer($data)
    {
        $commission_version = 2;
        $salesperson_id = $data['salesperson_id'];
        $customers = \Lava::DataTable();
        $title = "Sales for " . $salesperson_id;
        $month = $data['month'];

        $customerItems = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
			sum(commission) as customer_commission,
			sum(amt_invoiced) as customer_volume,
			avg(margin) as avg_margin
			'))
            ->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
            ->where('sales_person_id', '=', $salesperson_id)
            ->whereRaw('MONTH(saleinvoices.created_at) = ?', ($month))
            ->orderBy('customer_commission', 'desc')
            ->groupBy('customer_id')
            ->get();
        $chartItems = SaleInvoice::select(DB::raw('customers.name as "0",sum(commission) as "1",sum(amt_invoiced) as "2"'))
            ->join('customers', 'customers.ext_id', '=', 'saleinvoices.customer_id')
            ->where('sales_person_id', '=', $salesperson_id)
            ->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($month))
            ->orderBy("1", 'desc')
            ->groupBy('customer_id')
            ->get()->toArray();
        $customers->addStringColumn('Customer')
            ->addnumberColumn(' Commission $')
            ->addnumberColumn(' Sales $')
            ->addRows($chartItems);
        \Lava::BarChart('Customer', $customers, [
            'title' => 'Sales per Customer',
            'height' => 600,
            'width' => 780,
            'isStacked' => true,
            'bar' => ['groupWidth' => "50%"]
        ]);
        return ($customerItems);
    }

    function commissionsPerCustomerBrand($customer_id, $salesperson_id, $month)
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

        $customerBrands->addStringColumn('Customer');
        $customerBrands->addnumberColumn('Brands');
        $customerBrands->addRows($customerBrandItems);
        \Lava::ColumnChart('CustomerBrand', $customerBrands);

/*        $prevMonth = $month - 1;
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
        $customerBrands2->addRows($customerBrandItems2);
        \Lava::DonutChart('CustomerBrand2', $customerBrands2);*/

        return (view('test_donut'));
    }

    function commissionsPerBrand($data)
    {
        $commission_version = 2;
        $salesperson_id = $data['salesperson_id'];
        $month = $data['month'];

        $brands = \Lava::DataTable();
        $title = "Sales for " . $salesperson_id;


        $brandItems = SaleInvoice::select(DB::raw('brands.name as brand_name,sum(commission) as brand_commission,sum(amt_invoiced) as brand_volume'))
            ->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
            ->where('sales_person_id', '=', $salesperson_id)
            ->whereRaw('MONTH(saleinvoices.created_at) = ? ', ($month))
            ->orderBy('brand_commission', 'desc')
            ->groupBy('brand_id')
            ->get();

        $chartItems = SaleInvoice::select(DB::raw('brands.name as "0",sum(commission) as "1",sum(amt_invoiced) as "2"'))
            ->join('brands', 'brands.ext_id', '=', 'saleinvoices.brand_id')
            ->where('sales_person_id', '=', $salesperson_id)
            ->whereRaw(
                'MONTH(saleinvoices.created_at) = ? ', ($month))
            ->orderBy("2", 'desc')
            ->groupBy('brand_id')
            ->get()->toArray();
        $brands->addStringColumn('Brand')
            ->addnumberColumn(' Commission $')
            ->addnumberColumn(' Sales $')
            ->addRows($chartItems);
        \Lava::BarChart('Brand', $brands, [
            'title' => 'Sales per Brand',
            'height' => 600,
            'width' => 780,
            'bar' => ['groupWidth' => "50%"]
        ]);

        return ($brandItems);
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
        MONTH(saleinvoices.created_at) as month, 
        YEAR(created_at) as year'))
            ->has('salesperson')
            ->where('sales_person_id', '=', $salesperson_id)
            ->where('sales_person_id', '>', 0)
            ->orderBy('created_at', 'desc')
            ->orderBy('month_commission')
            ->groupBy('month')
            ->groupBy('sales_person_id')
            ->get();

        $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(saleinvoices.created_at) as "0", 
            sum(commission) as "1",
            sum(amt_invoiced) as "2"
        '))
            ->has('salesperson')
            ->where('sales_person_id', '=', $salesperson_id)
            ->where('sales_person_id', '>', 0)
            ->orderBy('created_at', 'desc')
            ->orderBy("2")
            ->groupBy("0")
            ->get()->toArray();

        $months->
        addStringColumn('Month')
            ->addnumberColumn(' Commission $')
            ->addnumberColumn(' Sales $')
            ->addRows($monthChartItems);
        \Lava::BarChart('Months', $months, [
            'title' => 'Sales per Month',
            'height' => 600,
            'width' => 780,
            'bar' => ['groupWidth' => "50%"]
        ]);
        return $monthItems;
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
