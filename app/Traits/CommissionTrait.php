<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Commission;
trait CommissionTrait {

    public function getCommission($margin, $region, $version) {
        $query = Commission::select(\DB::raw('max(margin) as max_margin'))->where('region', '=', $region)
            ->get();
        foreach ($query as $q) {
            $max_margin = $q->max_margin;
        }
        if ($margin > $max_margin) {
            $margin = $max_margin;
        };
        $comms = Commission::
        where('margin', '=', $margin)->
        where('region', '=', $region)->
        where('version', '=', $version)->
        limit(1)->get();
        foreach ($comms as $comm) {
            return ($comm->commission);
        }

    }

}
