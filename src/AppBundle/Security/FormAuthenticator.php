<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015-2017: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Security;

use AppBundle\Entity\Usuario;
use AppBundle\Service\SenecaAuthenticatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FormAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SenecaAuthenticatorService
     */
    private $senecaAuthenticator;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * Constructor
     */
    public function __construct(RouterInterface $router, UserPasswordEncoderInterface $encoder, SenecaAuthenticatorService $senecaAuthenticator, ManagerRegistry $managerRegistry) {
        $this->router = $router;
        $this->encoder = $encoder;
        $this->senecaAuthenticator = $senecaAuthenticator;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $username = $request->request->get('_username');
        $session = $request->getSession();

        if ($request->attributes->get('_route') !== 'usuario_comprobar' || !$request->isMethod('POST') || !$session) {
            return null;
        }

        $session->set(Security::LAST_USERNAME, $username);

        return array(
            'username' => $username,
            'password' => $request->request->get('_password'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['username']);
        } catch (UsernameNotFoundException $e) {
            throw new BadCredentialsException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user instanceof Usuario) {
            throw new AuthenticationServiceException();
        }

        $plainPassword = $credentials['password'];

        // ¿Comprobación de contraseña desde Séneca?
        if ($user->getEsExterno()) {
            $result = $this->senecaAuthenticator->checkUserCredentials($user->getUsername(), $plainPassword);

            if (SenecaAuthenticatorService::STATUS_USER_AUTHENTICATED === $result) {
                // contraseña correcta, actualizar en local por si perdemos la conectividad
                if (false === $this->encoder->isPasswordValid($user, $plainPassword)) {
                    $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
                    $em = $this->managerRegistry->getManagerForClass('AppBundle\Entity\Usuario');
                    if ($em) {
                        $em->flush();
                    }
                }
                return true;
            }

            if (SenecaAuthenticatorService::STATUS_WRONG_USER_OR_PASSWORD === $result) {
                return false;
            }

            if (SenecaAuthenticatorService::STATUS_USER_BLOCKED === $result) {
                throw new \Symfony\Component\Security\Core\Exception\LockedException();
            }

            // si estamos aquí es que no se ha podido contactar con Séneca, intentar en local
        }

        // comprobación local
        if (false === $this->encoder->isPasswordValid($user, $plainPassword)) {
            throw new BadCredentialsException();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path');
        if (!$targetPath) {
            $targetPath = $this->router->generate('portada');
        }
        return new RedirectResponse($targetPath);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('usuario_entrar');
        return new RedirectResponse($url);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('usuario_entrar');
        return new RedirectResponse($url);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
