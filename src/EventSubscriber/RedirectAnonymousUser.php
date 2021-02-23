<?php

/**
 * @file
 * Contains Drupal\drupal_redirect_user_event_subscriber\EventSubscriber\RedirectAnonymousUser
 */

namespace Drupal\drupal_redirect_user_event_subscriber\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousUser implements EventSubscriberInterface {

  public function __construct() {
    $this->account = \Drupal::currentUser();
  }

  public function checkAuthStatus(GetResponseEvent $event) {
    // Make sure the request is not via cli and not for any page you want accessible for anonymous users.
    // Any other logic to manage user access could be added here.
    if (php_sapi_name() != 'cli' && $this->account->isAnonymous() &&
      \Drupal::routeMatch()->getRouteName() != 'samlauth.saml_controller_login'
      && \Drupal::routeMatch()->getRouteName() != 'samlauth.saml_controller_acs') {

        // Redirect user to SAML login
        $response = new RedirectResponse('/samllogin', 301);
        $response->send();
        exit;
    }
  }

  public static function getSubscribedEvents() {
    // In this example I am using the EXCEPTION KernerEvents constant
    $events[KernelEvents::REQUEST][] = array('checkAuthStatus');
    return $events;
  }

}