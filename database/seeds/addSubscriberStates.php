<?php

use Illuminate\Database\Seeder;
use App\Models\States;
class addSubscriberStates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendordoc = new States();
        $vendordoc->name = 'active';
        $vendordoc->save();

        $vendordoc = new States();
        $vendordoc->name = 'Unsubscribed';
        $vendordoc->save();

        $vendordoc = new States();
        $vendordoc->name = 'junk';
        $vendordoc->save();

        $vendordoc = new States();
        $vendordoc->name = 'bounced';
        $vendordoc->save();

        $vendordoc = new States();
        $vendordoc->name = 'unconfirmed';
        $vendordoc->save();
    }
}
