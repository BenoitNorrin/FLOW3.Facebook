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
     * Settings
     * @param array $settings 
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * Return a Facebook object
     * @return \Facebook 
     */
    public function getFaceBookObject() {
        if (is_object($this->object) == false) {
            $this->object = new \Facebook(array(
                        'appId' => $this->settings['API']['appId'],
                        'secret' => $this->settings['API']['secret']
                    ));
        }
        return $this->object;
    }

}

?>
