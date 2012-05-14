<?php

namespace FLOW3\Facebook\Service;
use TYPO3\FLOW3\Annotations as FLOW3;

require_once FLOW3_PATH_PACKAGES . 'Application/FLOW3.Facebook/Resources/Private/PHP/facebook-sdk/facebook.php';

/**
 * Service Facebook
 *
 * @author Benoit NORRIN <benoit@norrin.fr>
 * @FLOW3\Scope("prototype")
 */
class FacebookService {

    /**
     * @var array
     */
    protected $options;

    /**
     *
     * @var \Facebook 
     */
    protected $object;

    /**
     * Return a Facebook object
     * @return \Facebook 
     */
    public function getFaceBookObject() {
        if (is_object($this->object) == false) {
            $this->object = new \Facebook(array(
                        //'appId' => $this->options['facebookAppId'],
                        //'secret' => $this->options['facebookSecretKey'],
                        'appId' => '292197434154783',
                        'secret' => '460627b183dba87bf910f7d21d4e1e23'
                    ));
        }
        return $this->object;
    }

}

?>
