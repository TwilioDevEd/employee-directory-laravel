<?php

use App\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->delete();
        $json = File::get("database/data/employees.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Employee::create(
                array(
                    'full_name'    => $obj->fullName,
                    'email'        => $obj->email,
                    'phone_number' => $obj->phoneNumber,
                    'image_url'    => $obj->imageUrl
                )
            );
        }
    }
}
