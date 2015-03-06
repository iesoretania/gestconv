<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015: Luis Ramón López López

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

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * @ORM\Entity
 * @ORM\Table
 */
class Usuario implements AdvancedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $nombreUsuario;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $apellidos;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @var boolean
     */
    protected $notificaciones;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    protected $esAdministrador;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    protected $esRevisor;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    protected $esDirectivo;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @var boolean
     */
    protected $estaActivo;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    protected $estaBloqueado;

    /**
     * @ORM\ManyToOne(targetEntity="Grupo", inversedBy="tutores")
     * @var Grupo
     */
    protected $tutoria = null;

    /**
     * @ORM\OneToMany(targetEntity="Parte", mappedBy="usuario")
     * @var Collection
     */
    protected $partes = null;

    /**
     * @ORM\OneToMany(targetEntity="Sancion", mappedBy="usuario")
     * @var Collection
     */
    protected $sanciones = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->partes = new ArrayCollection();
        $this->sanciones = new ArrayCollection();
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     *
     * @param string $valor
     * @return Usuario
     */
    public function setApellidos($valor)
    {
        $this->apellidos = $valor;

        return $this;
    }


    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param string $valor
     * @return Usuario
     */
    public function setEmail($valor)
    {
        $this->email = $valor;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getApellidos() . ', ' . $this->getNombre();
    }

    /**
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     *
     * @param string $valor
     * @return Usuario
     */
    public function setNombre($valor)
    {
        $this->nombre = $valor;

        return $this;
    }

    /**
     * Get tutoria
     *
     * @return Grupo
     */
    public function getTutoria()
    {
        return $this->tutoria;
    }

    /**
     *
     * @param Grupo $tutoria
     * @return Usuario
     */
    public function setTutoria($tutoria)
    {
        $this->tutoria = $tutoria;

        return $this;
    }

    /**
     * Add partes
     *
     * @param Parte $partes
     * @return Usuario
     */
    public function addParte(Parte $partes)
    {
        $this->partes[] = $partes;

        return $this;
    }

    /**
     * Remove partes
     *
     * @param Parte $partes
     */
    public function removeParte(Parte $partes)
    {
        $this->partes->removeElement($partes);
    }

    /**
     * Get partes
     *
     * @return Collection
     */
    public function getPartes()
    {
        return $this->partes;
    }

    /**
     * Get notificaciones
     *
     * @return boolean
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }

    /**
     * Set notificaciones
     *
     * @param boolean $notificaciones
     * @return Usuario
     */
    public function setNotificaciones($notificaciones)
    {
        $this->notificaciones = $notificaciones;

        return $this;
    }

    /**
     * Get esAdministrador
     *
     * @return boolean
     */
    public function getEsAdministrador()
    {
        return $this->esAdministrador;
    }

    /**
     * Set esAdministrador
     *
     * @param boolean $esAdministrador
     * @return Usuario
     */
    public function setEsAdministrador($esAdministrador)
    {
        $this->esAdministrador = $esAdministrador;

        return $this;
    }

    /**
     * Get esRevisor
     *
     * @return boolean
     */
    public function getEsRevisor()
    {
        return $this->esRevisor;
    }

    /**
     * Set esRevisor
     *
     * @param boolean $esRevisor
     * @return Usuario
     */
    public function setEsRevisor($esRevisor)
    {
        $this->esRevisor = $esRevisor;

        return $this;
    }

    /**
     * Get esDirectivo
     *
     * @return boolean
     */
    public function getEsDirectivo()
    {
        return $this->esDirectivo;
    }

    /**
     * Set esAdministrador
     *
     * @param boolean $esDirectivo
     * @return Usuario
     */
    public function setEsDirectivo($esDirectivo)
    {
        $this->esDirectivo = $esDirectivo;

        return $this;
    }

    /**
     * Add sanciones
     *
     * @param Sancion $sanciones
     * @return Usuario
     */
    public function addSancion(Sancion $sanciones)
    {
        $this->sanciones[] = $sanciones;

        return $this;
    }

    /**
     * Remove sanciones
     *
     * @param Sancion $sanciones
     */
    public function removeSancion(Sancion $sanciones)
    {
        $this->sanciones->removeElement($sanciones);
    }

    /**
     * Get sanciones
     *
     * @return Collection
     */
    public function getSanciones()
    {
        return $this->sanciones;
    }

    /**
     * Set nombreUsuario
     *
     * @param string $nombreUsuario
     * @return Usuario
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    /**
     * Get nombreUsuario
     *
     * @return string
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set estaActivo
     *
     * @param boolean $estaActivo
     * @return Usuario
     */
    public function setEstaActivo($estaActivo)
    {
        $this->estaActivo = $estaActivo;

        return $this;
    }

    /**
     * Get estaActivo
     *
     * @return boolean
     */
    public function getEstaActivo()
    {
        return $this->estaActivo;
    }

    /**
     * Set estaBloqueado
     *
     * @param boolean $estaBloqueado
     * @return Usuario
     */
    public function setEstaBloqueado($estaBloqueado)
    {
        $this->estaBloqueado = $estaBloqueado;

        return $this;
    }

    /**
     * Get estaBloqueado
     *
     * @return boolean
     */
    public function getEstaBloqueado()
    {
        return $this->estaBloqueado;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return (!$this->getEstaBloqueado());
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->getEstaActivo();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        // realmente new Role() no es necesario, pero para que el valor
        // devuelto se corresponda con la anotación lo hacemos así
        $roles = [new Role('ROLE_USUARIO')];

        if ($this->getEsAdministrador()) {
            $roles[] = new Role('ROLE_ADMIN');
        }

        if ($this->getEsRevisor()) {
            $roles[] = new Role('ROLE_REVISOR');
        }

        if ($this->getEsDirectivo()) {
            $roles[] = new Role('ROLE_DIRECTIVO');
        }

        if ($this->getTutoria()) {
            $roles[] = new Role('ROLE_TUTOR');
        }

        return $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getNombreUsuario();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }
}
