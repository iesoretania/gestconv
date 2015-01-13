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

use AppBundle\Entity\Parte;
use AppBundle\Entity\Sancion;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Grupo;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Usuario
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
    protected $apellido1;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $apellido2;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $email;

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
     * @ORM\OneToMany(targetEntity="Grupo", mappedBy="tutor")
     * @var Collection
     */
    protected $tutorias = null;

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
        $this->tutorias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sanciones = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function getApellido1()
    {
        return $this->apellido1;
    }

    /**
     *
     * @param string $valor
     * @return Usuario
     */
    public function setApellido1($valor)
    {
        $this->apellido1 = $valor;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getApellido2()
    {
        return $this->apellido2;
    }

    /**
     *
     * @param string $valor
     * @return Usuario
     */
    public function setApellido2($valor)
    {
        $this->apellido2 = $valor;

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
    public function getApellidos()
    {
        return $this->apellido1 . (($this->apellido2) ? (' ' . $this->apellido2) : '');
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
     * Add tutorias
     *
     * @param Grupo $tutorias
     * @return Usuario
     */
    public function addTutoria(Grupo $tutorias)
    {
        $this->tutorias[] = $tutorias;

        return $this;
    }

    /**
     * Remove tutorias
     *
     * @param Grupo $tutorias
     */
    public function removeTutoria(Grupo $tutorias)
    {
        $this->tutorias->removeElement($tutorias);
    }

    /**
     * Get tutorias
     *
     * @return Collection
     */
    public function getTutorias()
    {
        return $this->tutorias;
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
}
