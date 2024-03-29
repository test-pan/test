<?php
namespace App\Helpers;

use Illuminate\Http\Response;
use Facades\App\Repositories\{SectorRepository,IndustryRepository};
use Illuminate\Support\Facades\{Auth, Log, DB, Validator, Crypt};

class AppHelper
{
    
    public function getSectors() 
    {
        // $sectors = SectorRepository::findBy(['status'=> 1],'get',  ['id', 'sector_name'])->pluck('sector_name', 'id');
        $sectors = SectorRepository::fetchSectors()->pluck('sector_name', 'id');

        $finalData = [];
        foreach ($sectors as $key => $value) {
            $id = Crypt::encrypt($key);
            $finalData[$id] = $value;
        }

        // dd($finalData);


        return $finalData;
    }


    public function getIndustries($sector) 
    {
        $sectorId =  Crypt::decrypt($sector);
        $where = [
            'status'=> 1,
            'sector_id'=> $sectorId,
        ];

        $industries = IndustryRepository::findBy($where,'get',  ['id', 'industry_name'])
                    ->pluck('industry_name', 'id') ?? [];

        return $industries;
    }

    public static function createDropDown($field) 
    {
        $options = [];
        $lessThanPrefix = config('constant.filterOptionPrefix')['lte'];
        $graterThanPrefix = config('constant.filterOptionPrefix')['gte'];
        for($step = $field['min']; $step <= $field['max']; $step = $step + $field['step']) {
            if($step == $field['min']) {
                $index = ' <= '.$step;
                $options[$lessThanPrefix . '-' . $step] = $index;
            } else if($step == $field['max']) {
                $index = ' >= '.$step;
                $options[$graterThanPrefix . '-' . $step] = $index;
            } else {
                $prevStep = $step - $field['step'];
                $index = $prevStep . ' - ' . $step;
                // $options[] = $prevStep . ' - ' . $step;
                $options[$index] = $index;
            }
            
        }
        return $options;
        dd($options);
    }

}
