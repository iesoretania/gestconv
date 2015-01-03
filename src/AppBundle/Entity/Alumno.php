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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alumno",
 *              uniqueConstraints={
 *                  @ORM\UniqueConstraint(columns={"nie"})
 *              }
 *          )
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
     * @ORM\Column(type="string")
     */
    protected $nombre;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $apellido1;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $apellido2;
    
    /**
     * @ORM\ManyToOne(targetEntity="Grupo")
     * @var Grupo
     */
    protected $grupo;
    
    /**
     * @ORM\Column(type="string", length=10)
     * @var string
     */
    protected $nie;

    /**
     * @ORM\OneToMany(targetEntity="Parte", mappedBy="alumno")
     */
    protected $partes = null;
    
    public function __construct() {
        $this->partes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return string
     */
    public function getNie()
    {
        return $this->nie;
    }
    
    /**
     *
     * @param string $valor
     * @return Alumno
     */
    public function setNie($valor)
    {
        $this->nie = $valor;

        return $this;
    }
    
    /**
     *
     * @return Grupo
     */
    public function getGrupo()
    {
        return $this->grupo;
    }
    
    /**
     *
     * @param Grupo $grupo
     * @return Alumno
     */
    public function setGrupo($grupo)
    {
        $grupo->incorporarAlumno($this);

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
     * @return Alumno
     */
    public function setNombre($valor)
    {
        $this->nombre = $valor;

        return $this;
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
     * @return Collection
     */
    public function getPartes()
    {
        return $this->partes;
    }
}
