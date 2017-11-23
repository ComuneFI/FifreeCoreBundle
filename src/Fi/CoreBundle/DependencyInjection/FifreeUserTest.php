<?php

namespace Fi\CoreBundle\DependencyInjection;

use Symfony\Component\BrowserKit\Cookie;

class FifreeUserTest extends FifreeTest
{

    protected static function createAuthorizedClient($client)
    {
        $container = $client->getContainer();

        $session = $container->get('session');
        /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /* @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $username4test = $container->getParameter('usernoprivileges4test');
        $user = $userManager->findUserBy(array('username' => $username4test));
        $userManipulator = $container->get('fos_user.util.user_manipulator');
        if (!$user) {
            $adminPassword = $username4test;
            $adminUsername = $username4test;
            $adminEmail = $username4test . "@test.test";
            $isActive = true;
            $isSuperAdmin = false;
            $userManipulator->create($adminUsername, $adminPassword, $adminEmail, $isActive, $isSuperAdmin);
            $user = $userManager->findUserBy(array('username' => $username4test));
        }
        $loginManager->loginUser($firewallName, $user);

        /* save the login token into the session and put it in a cookie */
        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
