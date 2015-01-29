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

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Alumno
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @var int
     */
    protected $nie;

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
     * @ORM\Column(type="date")
     * @var \DateTime
     */
    protected $fechaNacimiento;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $tutor1;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $tutor2;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $telefono1;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $telefono2;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $notaTelefono1;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $notaTelefono2;

    /**
     * @ORM\ManyToOne(targetEntity="Grupo", inversedBy="alumnado")
     * @ORM\JoinColumn(nullable=false)
     * @var Grupo
     */
    protected $grupo;

    /**
     * @ORM\OneToMany(targetEntity="Parte", mappedBy="alumnos")
     * @var Collection
     */
    protected $partes = null;

    public function __construct() {
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
    public function getApellido1()
    {
        return $this->apellido1;
    }
    
    /**
     *
     * @param string $valor
     * @return Alumno
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
     * @return Alumno
     */
    public function setApellido2($valor)
    {
        $this->apellido2 = $valor;

        return $this;
    }
    
    /**
     *
     * @return int
     */
    public function getNie()
    {
        return $this->nie;
    }
    
    /**
     *
     * @param int $valor
     * @return Alumno
     */
    public function setNie($valor)
    {
        $this->nie = $valor;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getApellidos() . ', ' . $this->getNombre() . ' (' . $this->getGrupo()->getDescripcion() . ')';
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
     * @return Alumno
     */
    public function setNombre($valor)
    {
        $this->nombre = $valor;

        return $this;
    }

    /**
     * Set grupo
     *
     * @param Grupo $grupo
     * @return Alumno
     */
    public function setGrupo(Grupo $grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return Grupo
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set tutor1
     *
     * @param string $tutor1
     * @return Alumno
     */
    public function setTutor1($tutor1)
    {
        $this->tutor1 = $tutor1;

        return $this;
    }

    /**
     * Get tutor1
     *
     * @return string 
     */
    public function getTutor1()
    {
        return $this->tutor1;
    }

    /**
     * Set tutor2
     *
     * @param string $tutor2
     * @return Alumno
     */
    public function setTutor2($tutor2)
    {
        $this->tutor2 = $tutor2;

        return $this;
    }

    /**
     * Get tutor2
     *
     * @return string 
     */
    public function getTutor2()
    {
        return $this->tutor2;
    }

    /**
     * Set telefono1
     *
     * @param string $telefono1
     * @return Alumno
     */
    public function setTelefono1($telefono1)
    {
        $this->telefono1 = $telefono1;

        return $this;
    }

    /**
     * Get telefono1
     *
     * @return string 
     */
    public function getTelefono1()
    {
        return $this->telefono1;
    }

    /**
     * Set telefono2
     *
     * @param string $telefono2
     * @return Alumno
     */
    public function setTelefono2($telefono2)
    {
        $this->telefono2 = $telefono2;

        return $this;
    }

    /**
     * Get telefono2
     *
     * @return string 
     */
    public function getTelefono2()
    {
        return $this->telefono2;
    }

    /**
     * Set notaTelefono1
     *
     * @param string $notaTelefono1
     * @return Alumno
     */
    public function setNotaTelefono1($notaTelefono1)
    {
        $this->notaTelefono1 = $notaTelefono1;

        return $this;
    }

    /**
     * Get notaTelefono1
     *
     * @return string 
     */
    public function getNotaTelefono1()
    {
        return $this->notaTelefono1;
    }

    /**
     * Set notaTelefono2
     *
     * @param string $notaTelefono2
     * @return Alumno
     */
    public function setNotaTelefono2($notaTelefono2)
    {
        $this->notaTelefono2 = $notaTelefono2;

        return $this;
    }

    /**
     * Get notaTelefono2
     *
     * @return string 
     */
    public function getNotaTelefono2()
    {
        return $this->notaTelefono2;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Alumno
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Add partes
     *
     * @param Parte $partes
     * @return Alumno
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
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartes()
    {
        return $this->partes;
    }
}
