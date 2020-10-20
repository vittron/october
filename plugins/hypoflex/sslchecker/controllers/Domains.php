<?php namespace Hypoflex\Sslchecker\Controllers;

use Hypoflex\Sslchecker\Classes\SslChecker;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Lang;
use Backend\Classes\Controller;
use BackendMenu;
use Queue;
use Flash;
use DB;

class Domains extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Hypoflex.Sslchecker', 'ssl-checker', 'domains');
    }

    public function listExtendQuery($query)
    {
        $query->Where('active', 1);
    }

    public function onActiveNo()
    {
        /** Check if this is even set **/
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            /** Check if there's more than one record **/
            if (count($checkedIds) >= 1) :
                /** There's more than one record checked **/
                /** cycle through each id **/
                foreach ($checkedIds as $objectId) {
                    /** Check if there's an object actually related to this id **/
                    if (!$object = \Hypoflex\SslChecker\Models\Domains::find($objectId))
                        continue;
                    /** Screw this, next! **/
                    //Do delete the record
                    $object->update(['active' => 0]);
                }
            else:
                /** There's only one record checked **/
                \Flash::error('You have nothing selected, how did you even manage to come to this state!? STOP IT!');
            endif;

        }
        /** Return the new contents of the list, so the user will feel as if
         * they actually happened something
         **/
        return $this->listRefresh();
    }

    public function onSSLCheckHandler()
    {
        //Add the checker to the queue so it runs in the background */
        Queue::push('\hypoflex\sslchecker\classes\SslChecker');
        //Send a flash message to the user if the queue is added */
        Flash::success('Refreshing SSL information. This takes about 1 minute(s)');
        //Return the new contents of the list, so the user will feel as if something actually happend */
        return $this->listRefresh();
    }

}
