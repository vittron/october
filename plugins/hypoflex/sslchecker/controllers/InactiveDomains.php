<?php namespace Hypoflex\Sslchecker\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Hypoflex\Sslchecker\Classes\SslChecker;
use October\Rain\Exception\ApplicationException;
use Illuminate\Support\Facades\Lang;

class InactiveDomains extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Hypoflex.Sslchecker', 'ssl-checker', 'inactive-domains');
    }

    public function listExtendQuery($query)
    {
        $query->Where('active', 0);
    }

    public function onActiveYes()
    {
        /** Check if this is even set **/
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            /** Check if there's more than one record **/
            if(count($checkedIds) >= 1) :
                /** There's more than one record checked **/
                /** cycle through each id **/
                foreach ($checkedIds as $objectId) {
                    /** Check if there's an object actually related to this id **/
                    if (!$object = \Hypoflex\SslChecker\Models\Domains::find($objectId))
                        continue;  /** Screw this, next! **/
                    //Do delete the record
                    $object->update(['active' => 1]);
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
        $sslCheckerModel = new SslChecker();
        $sslCheckerModel->checkerInitialize();
        return $this->listRefresh();
    }
}
