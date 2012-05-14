<?php

namespace FLOW3\Facebook\Security\Authentication\Provider;

/* *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * An authentication provider that authenticates throw Facebook 
 */
class FacebookProvider extends \TYPO3\FLOW3\Security\Authentication\Provider\AbstractProvider {

    /**
     * @var \TYPO3\FLOW3\Security\AccountRepository
     * @FLOW3\Inject
     */
    protected $accountRepository;

    /**
     * @FLOW3\Inject
     * @var \TYPO3\FLOW3\Security\AccountFactory
     */
    protected $accountFactory;

    /**
     * @FLOW3\Inject
     * @var \FLOW3\Facebook\Service\FacebookService 
     */
    protected $facebookService;

    /**
     * @FLOW3\Inject
     * @var \TYPO3\Party\Domain\Repository\PartyRepository
     */
    protected $partyRepository;

    /**
     * Returns the classnames of the tokens this provider is responsible for.
     *
     * @return string The classname of the token this provider is responsible for
     */
    public function getTokenClassNames() {
        return array('\FLOW3\Facebook\Security\Authentication\Token\FacebookToken');
    }

    /**
     * Sets isAuthenticated to TRUE for all tokens.
     *
     * @param \TYPO3\FLOW3\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
     * @return void
     */
    public function authenticate(\TYPO3\FLOW3\Security\Authentication\TokenInterface $authenticationToken) {
        if (!($authenticationToken instanceof \FLOW3\Facebook\Security\Authentication\Token\FacebookToken)) {
            throw new \TYPO3\FLOW3\Security\Exception\UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1217339840);
        }

        // FacebookToken
        $credentials = $authenticationToken->getCredentials();
        
        if (is_array($credentials) && isset($credentials['email'])) {
            $account = $this->accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($credentials['email'], $this->name);
            
            // Account does not exist
            if (is_object($account) == false) {
                $account = $this->accountFactory->createAccountWithPassword($credentials['email'], md5(time()), array('UserLambda'), $this->name);
                $this->accountRepository->add($account);

                if ($credentials['last_name'] && $credentials['first_name']) {
                    $personEmail = new \TYPO3\Party\Domain\Model\ElectronicAddress();
                    $personEmail->setIdentifier($credentials['email']);
                    $personEmail->setType(\TYPO3\Party\Domain\Model\ElectronicAddress::TYPE_EMAIL);
                    $personEmail->setUsage(\TYPO3\Party\Domain\Model\ElectronicAddress::USAGE_HOME);

                    $person = new \TYPO3\Party\Domain\Model\Person();
                    $person->addElectronicAddress($personEmail);
                    $person->addAccount($account);
                    $person->setName(new \TYPO3\Party\Domain\Model\PersonName('', $credentials['first_name'], '', $credentials['last_name']));
                    $this->partyRepository->add($person);
                }
            }

            if (is_object($account)) {
                $authenticationToken->setAuthenticationStatus(\TYPO3\FLOW3\Security\Authentication\TokenInterface::AUTHENTICATION_SUCCESSFUL);
                $authenticationToken->setAccount($account);
            } elseif ($authenticationToken->getAuthenticationStatus() !== \TYPO3\FLOW3\Security\Authentication\TokenInterface::AUTHENTICATION_SUCCESSFUL) {
                $authenticationToken->setAuthenticationStatus(\TYPO3\FLOW3\Security\Authentication\TokenInterface::NO_CREDENTIALS_GIVEN);
            }
        } else {
            $authenticationToken->setAuthenticationStatus(\TYPO3\FLOW3\Security\Authentication\TokenInterface::WRONG_CREDENTIALS);
        }
    }

}

?>
