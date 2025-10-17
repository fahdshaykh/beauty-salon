<?php

namespace App\Imports;

use App\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Customer([
            'first_name'      =>  $row[0],
            'last_name'     =>  $row[1],
            'primary_phone'      =>  $row[2],
            'secondary_phone'     =>  $row[3],
            'email'      =>  $row[4],
            'zip'     =>  $row[5],
            'address'      =>  $row[6],
            'referred_by'     =>  $row[7],
        ]);
    }
}
