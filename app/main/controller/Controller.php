<?php
/**
 * Created by PhpStorm.
 * User: exodus4d
 * Date: 08.02.15
 * Time: 23:48
 */

namespace Controller;

class Controller {

    protected $f3;
    private $template;

    /**
     * @param mixed $template
     */
    public function setTemplate($template){
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getTemplate(){
        return $this->template;
    }

    function __construct(){

        $f3 = \Base::instance();
        $this->f3 = $f3;

        // init DB
        $this->setDB('PF');
    }

    /**
     * event handler
     */
    function beforeroute() {

    }

    /**
     * event handler
     */
    function afterroute() {
        if($this->template){
            echo \Template::instance()->render( $this->template );

        }
    }

    /**
     * set/change DB connection
     * @param $type
     */
    protected function setDB($type){

        if($type === 'CCP'){
            // CCP DB
            $db = new \DB\SQL(
                $this->f3->get('DB_CCP_DNS') . $this->f3->get('DB_CCP_NAME'),
                $this->f3->get('DB_CCP_USER'),
                $this->f3->get('DB_CCP_PASS')
            );
        }else{
            // Pathfinder DB
            $db = new \DB\SQL(
                $this->f3->get('DB_DNS') . $this->f3->get('DB_NAME'),
                $this->f3->get('DB_USER'),
                $this->f3->get('DB_PASS')
            );
        }
        $this->f3->set('DB', $db);

        // set DB timezone to UTC +00:00 (eve server time)
        $this->f3->get('DB')->exec('SET @@session.time_zone = "+00:00";');
    }


    public function showLogin(){
        $this->setTemplate('templates/view/login.html');
    }

    /**
     * check weather the page is IGB trusted or not
     * @return mixed
     */
    static function isIGBTrusted(){

        $igbHeaderData = self::getIGBHeaderData();

        return $igbHeaderData->trusted;
    }

    /**
     * extract all eve IGB specific header data
     * @return object
     */
    static function getIGBHeaderData(){
        $data = (object) [];
        $data->trusted = false;
        $data->values = [];
        $headerData = apache_request_headers();

        foreach($headerData as $key => $value){
            if (strpos($key, 'EVE_') === 0) {
                $key = str_replace('EVE_', '', $key);
                $key = strtolower($key);

                if (
                    $key === 'trusted' &&
                    $value === 'Yes'
                ) {
                    $data->trusted = true;
                }

                $data->values[$key] = $value;
            }
        }

        return $data;
    }

} 