<?php namespace Iocare\Letsencrypt\Controllers;

use Url;
use Lang;
use Flash;
use Event;
use Config;
use Request;
use Response;
use Exception;
use BackendMenu;
use Backend\Classes\Controller;

use Iocare\Letsencrypt\Classes\Lescript as LE;
class Settings extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController','Backend\Behaviors\ReorderController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {

        parent::__construct();
        BackendMenu::setContext('Iocare.Letsencrypt', 'main-menu-item');
    }
    public function index()
    {

        $this->bodyClass = 'compact-container';
        $this->pageTitle = 'indikator.devtools::lang.editor.menu_label';
        $this->pageTitleTemplate = '%s '.trans($this->pageTitle);
		$this->asExtension('ListController')->index();
    }
public function jamgaya($id)
    {

        echo "jamGya".$id;
    }
    public function onCreateCert($recordId, $context = null)
    {
        $model = $this->formFindModelObject($recordId);
        //$model->attemptActivation($model->activation_code);
		// Always use UTC
		if(!defined("PHP_VERSION_ID") || PHP_VERSION_ID < 50300 || !extension_loaded('openssl') || !extension_loaded('curl')) {
			//die("You need at least PHP 5.3.0 with OpenSSL and curl extension\n");
			Flash::success('You need at least PHP 5.3.0 with OpenSSL and curl extension');
			return;
		}
		date_default_timezone_set("UTC");

		$needsgen = false;
		$certfile = "$model->certlocation/cert.pem";

		// Make sure our cert location exists
		if (!is_dir($model->certlocation)) {
			// Make sure nothing is already there.
			if (file_exists($model->certlocation)) {
				unlink($model->certlocation);
			}
			mkdir ($model->certlocation);
		}

		if (!file_exists($certfile)) {
			// We don't have a cert, so we need to request one.
			//echo "Needs New SSL<br>";
			$needsgen = true;
		} else {
			// We DO have a certificate.
			$certdata = openssl_x509_parse(file_get_contents($certfile));
			//echo "SSL Exists for domain: <b>".$d."</b><br>";
			// If it expires in less than a month, we want to renew it.
			$renewafter = $certdata['validTo_time_t']-(86400*30);
			if (time() > $renewafter) {
				// Less than a month left, we need to renew.
				$needsgen = true;
				//echo "Needs Renewal<br>";
			}else{
				Flash::success('Certificate already present at at '.$model->certlocation.' and its up to date!');
			}
		}
		if ($needsgen) {
			try {
				$le = new LE($model->certlocation, $model->webroot);
				$le->contact = array('mailto:'.$model->email); // optional
				$domain = explode(",", $model->domain);
		
				$le->initAccount();
				$le->signDomains($domain);
				Flash::success('Certificate generated at '.$model->webroot.' ');
				} catch (\Exception $e) {
					//$logger->error($e->getMessage());
					//$logger->error($e->getTraceAsString());
					// Exit with an error code, something went wrong.
					Flash::success('Error! '.$e->getMessage().', '.$e->getTraceAsString());
				}
		}
	}

}