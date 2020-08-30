<?php

namespace Hypoflex\Sslchecker\Classes;

use DB;
use Flash;
use Hypoflex\Sslchecker\Classes\Constants;
use Hypoflex\Sslchecker\Models\Domains;
use Hypoflex\Sslchecker\Models\Settings;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;


Class SslChecker
{
    /** fire the job queue */
    public function fire($job){
        //call the checker
        $this->checkerInitialize();
        //deze the job if the checker is done
        $job->delete();

    }


    /** start of the script */
    public function checkerInitialize()
    {
        //Set the settings in case the user has not done this
        $settings = Settings::instance();
        if($settings->dataMethode === null) {
            $settings->dataMethode = 'all';
            $settings->save();
        }
        if($settings->jobRepeater === null) {
            $settings->jobRepeater = 'daily';
            $settings->save();
        }

        //initialize required classes and assign them to variables
        $now = new \DateTime();
        $constant = new Constants();
        $DomainModel = new Domains();
        //see what the user defined in the settings, and retrieve the requested data based on that.
        if($settings->dataMethode === 'single') {
            $domainRecords = $DomainModel::Where('active', 1)->get();
        }else{
            $domainRecords = $DomainModel::All();
        }
        $domains = [];
        $i = 1;
        $error = [];


        //for each  found domain, put them in a correct array order for later use
        foreach ($domainRecords as $domain) {
            array_push($domains, $domain->domain);
        }
        //count the amount of domains we have
        $domainCount = count($domains);

        //check if the list contains values, else show an error
        if ($domainCount === 0) {
            Flash::error(Lang::get('hypoflex.sslchecker::lang.ssl.empty_domain_list'));
            return;
        }
        //run a loop through all the domains to check the status
        foreach ($domains as $domain) {
            // initialize the certificate function
            $cert = $this->getCert($domain);
            $ipAddress = gethostbyname($domain);
            //display the domains, can be useful for listening to the queue in terminal
            var_dump($domain);

            if (!$cert) {
                //if the certificate comes back false assign the values for it and make error message appear.
                $validFrom = Lang::get('hypoflex.sslchecker::lang.ssl.unavailable');
                $validTo = Lang::get('hypoflex.sslchecker::lang.ssl.expired');
                $daysLeft = 0;
                $error[$i] = ['domain' => $domain];
            } else {
                //if ALL domains come back positive, flash the success message
//                Flash::success($constant::CHECK_COMPLETED_NO_ERROR_EN);
                //assign all certificate infromation to the variables, and show a success message
                $validFrom_time_t = new \DateTime("@" . $cert['validFrom_time_t']);
                $validTo_time_t = new \DateTime("@" . $cert['validTo_time_t']);
                $diff = $now->diff($validTo_time_t);
                $daysLeft = $diff->invert ? 0 : $diff->days;
                $validFrom = $validFrom_time_t->format($constant::FORMAT);
                $validTo = $validTo_time_t->format($constant::FORMAT);
            }
            //increase $i to count the errors if any come up
            $i++;

            //set the last check date on right now
            $last_check_date = Carbon::now();

            //return the variables to the database function to be queried
            $this->databaseUpdate($DomainModel, $domain, $validFrom, $validTo, $daysLeft, $ipAddress, $last_check_date);
        }
        if(count($error) > 0){
            //see if any domains returned a error on the check, if so message this to the user
            Flash::error(Lang::get('hypoflex.sslchecker::lang.ssl.check_completed').count($error).Lang::get('hypoflex.sslchecker::lang.ssl.check_with_x_errors'));
        }else{
            //if everything is alright, show a success message
            Flash::success(Lang::get('hypoflex.sslchecker::lang.ssl.check_completed_no_error'));
        }
        //return the flash message
        return;
    }

    /**
     * @param $domain
     * @return array
     */
    public function getCert($domain)
    {
        //Creates a stream context which returns a resource object
        $g = stream_context_create(array("ssl" => array("capture_peer_cert" => true)));
        //turn off error handling - this is needed for domains where the SSL is expired, this will otherwise cause a PHP error
        set_error_handler(function () {return true;});
        //open a socket to obtain the required SSL information from the given domain, with a 30 second timeout, use the resource object
        $r = stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $g);
        //retrieve the information from the socket
        $cont = stream_context_get_params($r);
        //restore the error handler to its original state
        restore_error_handler();
        //return the information of the certificate in array format
        return openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);
    }

    //update the database with the certificate information

    /**
     * @param $DomainModel
     * @param $domain
     * @param $validFrom
     * @param $validTo
     * @param $daysLeft
     * @param $ipAddress
     * @param $last_check_date
     */
    public function databaseUpdate($DomainModel, $domain, $validFrom, $validTo, $daysLeft, $ipAddress, $last_check_date){
        //update the database with the new received information
        Db::table($DomainModel->table)
            ->where('domain', $domain)
            ->update([
                'valid_from' => $validFrom,
                'valid_to' => $validTo,
                'diff' => $daysLeft,
                'ipaddress' => $ipAddress,
                'last_check_date' => $last_check_date,
            ]);
    }
}